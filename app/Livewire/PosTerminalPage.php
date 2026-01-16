<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\ProductVariant;
use App\Services\PosCheckoutService;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Livewire\Component;

class PosTerminalPage extends Component
{
    public string $search = '';
    public ?int $categoryId = null;
    public array $cart = [];
    public string $paymentMethod = 'cash';
    public string $cashReceived = '';
    public float $discountTotal = 0;
    public bool $processing = false;
    public bool $showCheckout = false;
    public ?string $customerName = null;

    public function render()
    {
        return view('livewire.pos-terminal-page', [
            'variants' => $this->searchVariants(),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function addFirstMatch(): void
    {
        // Disabled: search should only filter list, not auto-add to cart.
    }

    public function addVariant(int $variantId): void
    {
        $variant = $this->findVariant($variantId);

        if (! $variant) {
            $this->toast('Produk tidak ditemukan.', 'error');
            return;
        }

        $stockQty = (int) ($variant->stock?->qty ?? 0);

        if ($stockQty <= 0) {
            $this->toast('Stok habis.', 'error');
            return;
        }

        $existing = Arr::get($this->cart, $variantId);

        if ($existing) {
            $newQty = $existing['qty'] + 1;
            if ($newQty > $stockQty) {
                $this->toast('Qty melebihi stok.', 'error');
                return;
            }

            $this->cart[$variantId]['qty'] = $newQty;
            $this->recalculateItem($variantId);
            $this->toast('Produk ditambahkan.', 'success');
            return;
        }

        $this->cart[$variantId] = [
            'variant_id' => $variant->id,
            'sku' => $variant->sku,
            'name' => $variant->product->name,
            'size' => $variant->size->name,
            'color' => $variant->color->name,
            'price' => (float) $variant->price,
            'qty' => 1,
            'discount' => 0,
            'stock' => $stockQty,
            'subtotal' => (float) $variant->price,
        ];

        $this->toast('Produk ditambahkan.', 'success');
    }

    public function incrementQty(int $variantId): void
    {
        if (! isset($this->cart[$variantId])) {
            return;
        }

        $item = $this->cart[$variantId];

        if ($item['qty'] + 1 > $item['stock']) {
            $this->toast('Qty melebihi stok.', 'error');
            return;
        }

        $this->cart[$variantId]['qty']++;
        $this->recalculateItem($variantId);
    }

    public function decrementQty(int $variantId): void
    {
        if (! isset($this->cart[$variantId])) {
            return;
        }

        if ($this->cart[$variantId]['qty'] <= 1) {
            unset($this->cart[$variantId]);
            return;
        }

        $this->cart[$variantId]['qty']--;
        $this->recalculateItem($variantId);
    }

    public function updateQty(int $variantId, int $qty): void
    {
        if (! isset($this->cart[$variantId])) {
            return;
        }

        $qty = max(1, $qty);
        $qty = min($qty, $this->cart[$variantId]['stock']);

        $this->cart[$variantId]['qty'] = $qty;
        $this->recalculateItem($variantId);
    }

    public function updateItemDiscount(int $variantId, float $discount): void
    {
        if (! isset($this->cart[$variantId])) {
            return;
        }

        $discount = max(0, $discount);
        $this->cart[$variantId]['discount'] = $discount;
        $this->recalculateItem($variantId);
    }

    public function removeItem(int $variantId): void
    {
        unset($this->cart[$variantId]);
    }

    public function openCheckout(): void
    {
        if (empty($this->cart)) {
            $this->toast('Keranjang kosong.', 'error');
            return;
        }

        $this->showCheckout = true;
    }

    public function closeCheckout(): void
    {
        $this->showCheckout = false;
    }

    public function checkout(): void
    {
        if ($this->processing) {
            return;
        }

        if (empty($this->cart)) {
            $this->toast('Keranjang kosong.', 'error');
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->cashReceived === '') {
            $this->toast('Masukkan uang bayar.', 'error');
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->cashReceivedNumber < $this->grandTotal) {
            $this->toast('Uang bayar kurang.', 'error');
            return;
        }

        $this->processing = true;

        try {
            $service = app(PosCheckoutService::class);
            $result = $service->checkout([
                'items' => array_values($this->cart),
                'discount_total' => $this->discountTotal,
                'payment_method' => $this->paymentMethod,
                'cash_received' => $this->cashReceivedNumber,
                'customer_name' => $this->customerName,
            ]);

            if (! $result['success']) {
                $this->toast($result['message'] ?? 'Checkout gagal.', 'error');
                return;
            }

            $this->reset(['cart', 'search', 'cashReceived', 'discountTotal', 'customerName', 'showCheckout']);
            $this->toast('Transaksi berhasil.', 'success');

            if (! empty($result['receipt'])) {
                $this->dispatch('show-receipt', receipt: $result['receipt']);
            }
        } catch (\Throwable $e) {
            $this->toast('Checkout gagal: ' . $e->getMessage(), 'error');
        } finally {
            $this->processing = false;
        }
    }

    public function getTotalItemsProperty(): int
    {
        return collect($this->cart)->sum('qty');
    }

    public function getItemsSubtotalProperty(): float
    {
        return (float) collect($this->cart)->sum('subtotal');
    }

    public function getGrandTotalProperty(): float
    {
        return max(0, $this->itemsSubtotal - $this->discountTotal);
    }

    public function getChangeProperty(): float
    {
        if ($this->paymentMethod !== 'cash') {
            return 0;
        }

        if ($this->cashReceived === '') {
            return 0;
        }

        return max(0, $this->cashReceivedNumber - $this->grandTotal);
    }

    public function getCashReceivedNumberProperty(): float
    {
        return (float) preg_replace('/[^0-9.]/', '', $this->cashReceived);
    }

    private function findVariant(int $variantId): ?ProductVariant
    {
        return ProductVariant::query()
            ->with(['product', 'size', 'color', 'stock'])
            ->find($variantId);
    }

    private function searchVariants()
    {
        return ProductVariant::query()
            ->with(['product', 'size', 'color', 'stock', 'product.category'])
            ->when($this->categoryId, function ($query) {
                $query->whereHas('product', function ($subQuery) {
                    $subQuery->where('category_id', $this->categoryId);
                });
            })
            ->when($this->search, function ($query) {
                $search = trim($this->search);
                $query->where('sku', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('id', 'desc')
            ->limit(60)
            ->get();
    }

    private function recalculateItem(int $variantId): void
    {
        if (! isset($this->cart[$variantId])) {
            return;
        }

        $item = $this->cart[$variantId];
        $subtotal = ($item['price'] * $item['qty']) - $item['discount'];
        $this->cart[$variantId]['subtotal'] = max(0, $subtotal);
    }

    private function toast(string $message, string $type = 'info'): void
    {
        $notification = Notification::make()
            ->title($message);

        if ($type === 'success') {
            $notification->success();
        } elseif ($type === 'error') {
            $notification->danger();
        } else {
            $notification->info();
        }

        $notification->send();
    }
}
