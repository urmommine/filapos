<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FilaPOS') }} Game Store - Layanan Top Up Game Cepat & Otomatis 24 Jam</title>
    <meta name="description" content="Layanan Top Up Game Tercepat, Murah, dan Otomatis 24 Jam dengan Pembayaran QRIS BorderPay resmi FilaPOS.">
    <meta name="theme-color" content="#84cc16">

    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {!! ToastMagic::styles() !!}

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Clean Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-slate-50 text-slate-800 antialiased selection:bg-lime-200 selection:text-lime-900">

    <!-- Top Navigation Bar (FilaPOS Hero Brand Style) -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('topups') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-lime-500 text-slate-950 flex items-center justify-center shadow-md shadow-lime-500/20 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900">Fila<span class="text-lime-600">POS</span></span>
                    <span class="text-slate-300 font-light">/</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">PPOB</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-1 bg-slate-100/90 p-1 rounded-2xl border border-slate-200/90 text-xs font-bold">
                <a href="{{ route('topups') }}" 
                   class="px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('topups') ? 'bg-white text-slate-950 shadow-xs font-extrabold' : 'text-slate-600 hover:text-slate-950' }}">
                    <span class="material-symbols-outlined text-base {{ request()->routeIs('topups') ? 'text-lime-600' : 'text-slate-400' }}">shopping_bag</span>
                    <span>Order Produk</span>
                </a>
                <a href="{{ route('topups.check-username') }}" 
                   class="px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('topups.check-username') ? 'bg-white text-slate-950 shadow-xs font-extrabold' : 'text-slate-600 hover:text-slate-950' }}">
                    <span class="material-symbols-outlined text-base {{ request()->routeIs('topups.check-username') ? 'text-lime-600' : 'text-slate-400' }}">verified_user</span>
                    <span>Cek Username</span>
                </a>
                @auth
                    <a href="{{ route('topups.account') }}" 
                       class="px-3.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('topups.account') ? 'bg-white text-slate-950 shadow-xs font-extrabold' : 'text-slate-600 hover:text-slate-950' }}">
                        <span class="material-symbols-outlined text-base {{ request()->routeIs('topups.account') ? 'text-lime-600' : 'text-slate-400' }}">account_balance_wallet</span>
                        <span>Akun & Saldo</span>
                    </a>
                @endauth
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-2.5">
                @auth
                    <!-- Member Saldo Badge (Sync Otomatis) -->
                    <a href="{{ route('topups.account') }}" 
                       x-data="{ navBalance: 'Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}' }"
                       @balance-updated.window="navBalance = ($event.detail && $event.detail.formattedBalance) ? $event.detail.formattedBalance : (($event.detail && $event.detail[0]) ? $event.detail[0].formattedBalance : navBalance)"
                       class="inline-flex items-center gap-2 pl-3 pr-2 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold shadow-xs hover:bg-slate-800 transition-all border border-slate-800">
                        <span class="text-slate-400 font-normal">Saldo:</span>
                        <span class="font-mono text-lime-400" x-text="navBalance">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</span>
                        <span class="w-5 h-5 rounded-lg bg-lime-500 text-slate-950 flex items-center justify-center text-xs font-black">+</span>
                    </a>

                    <!-- Member Profile & Logout -->
                    <div class="flex items-center gap-1">
                        <a href="{{ route('topups.account') }}" title="Akun Saya"
                           class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 flex items-center justify-center text-xs font-extrabold transition-all">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </a>
                        <form method="POST" action="{{ route('topups.logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-lg">logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest: Login / Register Button -->
                    <a href="{{ route('topups.login') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs">
                        <span class="material-symbols-outlined text-base text-lime-400">login</span>
                        <span>Masuk / Daftar</span>
                    </a>
                @endauth

                <!-- <div class="h-4 w-px bg-slate-200 hidden sm:block"></div> -->
<!-- 
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    <span class="material-symbols-outlined text-base text-slate-400">storefront</span>
                    <span class="hidden sm:inline">Kasir POS</span>
                </a> -->
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Clean Retail Footer -->
    <footer class="mt-16 bg-white border-t border-slate-200/90 py-8 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-800">{{ config('app.name', 'FilaPOS') }} Games</span>
                <span>•</span>
                <span>Portal Top Up Game & PPOB. Powered by FilaPOS.</span>
            </div>
            <div class="flex items-center gap-4 text-slate-500">
                <span class="font-medium">⚡ Instan 24 Jam</span>
                <span>•</span>
                <span class="font-medium">Aman</span>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    @livewireScripts
    {!! ToastMagic::scripts() !!}

    <script>
        window.addEventListener('topup-alert', event => {
            const detail = (event.detail && event.detail[0]) ? event.detail[0] : (event.detail || {});
            const msg = detail.message || (typeof event.detail === 'string' ? event.detail : 'Pemberitahuan');
            const type = detail.type || 'info';

            try {
                if (typeof window.toastMagic !== 'undefined') {
                    if (type === 'error' && typeof window.toastMagic.error === 'function') {
                        window.toastMagic.error('Pemberitahuan', msg);
                    } else if (type === 'success' && typeof window.toastMagic.success === 'function') {
                        window.toastMagic.success('Berhasil', msg);
                    } else if (typeof window.toastMagic.info === 'function') {
                        window.toastMagic.info('Info', msg);
                    } else {
                        alert((type === 'error' ? '⚠️ ' : '✅ ') + msg);
                    }
                } else {
                    alert((type === 'error' ? '⚠️ ' : '✅ ') + msg);
                }
            } catch (err) {
                console.error('Toast error:', err);
                alert((type === 'error' ? '⚠️ ' : '✅ ') + msg);
            }
        });
    </script>
</body>

</html>
