<div class="flex flex-1 flex-col rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg shadow-slate-950/40">
    <div class="flex items-center justify-between border-b border-slate-800 px-5 py-4">
        <div>
            <p class="text-base font-semibold">Daftar Produk</p>
            <p class="text-xs text-slate-400">Pilih variant untuk menambah ke keranjang</p>
        </div>
        <span class="rounded-full border border-slate-700 px-3 py-1 text-xs text-slate-300">{{ $variants->count() }} items</span>
    </div>

    <div class="flex-1 max-h-[420px] overflow-x-auto overflow-y-auto p-5 lg:max-h-none lg:overflow-y-hidden">
        @if ($variants->isEmpty())
            <div class="py-12 text-center text-sm text-slate-400">
                Produk tidak ditemukan.
            </div>
        @else
            <div class="flex flex-col gap-4 lg:grid lg:min-w-[752px] lg:w-[752px] lg:auto-cols-[240px] lg:grid-flow-col lg:grid-rows-2">
                @foreach ($variants as $variant)
                    @php
                        $stockQty = (int) ($variant->stock?->qty ?? 0);
                        $stockLabel = $stockQty <= 0 ? 'Habis' : ($stockQty <= 3 ? 'Menipis' : 'Aman');
                        $stockClass = $stockQty <= 0
                            ? 'bg-red-500/15 text-red-200 border-red-500/40'
                            : ($stockQty <= 3 ? 'bg-amber-500/15 text-amber-200 border-amber-500/40' : 'bg-emerald-500/15 text-emerald-200 border-emerald-500/40');
                    @endphp
                    <div class="flex h-full flex-col justify-between rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                        <div class="space-y-2">
                            <p class="text-sm font-semibold tracking-wide">{{ $variant->product->name }}</p>
                            <p class="text-xs text-slate-400">{{ $variant->size->name }} / {{ $variant->color->name }}</p>
                            <p class="text-[11px] font-mono text-slate-500">SKU: {{ $variant->sku }}</p>
                        </div>
                        <div class="mt-4 space-y-3">
                            <p class="text-base font-semibold text-amber-300">Rp {{ number_format($variant->price, 0, ',', '.') }}</p>
                            <div class="flex items-center justify-between gap-2">
                                <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $stockClass }}">{{ $stockLabel }} ({{ $stockQty }})</span>
                                <button
                                    wire:click="addVariant({{ $variant->id }})"
                                    @disabled($stockQty <= 0)
                                    class="rounded-xl bg-amber-500 px-4 py-2 text-xs font-semibold text-slate-950 shadow-sm shadow-amber-500/30 hover:bg-amber-400 disabled:cursor-not-allowed disabled:bg-slate-800 disabled:text-slate-500"
                                >
                                    Tambah
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
