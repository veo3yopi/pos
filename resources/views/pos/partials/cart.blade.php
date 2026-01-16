<div class="flex flex-col rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg shadow-slate-950/40 xl:h-full">
    <div class="flex items-center justify-between border-b border-slate-800 px-5 py-4">
        <div>
            <p class="text-base font-semibold">Keranjang</p>
            <p class="text-xs text-slate-400">Kelola qty dan diskon item</p>
        </div>
        <span class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-300">{{ $this->totalItems }} items</span>
    </div>

    <div class="flex-1 overflow-y-auto">
        @forelse ($cart as $item)
            <div class="border-b border-slate-800 px-5 py-4" wire:key="cart-{{ $item['variant_id'] }}" x-data="{ showDiscount: false }">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-sm font-semibold tracking-wide">{{ $item['name'] }}</p>
                        <p class="text-xs text-slate-400">{{ $item['size'] }} / {{ $item['color'] }}</p>
                        <p class="text-[11px] font-mono text-slate-500">SKU: {{ $item['sku'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Subtotal</p>
                        <p class="text-base font-semibold text-amber-300">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                        <button
                            type="button"
                            @click="showDiscount = !showDiscount"
                            class="mt-2 text-[11px] uppercase tracking-wide text-amber-200 hover:text-amber-100"
                        >
                            Diskon
                        </button>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-[1fr_auto] items-center gap-4">
                    <div>
                        <label class="text-[11px] uppercase tracking-wide text-slate-500">Qty</label>
                        <div class="mt-2 flex items-center gap-2">
                            <button
                                wire:click="decrementQty({{ $item['variant_id'] }})"
                                class="h-10 w-10 rounded-lg border border-slate-700 text-base"
                            >-</button>
                            <input
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                value="{{ $item['qty'] }}"
                                wire:change="updateQty({{ $item['variant_id'] }}, $event.target.value)"
                                class="w-16 rounded-lg border border-slate-700 bg-slate-950 px-2 py-2 text-center text-sm"
                            />
                            <button
                                wire:click="incrementQty({{ $item['variant_id'] }})"
                                @disabled($item['qty'] >= $item['stock'])
                                class="h-10 w-10 rounded-lg border border-slate-700 text-base disabled:opacity-40"
                            >+</button>
                            <span class="rounded-full border border-slate-700 px-2 py-1 text-[10px] text-slate-400">Stok {{ $item['stock'] }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] uppercase tracking-wide text-slate-500">Harga</p>
                        <p class="text-base font-semibold">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        <button
                            wire:click="removeItem({{ $item['variant_id'] }})"
                            class="mt-2 text-xs text-slate-400 hover:text-red-300"
                        >
                            Hapus
                        </button>
                    </div>
                </div>

                <div x-show="showDiscount" x-transition class="mt-4">
                    <label class="text-[11px] uppercase tracking-wide text-slate-500">Diskon item</label>
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        value="{{ $item['discount'] }}"
                        wire:change="updateItemDiscount({{ $item['variant_id'] }}, $event.target.value)"
                        class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm"
                    />
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-slate-400">
                <p class="text-base font-semibold">Keranjang kosong</p>
                <p class="mt-2 text-xs text-slate-500">Cari produk lalu tekan Enter untuk tambah cepat.</p>
            </div>
        @endforelse
    </div>
</div>
