<x-filament::page>
    <div class="mx-auto w-full max-w-3xl space-y-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">Receipt #{{ $this->record->id }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">{{ $this->record->created_at?->format('d M Y, H:i') }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Kasir: {{ $this->record->cashier?->name ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="window.print()"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:text-slate-200 dark:hover:bg-gray-900"
                >
                    Print
                </button>
                <a
                    href="{{ $this->getResourceUrl('index') }}"
                    class="rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-500"
                >
                    Kembali
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-950">
            <p class="text-base font-semibold text-gray-900 dark:text-white">Ringkasan Item</p>
            <div class="mt-4 divide-y divide-gray-200 dark:divide-white/10">
                @foreach ($this->record->items as $item)
                    @php
                        $imageUrl = $item->variant->product->getFirstMediaUrl('images');
                    @endphp
                    <div class="flex items-start justify-between py-3">
                        <div class="flex items-start gap-3">
                            <div class="h-12 w-12 overflow-hidden rounded-lg border border-gray-200 bg-gray-100 dark:border-white/10 dark:bg-gray-900">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $item->variant->product->name }}" class="h-full w-full object-cover" />
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->variant->product->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $item->variant->size->name }} / {{ $item->variant->color->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-slate-500">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-slate-200">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-950">
                <p class="text-base font-semibold text-gray-900 dark:text-white">Pembayaran</p>
                <div class="mt-3 space-y-2 text-sm text-gray-600 dark:text-slate-300">
                    @forelse ($this->record->payments as $payment)
                        <div class="flex items-center justify-between">
                            <span>{{ ucfirst($payment->method) }}</span>
                            <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-gray-400 dark:text-slate-500">Tidak ada data pembayaran.</div>
                    @endforelse
                </div>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-950">
                <p class="text-base font-semibold text-gray-900 dark:text-white">Total</p>
                <div class="mt-3 space-y-2 text-sm text-gray-600 dark:text-slate-300">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($this->record->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Diskon</span>
                        <span>Rp {{ number_format($this->record->discount_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-base font-semibold text-gray-900 dark:text-white">
                        <span>Grand Total</span>
                        <span>Rp {{ number_format($this->record->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
