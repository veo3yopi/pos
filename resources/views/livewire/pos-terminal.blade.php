<div class="grid grid-cols-1 items-stretch gap-6 xl:grid-cols-[1.25fr_0.9fr_0.6fr]">
    <section class="flex flex-col gap-4 xl:h-[calc(100vh-160px)]">
        <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 shadow-lg shadow-slate-950/40">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-800 text-slate-200">🔍</div>
                <div class="flex-1">
                    <label class="text-[11px] uppercase tracking-wide text-slate-400">Cari Produk / SKU</label>
                    <input
                        type="text"
                        x-data
                        x-init="$nextTick(() => $refs.search.focus())"
                        x-ref="search"
                        wire:model.debounce.300ms="search"
                        wire:keydown.enter.prevent="addFirstMatch"
                        placeholder="Ketik nama, SKU, atau barcode"
                        class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:border-amber-400 focus:ring-1 focus:ring-amber-400"
                    />
                </div>
            </div>
        </div>

        @include('pos.partials.product-list', ['variants' => $variants])
    </section>

    <section class="xl:h-[calc(100vh-160px)]">
        @include('pos.partials.cart')
    </section>

    <section class="xl:h-[calc(100vh-160px)]">
        @include('pos.partials.payment-panel')
    </section>
</div>
