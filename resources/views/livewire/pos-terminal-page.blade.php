<div
    x-data="{ toast: { show: false, message: '', type: 'info' }, openCheckout: @entangle('showCheckout') }"
    @toast.window="toast = { show: true, message: $event.detail.message, type: $event.detail.type }; setTimeout(() => toast.show = false, 2500)"
    class="space-y-6"
>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">Kasir Point of Sale</p>
            <p class="text-sm text-gray-500 dark:text-slate-400">Proses transaksi cepat dan akurat</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-950">
                <div class="grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
                    <div>
                        <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-400">Cari Produk</label>
                        <input
                            type="text"
                            wire:model.live="search"
                            placeholder="Ketik nama produk..."
                            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-gray-950 dark:text-white dark:placeholder:text-slate-500"
                        />
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-400">Kategori</label>
                        <select
                            wire:model="categoryId"
                            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-gray-950 dark:text-white"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-950">
                <div class="flex items-center justify-between">
                    <p class="text-base font-semibold text-gray-900 dark:text-white">Pilih Produk</p>
                    <span class="text-xs text-gray-500 dark:text-slate-400">{{ $variants->count() }} items</span>
                </div>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($variants as $variant)
                        @php
                            $stockQty = (int) ($variant->stock?->qty ?? 0);
                            $stockClass = $stockQty <= 0
                                ? 'text-red-500 dark:text-red-300'
                                : ($stockQty <= 3 ? 'text-amber-600 dark:text-amber-300' : 'text-emerald-600 dark:text-emerald-300');
                        @endphp
                        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $variant->product->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $variant->size->name }} / {{ $variant->color->name }}</p>
                            <p class="mt-2 text-sm font-semibold text-primary-600 dark:text-primary-400">Rp {{ number_format($variant->price, 0, ',', '.') }}</p>
                            <div class="mt-2 flex items-center justify-between text-xs">
                                <span class="{{ $stockClass }}">Stok: {{ $stockQty }}</span>
                                <span class="text-gray-400 dark:text-slate-500">SKU: {{ $variant->sku }}</span>
                            </div>
                            <button
                                wire:click="addVariant({{ $variant->id }})"
                                @disabled($stockQty <= 0)
                                class="mt-4 w-full rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white hover:bg-primary-500 disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:bg-gray-800 dark:disabled:text-gray-500"
                            >
                                Tambah ke Keranjang
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full rounded-xl border border-gray-200 bg-white p-6 text-center text-sm text-gray-500 dark:border-white/10 dark:bg-gray-950/60 dark:text-slate-400">Produk tidak ditemukan.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-950">
                <div class="flex items-center justify-between">
                    <p class="text-base font-semibold text-gray-900 dark:text-white">Keranjang</p>
                    <span class="text-xs text-red-500 dark:text-red-400">{{ $this->totalItems ? '' : 'Kosongkan' }}</span>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($cart as $item)
                        <div class="rounded-xl border border-gray-200 bg-white px-3 py-3 shadow-sm dark:border-white/10 dark:bg-gray-900">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $item['size'] }} / {{ $item['color'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-slate-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="decrementQty({{ $item['variant_id'] }})" class="h-7 w-7 rounded bg-gray-200 text-xs text-gray-700 dark:bg-gray-800 dark:text-slate-200">-</button>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $item['qty'] }}</span>
                                    <button wire:click="incrementQty({{ $item['variant_id'] }})" class="h-7 w-7 rounded bg-gray-200 text-xs text-gray-700 dark:bg-gray-800 dark:text-slate-200">+</button>
                                    <button wire:click="removeItem({{ $item['variant_id'] }})" class="h-7 w-7 rounded bg-red-500/20 text-xs text-red-600 dark:text-red-300">x</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-gray-200 bg-white px-4 py-6 text-center text-xs text-gray-500 dark:border-white/10 dark:bg-gray-900 dark:text-slate-400">Keranjang kosong</div>
                    @endforelse
                </div>
                <div class="mt-4 flex items-center justify-between text-sm text-gray-600 dark:text-slate-300">
                    <span>Total:</span>
                    <span class="text-lg font-semibold text-primary-600 dark:text-primary-400">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                </div>
                <button
                    type="button"
                    @click="openCheckout = true"
                    class="mt-4 w-full rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white hover:bg-primary-500"
                >
                    Checkout ({{ $this->totalItems }})
                </button>
            </div>
        </div>
    </div>

    <div
        x-show="openCheckout"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 px-6"
    >
        <div class="w-full max-w-5xl rounded-3xl border border-gray-200 bg-white p-6 shadow-xl dark:border-white/10 dark:bg-gray-950">
            <div class="mb-5 flex items-center justify-between">
                <p class="text-xl font-semibold text-gray-900 dark:text-white">Checkout</p>
                <button @click="openCheckout = false" class="text-gray-400 hover:text-gray-700 dark:text-slate-400 dark:hover:text-slate-200">✕</button>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <p class="text-base font-semibold text-gray-900 dark:text-white">Informasi Pembayaran</p>

                    <div class="mt-4">
                        <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-400">Nama Customer (Opsional)</label>
                        <input
                            type="text"
                            wire:model.lazy="customerName"
                            placeholder="Masukkan nama customer..."
                            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 dark:border-white/10 dark:bg-gray-950 dark:text-white dark:placeholder:text-slate-500"
                        />
                    </div>

                    <div class="mt-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-400">Metode Pembayaran</p>
                        <div class="mt-2 space-y-2 text-sm text-gray-700 dark:text-slate-200">
                            <label class="flex items-center gap-2">
                                <input type="radio" wire:model="paymentMethod" value="cash" class="text-primary-600" />
                                Tunai
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="radio" wire:model="paymentMethod" value="transfer" class="text-primary-600" />
                                Transfer Bank
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-400">Uang Bayar</label>
                        <input
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            wire:model.lazy="cashReceived"
                            class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 dark:border-white/10 dark:bg-gray-950 dark:text-white"
                        />
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="openCheckout = false"
                            class="rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700 dark:border-white/10 dark:text-slate-200"
                        >
                            Kembali
                        </button>
                        <button
                            type="button"
                            wire:click="checkout"
                            wire:loading.attr="disabled"
                            class="rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-500 disabled:opacity-60"
                        >
                            Proses Transaksi
                        </button>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <p class="text-base font-semibold text-gray-900 dark:text-white">Ringkasan Pesanan</p>

                    <div class="mt-4 space-y-3">
                        @foreach ($cart as $item)
                            <div class="flex items-start justify-between border-b border-gray-200 pb-3 dark:border-white/10">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Ukuran: {{ $item['size'] }} / {{ $item['color'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-slate-500">{{ $item['qty'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-slate-200">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex items-center justify-between text-sm text-gray-600 dark:text-slate-300">
                        <span>Total Pembayaran:</span>
                        <span class="text-lg font-semibold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        x-show="toast.show"
        x-transition
        class="fixed bottom-6 right-6 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-lg dark:border-white/10 dark:bg-gray-950"
        :class="toast.type === 'error' ? 'border-red-500/50 text-red-300' : toast.type === 'success' ? 'border-emerald-500/50 text-emerald-300' : 'text-slate-200'"
    >
        <p x-text="toast.message"></p>
    </div>
</div>
