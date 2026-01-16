<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockLog;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosCheckoutService
{
    public function checkout(array $payload): array
    {
        $user = Auth::user();

        if (! $user) {
            return $this->fail('User tidak terautentikasi.');
        }

        $items = $payload['items'] ?? [];
        if (empty($items)) {
            return $this->fail('Keranjang kosong.');
        }

        $paymentMethod = $payload['payment_method'] ?? 'cash';
        if (! in_array($paymentMethod, ['cash', 'transfer'], true)) {
            return $this->fail('Metode pembayaran tidak valid.');
        }

        $discountTotal = (float) ($payload['discount_total'] ?? 0);
        $cashReceived = $payload['cash_received'] ?? null;

        try {
            $result = DB::transaction(function () use ($items, $user, $paymentMethod, $discountTotal, $cashReceived) {
                $lineItems = [];
                $subtotal = 0.0;

                foreach ($items as $item) {
                    $variantId = (int) ($item['variant_id'] ?? 0);
                    $qty = (int) ($item['qty'] ?? 0);
                    $itemDiscount = (float) ($item['discount'] ?? 0);

                    if ($variantId <= 0 || $qty <= 0) {
                        throw new \RuntimeException('Item tidak valid.');
                    }

                    $variant = ProductVariant::query()
                        ->with(['product', 'size', 'color'])
                        ->find($variantId);

                    if (! $variant) {
                        throw new \RuntimeException('Produk tidak ditemukan.');
                    }

                    $stock = Stock::query()
                        ->where('variant_id', $variantId)
                        ->lockForUpdate()
                        ->first();

                    $stockQty = (int) ($stock?->qty ?? 0);
                    if ($stockQty < $qty) {
                        throw new \RuntimeException('Stok tidak cukup untuk ' . $variant->sku);
                    }

                    $price = (float) $variant->price;
                    $maxDiscount = $price * $qty;
                    $itemDiscount = max(0, min($itemDiscount, $maxDiscount));
                    $lineSubtotal = max(0, ($price * $qty) - $itemDiscount);

                    $subtotal += $lineSubtotal;

                    $lineItems[] = [
                        'variant' => $variant,
                        'qty' => $qty,
                        'price' => $price,
                        'discount' => $itemDiscount,
                        'subtotal' => $lineSubtotal,
                        'stock' => $stock,
                    ];
                }

                $discountTotal = max(0, min($discountTotal, $subtotal));
                $grandTotal = max(0, $subtotal - $discountTotal);

                if ($paymentMethod === 'cash') {
                    if ($cashReceived === null) {
                        throw new \RuntimeException('Uang bayar belum diisi.');
                    }
                    $cashReceived = (float) $cashReceived;
                    if ($cashReceived < $grandTotal) {
                        throw new \RuntimeException('Uang bayar kurang.');
                    }
                }

                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'subtotal' => $subtotal,
                    'discount_total' => $discountTotal,
                    'grand_total' => $grandTotal,
                    'status' => 'paid',
                ]);

                foreach ($lineItems as $line) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'variant_id' => $line['variant']->id,
                        'qty' => $line['qty'],
                        'price' => $line['price'],
                        'discount' => $line['discount'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    $line['stock']->update([
                        'qty' => $line['stock']->qty - $line['qty'],
                    ]);

                    StockLog::create([
                        'variant_id' => $line['variant']->id,
                        'type' => 'out',
                        'qty' => $line['qty'],
                        'note' => 'POS Sale #' . $transaction->id,
                        'user_id' => $user->id,
                    ]);
                }

                Payment::create([
                    'transaction_id' => $transaction->id,
                    'method' => $paymentMethod,
                    'amount' => $grandTotal,
                    'note' => $paymentMethod === 'cash' ? 'Cash received: ' . (float) $cashReceived : null,
                ]);

                return [
                    'transaction' => $transaction,
                    'items' => $lineItems,
                    'grand_total' => $grandTotal,
                    'cash_received' => $cashReceived,
                ];
            });
        } catch (\Throwable $e) {
            return $this->fail($e->getMessage());
        }

        $change = 0.0;
        if ($paymentMethod === 'cash' && $result['cash_received'] !== null) {
            $change = max(0, (float) $result['cash_received'] - $result['grand_total']);
        }

        $receipt = [
            'transaction_id' => $result['transaction']->id,
            'cashier' => $user->name,
            'items' => collect($result['items'])->map(function ($line) {
                return [
                    'sku' => $line['variant']->sku,
                    'name' => $line['variant']->product->name,
                    'size' => $line['variant']->size->name,
                    'color' => $line['variant']->color->name,
                    'qty' => $line['qty'],
                    'price' => $line['price'],
                    'discount' => $line['discount'],
                    'subtotal' => $line['subtotal'],
                ];
            })->all(),
            'subtotal' => $result['transaction']->subtotal,
            'discount_total' => $result['transaction']->discount_total,
            'grand_total' => $result['transaction']->grand_total,
            'payment_method' => $paymentMethod,
            'cash_received' => $result['cash_received'],
            'change' => $change,
            'created_at' => $result['transaction']->created_at,
        ];

        return [
            'success' => true,
            'message' => 'Checkout berhasil.',
            'receipt' => $receipt,
        ];
    }

    private function fail(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'receipt' => null,
        ];
    }
}
