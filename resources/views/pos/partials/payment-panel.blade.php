<div class="flex flex-col rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg shadow-slate-950/40 xl:h-full">
    <div class="border-b border-slate-800 px-5 py-4">
        <p class="text-base font-semibold">Checkout</p>
        <p class="text-xs text-slate-400">Ringkasan transaksi</p>
    </div>

    <div class="flex-1 space-y-5 px-5 py-5">
        <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-4">
            <p class="text-xs text-slate-400">Total bayar</p>
            <p class="mt-2 text-3xl font-bold text-amber-300">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</p>
            <div class="mt-3 flex items-center justify-between text-xs text-slate-400">
                <span>Item: {{ $this->totalItems }}</span>
                <span>Subtotal: Rp {{ number_format($this->itemsSubtotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div>
            <label class="text-[11px] uppercase tracking-wide text-slate-500">Diskon total</label>
            <input
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                wire:model.lazy="discountTotal"
                class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm"
            />
        </div>

        <div>
            <label class="text-[11px] uppercase tracking-wide text-slate-500">Metode pembayaran</label>
            <div class="mt-2 grid grid-cols-2 gap-2">
                <button
                    type="button"
                    wire:click="$set('paymentMethod', 'cash')"
                    @class([
                        'rounded-xl border px-3 py-2 text-xs font-semibold',
                        'border-amber-400 text-amber-200' => $paymentMethod === 'cash',
                        'border-slate-700 text-slate-300' => $paymentMethod !== 'cash',
                    ])
                >
                    Cash
                </button>
                <button
                    type="button"
                    wire:click="$set('paymentMethod', 'transfer')"
                    @class([
                        'rounded-xl border px-3 py-2 text-xs font-semibold',
                        'border-amber-400 text-amber-200' => $paymentMethod === 'transfer',
                        'border-slate-700 text-slate-300' => $paymentMethod !== 'transfer',
                    ])
                >
                    Transfer
                </button>
            </div>
        </div>

        <div>
            <label class="text-[11px] uppercase tracking-wide text-slate-500">Uang bayar (cash)</label>
            <input
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                wire:model.lazy="cashReceived"
                @disabled($paymentMethod !== 'cash')
                class="mt-2 w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm disabled:opacity-40"
            />
        </div>

        <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-400">Kembalian</p>
                <p class="text-base font-semibold">Rp {{ number_format($this->change, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="mt-auto">
            <button
                type="button"
                x-on:click.prevent="if (confirm('Proses pembayaran sekarang?')) { $wire.checkout() }"
                wire:loading.attr="disabled"
                wire:target="checkout"
                class="w-full rounded-2xl bg-amber-500 px-4 py-4 text-sm font-bold uppercase tracking-wide text-slate-950 shadow-lg shadow-amber-500/30 hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="checkout">Proses Bayar</span>
                <span wire:loading wire:target="checkout">Memproses...</span>
            </button>
        </div>
    </div>
</div>
