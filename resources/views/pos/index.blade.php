<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Terminal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div
        x-data="{
            now: new Date(),
            toast: { show: false, message: '', type: 'info' },
        }"
        x-init="setInterval(() => now = new Date(), 1000)"
        @toast.window="toast = { show: true, message: $event.detail.message, type: $event.detail.type }; setTimeout(() => toast.show = false, 2500)"
        class="min-h-screen"
    >
        <header class="border-b border-slate-800 bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900">
            <div class="mx-auto flex w-full max-w-none items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-bold">RF</div>
                    <div>
                        <p class="text-sm text-slate-400">Retail Fashion</p>
                        <p class="text-lg font-semibold tracking-wide">POS Terminal</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Kasir</p>
                        <p class="text-sm font-medium">{{ auth()->user()->name ?? 'Guest' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Jam</p>
                        <p class="text-sm font-medium" x-text="now.toLocaleTimeString()"></p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg border border-amber-500/40 px-4 py-2 text-sm font-semibold text-amber-300 hover:bg-amber-500/10">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="mx-auto grid w-full max-w-none grid-cols-1 gap-6 px-6 py-6">
            @livewire('pos-terminal')
        </main>

        <div
            x-show="toast.show"
            x-transition
            class="fixed bottom-6 right-6 rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm shadow-lg"
            :class="toast.type === 'error' ? 'border-red-500/50 text-red-300' : toast.type === 'success' ? 'border-emerald-500/50 text-emerald-300' : 'text-slate-200'"
        >
            <p x-text="toast.message"></p>
        </div>
    </div>

    @livewireScripts
</body>
</html>
