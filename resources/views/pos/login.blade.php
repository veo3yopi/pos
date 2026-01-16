<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kasir Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.12),_transparent_40%)]">
        <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-6">
            <div class="grid w-full items-stretch gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 p-10 shadow-2xl shadow-slate-950/50 lg:flex lg:flex-col lg:justify-between">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500 text-slate-950 font-bold">RF</div>
                        <p class="mt-6 text-sm text-slate-400">Retail Fashion</p>
                        <h1 class="mt-2 text-3xl font-semibold tracking-wide">POS Kasir</h1>
                        <p class="mt-4 text-sm text-slate-400">Masuk untuk mulai transaksi cepat, aman, dan terkontrol.</p>
                    </div>
                    <div class="space-y-3 text-xs text-slate-400">
                        <p>Tips:</p>
                        <p>• Gunakan email kasir terdaftar</p>
                        <p>• Pastikan koneksi lokal stabil</p>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-800 bg-slate-900/70 p-8 shadow-2xl shadow-slate-950/50">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-950 font-bold">RF</div>
                        <div>
                            <p class="text-xs text-slate-400">Retail Fashion</p>
                            <p class="text-lg font-semibold">Login Kasir</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('pos.login.submit') }}" class="mt-8 space-y-5">
                        @csrf
                        <div>
                            <label class="text-xs uppercase tracking-wide text-slate-400">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:border-amber-400 focus:ring-1 focus:ring-amber-400"
                                placeholder="kasir@mail.com"
                            />
                            @error('email')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs uppercase tracking-wide text-slate-400">Password</label>
                            <input
                                type="password"
                                name="password"
                                required
                                class="mt-2 w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:border-amber-400 focus:ring-1 focus:ring-amber-400"
                                placeholder="Masukkan password"
                            />
                            @error('password')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-2xl bg-amber-500 px-4 py-3 text-sm font-bold uppercase tracking-wide text-slate-950 shadow-lg shadow-amber-500/30 hover:bg-amber-400"
                        >
                            Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
