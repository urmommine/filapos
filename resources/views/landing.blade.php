<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilaPOS Smart - Platform Kasir Digital, PPOB Terlengkap & AI Subscription</title>
    <meta name="description" content="FilaPOS Smart: Solusi bisnis all-in-one. Kelola kasir digital toko fisik, transaksi PPOB (pulsa, PLN, e-wallet, voucher game), dan langganan AI terpercaya dalam satu web app responsif.">
    <meta name="keywords" content="aplikasi kasir, ppob pulsa pln, top up game, voucher e-wallet, ai subscription indonesia, point of sale web, kasir pintar umkm">
    <meta name="theme-color" content="#84cc16">

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="FilaPOS Smart - Platform Bisnis Digital, Kasir & Layanan AI">
    <meta property="og:description" content="Satu platform untuk kelola kasir toko, transaksi PPOB lengkap (PLN, pulsa, game, e-wallet), dan langganan AI subscription instan.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .bg-grid-pattern {
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(100, 116, 139, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(100, 116, 139, 0.05) 1px, transparent 1px);
        }

        input[type="range"] {
            accent-color: #84cc16;
        }

        /* ======================================================== */
        /* OPENING & SCROLL ANIMATION SYSTEM                        */
        /* ======================================================== */
        @keyframes heroFadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes heroScaleIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(24px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes floatSlow {
            0%, 100% {
                transform: translateY(0px) rotate(-1deg);
            }
            50% {
                transform: translateY(-8px) rotate(0.5deg);
            }
        }

        @keyframes floatReverse {
            0%, 100% {
                transform: translateY(0px) rotate(1deg);
            }
            50% {
                transform: translateY(8px) rotate(-0.5deg);
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                opacity: 0.35;
                transform: scale(1);
            }
            50% {
                opacity: 0.65;
                transform: scale(1.08);
            }
        }

        /* Opening Animation Classes */
        .anim-fade-up {
            opacity: 0;
            animation: heroFadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .anim-scale-in {
            opacity: 0;
            animation: heroScaleIn 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .anim-float {
            animation: floatSlow 6s ease-in-out infinite;
        }

        .anim-float-reverse {
            animation: floatReverse 7s ease-in-out infinite;
        }

        .anim-glow {
            animation: pulseGlow 5s ease-in-out infinite;
        }

        /* Stagger Delays */
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }
        .delay-6 { animation-delay: 0.65s; }

        /* Scroll Reveal Classes */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1), transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* Reduced Motion Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .anim-fade-up,
            .anim-scale-in,
            .anim-float,
            .anim-float-reverse,
            .anim-glow {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
            .reveal {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased selection:bg-lime-200 selection:text-lime-900">

    <!-- ======================================================== -->
    <!-- NAVIGATION BAR                                           -->
    <!-- ======================================================== -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18">
                <!-- Brand Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-lime-500 text-slate-950 flex items-center justify-center shadow-md shadow-lime-500/25 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900">Fila<span class="text-lime-600">POS</span></span>
                            <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-lime-100 text-lime-800 rounded">Smart Ecosystem</span>
                        </div>
                        <span class="text-[11px] font-medium text-slate-500 -mt-0.5">Kasir • PPOB • AI Hub</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center gap-7">
                    <a href="#layanan" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">Ekosistem Layanan</a>
                    <a href="#pos-terminal" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">Kasir POS</a>
                    <a href="#ppob-digital" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">PPOB & Digital</a>
                    <a href="#ai-subscription" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">AI Subscription</a>
                    <a href="#simulasi" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">Kalkulator Cuan</a>
                    <a href="#faq" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">FAQ</a>
                </nav>

                <!-- Desktop Actions -->
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ url('/admin') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Portal Admin</span>
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-950 bg-lime-500 hover:bg-lime-600 active:bg-lime-700 rounded-lg shadow-sm shadow-lime-500/30 hover:shadow-md hover:shadow-lime-500/40 transition-all duration-200">
                        <span>Buka Platform POS</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center lg:hidden">
                    <button type="button" onclick="toggleMobileMenu()" id="mobileMenuBtn" aria-label="Menu" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-lime-500">
                        <svg id="menuIconOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="menuIconClose" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobileMenu" class="hidden lg:hidden bg-white border-b border-slate-200 px-4 pt-3 pb-6 space-y-3 shadow-lg">
            <div class="flex flex-col space-y-2">
                <a href="#layanan" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Ekosistem Layanan</a>
                <a href="#pos-terminal" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Kasir POS Digital</a>
                <a href="#ppob-digital" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">PPOB, PLN & Game</a>
                <a href="#ai-subscription" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">AI Subscriptions</a>
                <a href="#simulasi" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Simulasi Keuntungan</a>
                <a href="#faq" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Pertanyaan (FAQ)</a>
            </div>
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5">
                <a href="{{ url('/admin') }}" onclick="closeMobileMenu()" class="w-full py-2.5 px-4 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Masuk Portal Admin</span>
                </a>
                <a href="{{ route('login') }}" class="w-full py-2.5 px-4 text-xs font-bold text-center text-slate-950 bg-lime-500 hover:bg-lime-600 rounded-lg shadow-sm">
                    Buka Kasir POS Sekarang
                </a>
            </div>
        </div>
    </header>

    <!-- ======================================================== -->
    <!-- HERO SECTION                                             -->
    <!-- ======================================================== -->
    <section class="relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden bg-grid-pattern">
        <!-- Background Glow Accent with Breathing Animation -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[380px] bg-lime-200/45 rounded-full blur-3xl pointer-events-none -z-10 anim-glow"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <!-- Pill Badge (Opening Delay 1) -->
                <div class="anim-fade-up delay-1 inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-lime-100/90 border border-lime-300 text-lime-900 text-xs font-semibold mb-6 shadow-xs">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lime-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-lime-600"></span>
                    </span>
                    <span>Platform All-in-One: Kasir Web • Multi-PPOB • AI Subscriptions</span>
                </div>

                <!-- Main Heading (Opening Delay 2) -->
                <h1 class="anim-fade-up delay-2 text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-950 tracking-tight leading-[1.15]">
                    Satu Platform untuk <span class="text-lime-600 underline decoration-lime-300 decoration-wavy underline-offset-8">Kasir Cepat</span>, PPOB Lengkap & Layanan AI.
                </h1>

                <!-- Subtitle (Opening Delay 3) -->
                <p class="anim-fade-up delay-3 mt-6 text-base sm:text-lg text-slate-600 leading-relaxed font-normal max-w-3xl mx-auto">
                    Kelola transaksi kasir toko fisik Anda, raup cuan ekstra dari penjualan pulsa, token PLN, e-wallet, voucher game, hingga langganan tools AI modern langsung dalam satu web dashboard terintegrasi.
                </p>

                <!-- CTA Buttons (Opening Delay 4) -->
                <div class="anim-fade-up delay-4 mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 text-sm font-bold text-slate-950 bg-lime-500 hover:bg-lime-600 active:bg-lime-700 rounded-xl shadow-lg shadow-lime-500/30 hover:shadow-xl hover:shadow-lime-500/40 hover:-translate-y-0.5 transition-all duration-200">
                        <span>Buka Aplikasi Kasir</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="{{ url('/admin') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl shadow-xs hover:border-slate-400 hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Portal Manajemen Admin</span>
                    </a>
                </div>

                <!-- 3 Pillars Badges Strip (Opening Delay 5) -->
                <div class="anim-fade-up delay-5 mt-10 grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-3xl mx-auto text-left">
                    <div class="bg-white/80 backdrop-blur-xs border border-slate-200/90 rounded-xl p-3 flex items-center gap-3 shadow-xs hover:border-lime-400 hover:shadow-sm transition-all duration-200">
                        <div class="w-9 h-9 rounded-lg bg-lime-100 text-lime-800 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">Kasir Web Modern</div>
                            <div class="text-[11px] text-slate-500">Struk thermal, stok & multi-kasir</div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-xs border border-slate-200/90 rounded-xl p-3 flex items-center gap-3 shadow-xs hover:border-emerald-400 hover:shadow-sm transition-all duration-200">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">PPOB, Game & E-Wallet</div>
                            <div class="text-[11px] text-slate-500">PLN, pulsa, MLBB, Dana & OVO</div>
                        </div>
                    </div>

                    <div class="bg-white/80 backdrop-blur-xs border border-slate-200/90 rounded-xl p-3 flex items-center gap-3 shadow-xs hover:border-slate-800 hover:shadow-sm transition-all duration-200">
                        <div class="w-9 h-9 rounded-lg bg-slate-900 text-lime-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">AI Subscription Hub</div>
                            <div class="text-[11px] text-slate-500">ChatGPT, Midjourney & AI Tools</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- MULTI-SERVICE SHOWCASE (Opening Scale In Delay 6)        -->
            <!-- ======================================================== -->
            <div class="anim-scale-in delay-6 mt-14 relative max-w-5xl mx-auto">
                <!-- Outer Laptop/Tablet Shell -->
                <div class="rounded-2xl bg-slate-900 p-2 sm:p-3.5 shadow-2xl shadow-slate-950/20 border border-slate-800">
                    <!-- Top Window Bar -->
                    <div class="flex items-center justify-between px-3 py-2 bg-slate-950 rounded-t-xl mb-1 text-slate-400">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></div>
                        </div>
                        <div class="flex items-center gap-2 bg-slate-800/80 px-3 py-1 rounded-md text-[11px] font-mono text-slate-300">
                            <svg class="w-3 h-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>https://filapos.store/hub</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] font-medium text-lime-400 bg-lime-950/80 px-2.5 py-0.5 rounded border border-lime-800/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-lime-400 animate-pulse"></span>
                            <span>Multi-Service Ready</span>
                        </div>
                    </div>

                    <!-- Inner Mockup: 3 Services Cards Grid -->
                    <div class="bg-slate-100 rounded-b-xl overflow-hidden p-3 sm:p-5 text-slate-800 grid grid-cols-1 lg:grid-cols-3 gap-4">
                        
                        <!-- Panel 1: POS Kasir -->
                        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 flex flex-col justify-between shadow-xs hover:shadow-md transition-shadow">
                            <div>
                                <div class="flex items-center justify-between pb-2 mb-2.5 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded bg-lime-100 text-lime-700 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">Kasir POS Toko</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">Thermal Ready</span>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div class="p-2 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-slate-800 text-[11px]">Kopi Arabica Blend</p>
                                            <p class="text-[10px] text-slate-500">Stok: 50 pcs</p>
                                        </div>
                                        <span class="font-extrabold text-lime-700 text-xs">Rp 25.000</span>
                                    </div>
                                    <div class="p-2 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-slate-800 text-[11px]">Nasi Goreng Spesial</p>
                                            <p class="text-[10px] text-slate-500">Stok: 40 pcs</p>
                                        </div>
                                        <span class="font-extrabold text-lime-700 text-xs">Rp 20.000</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 font-medium">Transaksi POS:</span>
                                <span class="font-bold text-lime-700">Cetak Nota Instan ➔</span>
                            </div>
                        </div>

                        <!-- Panel 2: PPOB & Game Hub -->
                        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 flex flex-col justify-between shadow-xs hover:shadow-md transition-shadow">
                            <div>
                                <div class="flex items-center justify-between pb-2 mb-2.5 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">PPOB, Game & PLN</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">24/7 Otomatis</span>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div class="p-2 rounded-lg bg-emerald-50/50 border border-emerald-100 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-slate-800 text-[11px]">Token Listrik PLN 50.000</p>
                                            <p class="text-[10px] text-slate-500">ID: 1428-9921-3011</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-white px-1.5 py-0.5 rounded border border-emerald-200">SUKSES</span>
                                    </div>
                                    <div class="p-2 rounded-lg bg-emerald-50/50 border border-emerald-100 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-slate-800 text-[11px]">257 Diamond MLBB</p>
                                            <p class="text-[10px] text-slate-500">User ID: 88219 (Zone 2101)</p>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-white px-1.5 py-0.5 rounded border border-emerald-200">TERKIRIM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 font-medium">Margin PPOB:</span>
                                <span class="font-bold text-emerald-700">+ Cuan Rp 2.500/trx ➔</span>
                            </div>
                        </div>

                        <!-- Panel 3: AI Subscriptions Hub -->
                        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 flex flex-col justify-between shadow-xs hover:shadow-md transition-shadow">
                            <div>
                                <div class="flex items-center justify-between pb-2 mb-2.5 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded bg-slate-900 text-lime-400 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <span class="font-bold text-xs text-slate-900">AI Subscription</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-lime-800 bg-lime-100 px-1.5 py-0.5 rounded">Aktif Instan</span>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div class="p-2 rounded-lg bg-slate-900 text-white flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-[11px] text-lime-400">ChatGPT Plus / Team</p>
                                            <p class="text-[10px] text-slate-300">Akses GPT-4o & Canvas</p>
                                        </div>
                                        <span class="text-[10px] font-mono text-lime-300 bg-slate-800 px-1.5 py-0.5 rounded">Reseller OK</span>
                                    </div>
                                    <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-slate-800 text-[11px]">Midjourney & Claude Pro</p>
                                            <p class="text-[10px] text-slate-500">Desain & Smart Writing</p>
                                        </div>
                                        <span class="text-[10px] font-semibold text-slate-700 bg-white px-1.5 py-0.5 rounded border border-slate-200">1 Bulan</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 font-medium">Layanan AI:</span>
                                <span class="font-bold text-slate-900">Katalog Lengkap ➔</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Floating Live PPOB Notification (Smooth Float Animation) -->
                <div class="anim-float absolute -top-6 -left-2 sm:-top-8 sm:-left-6 w-52 sm:w-64 bg-white rounded-xl shadow-2xl p-3 border border-slate-200/90 text-[11px] pointer-events-none sm:pointer-events-auto">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-bold text-slate-900">Transaksi PPOB Berhasil</span>
                        <span class="text-[9px] text-slate-600 ml-auto">Baru saja</span>
                    </div>
                    <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-100 text-[10px] text-slate-700 font-mono">
                        <div>PLN Prepaid: 50.000</div>
                        <div class="text-emerald-700 font-bold">Strum: 31.2 kWh (Token Siap)</div>
                    </div>
                </div>

                <!-- Floating AI Assistant Card (Reverse Float Animation) -->
                <div class="anim-float-reverse absolute -bottom-6 -right-2 sm:-bottom-8 sm:right-4 w-52 sm:w-64 bg-slate-950 text-white rounded-xl shadow-2xl p-3 border border-slate-800 text-[11px] pointer-events-none sm:pointer-events-auto">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-lime-400"></span>
                        <span class="font-bold text-lime-400">AI Store Assistant</span>
                    </div>
                    <p class="text-[10px] text-slate-300 leading-tight">
                        "Produk Kopi Arabica stok tersisa 10 pcs. Mau saya buatkan caption promo diskon 15% untuk WhatsApp?"
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- VALUE PROPOSITIONS METRIC STRIP (Scroll Reveal)          -->
    <!-- ======================================================== -->
    <section class="border-y border-slate-200 bg-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-3 reveal" data-delay="0">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">3-in-1 Hub</div>
                    <div class="mt-1 text-xs font-semibold text-lime-700 uppercase tracking-wider">Multi Layanan</div>
                    <p class="mt-1 text-xs text-slate-500">Kasir Toko, PPOB Digital, & AI Subscriptions</p>
                </div>
                <div class="p-3 reveal" data-delay="100">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">1000+ Produk</div>
                    <div class="mt-1 text-xs font-semibold text-lime-700 uppercase tracking-wider">PPOB & Game</div>
                    <p class="mt-1 text-xs text-slate-500">PLN, Pulsa, DANA, OVO, MLBB, Free Fire, dll</p>
                </div>
                <div class="p-3 reveal" data-delay="200">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Instan & 24 Jam</div>
                    <div class="mt-1 text-xs font-semibold text-lime-700 uppercase tracking-wider">Transaksi Otomatis</div>
                    <p class="mt-1 text-xs text-slate-500">Proses kilat dalam hitungan detik non-stop</p>
                </div>
                <div class="p-3 reveal" data-delay="300">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Rp 0,- Hardware</div>
                    <div class="mt-1 text-xs font-semibold text-lime-700 uppercase tracking-wider">Tanpa Mesin Khusus</div>
                    <p class="mt-1 text-xs text-slate-500">Cukup buka browser di HP, tablet, atau PC</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- 3 CORE PILLARS OVERVIEW (Scroll Reveal)                  -->
    <!-- ======================================================== -->
    <section id="layanan" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14 reveal">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Ekosistem FilaPOS Smart</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">3 Pilar Utama untuk Melejitkan Pendapatan Toko</h2>
                <p class="mt-3 text-sm text-slate-600">Bukan cuma kasir biasa, FilaPOS Smart mengubah usaha Anda menjadi pusat layanan digital modern.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Pillar 1 Card -->
                <div id="pos-terminal" class="reveal bg-white rounded-3xl p-7 border border-slate-200/90 shadow-xs hover:shadow-xl hover:border-lime-300 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group" data-delay="0">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-lime-100 text-lime-800 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-lime-200 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-lime-700 bg-lime-50 px-2 py-0.5 rounded">Pilar 01</span>
                        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Sistem Kasir Digital (POS)</h3>
                        <p class="mt-3 text-xs text-slate-600 leading-relaxed">
                            Aplikasi kasir web-based responsif untuk transaksi toko fisik, cetak struk thermal ESC/POS via Bluetooth/USB, monitor stok berkurang otomatis, dan rekapan omzet real-time.
                        </p>
                        <ul class="mt-5 space-y-2 text-xs text-slate-700">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-lime-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Multi-device (HP, Tablet, PC)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-lime-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Cetak Nota Thermal 58mm & 80mm</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-lime-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Pemisahan Hak Akses Kasir vs Owner</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-lime-700 hover:text-lime-800 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            <span>Akses Terminal Kasir</span>
                            <span>➔</span>
                        </a>
                    </div>
                </div>

                <!-- Pillar 2 Card -->
                <div id="ppob-digital" class="reveal bg-white rounded-3xl p-7 border border-slate-200/90 shadow-xs hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group" data-delay="150">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-emerald-200 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Pilar 02</span>
                        <h3 class="mt-2 text-xl font-extrabold text-slate-900">Layanan PPOB & Produk Digital</h3>
                        <p class="mt-3 text-xs text-slate-600 leading-relaxed">
                            Tambah sumber penghasilan toko dengan melayani pembayaran harian pelanggan: token listrik PLN, pulsa & kuota data semua operator, top-up e-wallet, dan voucher game online.
                        </p>
                        <ul class="mt-5 space-y-2 text-xs text-slate-700">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Token PLN, Tagihan Listrik & BPJS</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Top Up DANA, OVO, GoPay, ShopeePay</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Voucher Mobile Legends, FF, Steam, dll</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="#simulasi" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            <span>Hitung Potensi Cuan PPOB</span>
                            <span>➔</span>
                        </a>
                    </div>
                </div>

                <!-- Pillar 3 Card -->
                <div id="ai-subscription" class="reveal bg-white rounded-3xl p-7 border border-slate-200/90 shadow-xs hover:shadow-xl hover:border-slate-800 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group" data-delay="300">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-slate-900 text-lime-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-slate-800 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-900 bg-slate-100 px-2 py-0.5 rounded">Pilar 03</span>
                        <h3 class="mt-2 text-xl font-extrabold text-slate-900">AI Subscription Hub</h3>
                        <p class="mt-3 text-xs text-slate-600 leading-relaxed">
                            Akses langganan akun AI premium (ChatGPT Plus, Midjourney, Claude, Gemini) untuk kebutuhan pribadi, operasional toko, maupun dijual kembali (*reseller*) dengan profit menjanjikan.
                        </p>
                        <ul class="mt-5 space-y-2 text-xs text-slate-700">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-lime-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Akses ChatGPT Plus & GPT-4o Instan</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-lime-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Tools AI Desain, Copywriting & Media</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-lime-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Peluang Bisnis Reseller Akun AI Digital</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ url('/admin') }}" class="text-xs font-bold text-slate-900 hover:text-slate-700 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            <span>Kelola Layanan AI</span>
                            <span>➔</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- DETAILED SERVICE MATRIX (Scroll Reveal)                  -->
    <!-- ======================================================== -->
    <section class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14 reveal">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Katalog Lengkap</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Semua Layanan Transaksi Digital yang Anda Butuhkan</h2>
                <p class="mt-3 text-sm text-slate-600">Pelanggan tidak perlu ke tempat lain. Jadikan toko Anda pusat transaksi segala kebutuhan sehari-hari.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Item 1: PLN & Tagihan -->
                <div class="reveal bg-slate-50 p-5 rounded-2xl border border-slate-200/80 hover:bg-white hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="50">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">Listrik PLN & Tagihan</h4>
                    <p class="mt-1 text-xs text-slate-600">Token listrik prabayar, bayar tagihan PLN pascabayar, PDAM, dan BPJS Kesehatan secara instan.</p>
                </div>

                <!-- Item 2: Pulsa & Paket Data -->
                <div class="reveal bg-slate-50 p-5 rounded-2xl border border-slate-200/80 hover:bg-white hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="100">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">Pulsa & Paket Data</h4>
                    <p class="mt-1 text-xs text-slate-600">Telkomsel, Indosat Ooredoo, XL, Axis, Tri, dan Smartfren dengan harga grosir kompetitif.</p>
                </div>

                <!-- Item 3: Top Up E-Wallet -->
                <div class="reveal bg-slate-50 p-5 rounded-2xl border border-slate-200/80 hover:bg-white hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="150">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">Isi Saldo E-Wallet</h4>
                    <p class="mt-1 text-xs text-slate-600">Top-up saldo DANA, GoPay Customer & Driver, OVO, ShopeePay, Maxim, hingga LinkAja.</p>
                </div>

                <!-- Item 4: Top Up Game Online -->
                <div class="reveal bg-slate-50 p-5 rounded-2xl border border-slate-200/80 hover:bg-white hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="200">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">Voucher Game Favorit</h4>
                    <p class="mt-1 text-xs text-slate-600">Mobile Legends Diamonds, Free Fire, PUBG Mobile, Roblox, Valorant, Genshin Impact & Steam Wallet.</p>
                </div>

                <!-- Item 5: Media Sosial & Streaming -->
                <div class="reveal bg-slate-50 p-5 rounded-2xl border border-slate-200/80 hover:bg-white hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="250">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">Streaming & Sosmed</h4>
                    <p class="mt-1 text-xs text-slate-600">Voucher Spotify Premium, Netflix, YouTube Premium, Disney+, dan boost media sosial terpercaya.</p>
                </div>

                <!-- Item 6: ChatGPT & AI Assistant -->
                <div class="reveal bg-slate-50 p-5 rounded-2xl border border-slate-200/80 hover:bg-white hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="300">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-lime-400 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">Langganan Akun AI</h4>
                    <p class="mt-1 text-xs text-slate-600">ChatGPT Plus, Claude Pro, Midjourney, Canva Pro, dan Gemini Advanced dengan garansi penuh.</p>
                </div>

                <!-- Item 7: Kasir Toko & Thermal Struk -->
                <div class="reveal bg-slate-50 p-5 rounded-2xl border border-slate-200/80 hover:bg-white hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="350">
                    <div class="w-10 h-10 rounded-xl bg-lime-100 text-lime-800 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">Cetak Struk Thermal</h4>
                    <p class="mt-1 text-xs text-slate-600">Cetak bukti transaksi kasir ritel maupun struk PPOB (Token PLN, Pulsa) via printer kasir Bluetooth/USB.</p>
                </div>

                <!-- Item 8: Multi Pembayaran & QRIS -->
                <div class="reveal bg-slate-50 p-5 rounded-2xl border border-slate-200/80 hover:bg-white hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="400">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">QRIS & Multi-Payment</h4>
                    <p class="mt-1 text-xs text-slate-600">Terima pembayaran pelanggan lewat tunai, transfer bank, hingga QRIS dinamis/statis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- INTERACTIVE PROFIT / CUAN CALCULATOR (Scroll Reveal)     -->
    <!-- ======================================================== -->
    <section id="simulasi" class="py-20 bg-slate-50 border-y border-slate-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Simulasi Pendapatan Ekstra</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Hitung Potensi Cuan Tambahan Bisnis Anda</h2>
                <p class="mt-2 text-sm text-slate-600">Kombinasikan keuntungan penjualan kasir toko dengan komisi transaksi PPOB dan margin layanan AI.</p>
            </div>

            <!-- Calculator Card -->
            <div class="reveal bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left: Sliders Input (7 cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Slider 1: Transaksi Kasir Toko Fisik -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="txRange" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Transaksi Kasir Toko Harian</label>
                                <span class="text-sm font-extrabold text-lime-700 bg-lime-100 px-2.5 py-0.5 rounded-lg">
                                    <span id="txValueText">50</span> Trx Kasir / hari
                                </span>
                            </div>
                            <input id="txRange" type="range" min="10" max="250" step="5" value="50" class="w-full h-2 bg-slate-200 rounded-lg cursor-pointer" oninput="updateCalculator()">
                            <div class="flex justify-between text-[11px] text-slate-600 mt-1 font-medium">
                                <span>10 Trx</span>
                                <span>100 Trx</span>
                                <span>250+ Trx</span>
                            </div>
                        </div>

                        <!-- Slider 2: Transaksi PPOB & Game (Listrik, Pulsa, Diamond) -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="ppobRange" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Transaksi PPOB & Game Harian</label>
                                <span class="text-sm font-extrabold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-lg">
                                    <span id="ppobValueText">30</span> Trx PPOB / hari
                                </span>
                            </div>
                            <input id="ppobRange" type="range" min="5" max="150" step="5" value="30" class="w-full h-2 bg-slate-200 rounded-lg cursor-pointer" oninput="updateCalculator()">
                            <div class="flex justify-between text-[11px] text-slate-600 mt-1 font-medium">
                                <span>5 Trx</span>
                                <span>50 Trx</span>
                                <span>150+ Trx</span>
                            </div>
                        </div>

                        <!-- Slider 3: Penjualan Akun AI Subscription Bulanan -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="aiRange" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Penjualan Langganan AI / Bulan</label>
                                <span class="text-sm font-extrabold text-slate-900 bg-slate-100 px-2.5 py-0.5 rounded-lg">
                                    <span id="aiValueText">10</span> Akun AI / bulan
                                </span>
                            </div>
                            <input id="aiRange" type="range" min="0" max="50" step="1" value="10" class="w-full h-2 bg-slate-200 rounded-lg cursor-pointer" oninput="updateCalculator()">
                            <div class="flex justify-between text-[11px] text-slate-600 mt-1 font-medium">
                                <span>0 Akun</span>
                                <span>25 Akun</span>
                                <span>50+ Akun</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Dynamic Outcome Panel (5 cols) -->
                    <div class="lg:col-span-5 bg-slate-900 rounded-2xl p-6 text-white flex flex-col justify-between space-y-6 shadow-xl">
                        <div>
                            <span class="text-[11px] uppercase tracking-wider font-bold text-lime-400">Estimasi Tambahan Profit Bersih</span>
                            <div class="mt-2">
                                <div class="text-xs text-slate-400">Total Keuntungan Tambahan:</div>
                                <div id="calcMoneySaved" class="text-3xl font-extrabold text-lime-400 tracking-tight mt-0.5">
                                    Rp 3.750.000
                                </div>
                                <div class="text-[11px] text-slate-400">per bulan (PPOB + Kasir + AI)</div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-800 grid grid-cols-2 gap-3">
                                <div>
                                    <div class="text-[11px] text-slate-400">Cuan PPOB & Game:</div>
                                    <div id="calcPpobProfit" class="text-base font-bold text-emerald-400">Rp 2.250.000</div>
                                    <div class="text-[10px] text-slate-500">Margin ~Rp 2.500/trx</div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-slate-400">Profit Akun AI:</div>
                                    <div id="calcAiProfit" class="text-base font-bold text-white">Rp 500.000</div>
                                    <div class="text-[10px] text-slate-500">Margin ~Rp 50rb/akun</div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('login') }}" class="w-full py-3 bg-lime-500 hover:bg-lime-600 active:bg-lime-700 text-slate-950 font-extrabold text-xs text-center rounded-xl transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                            Mulai Dapatkan Cuan Sekarang ➔
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- 3 SIMPLE STEPS TO GET STARTED (Scroll Reveal)            -->
    <!-- ======================================================== -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14 reveal">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Alur Mudah</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Mulai Seluruh Layanan dalam 3 Langkah Praktis</h2>
                <p class="mt-2 text-sm text-slate-600">Satu akun untuk mengoperasikan kasir, jualan PPOB, dan berlangganan AI.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="reveal bg-slate-50 p-7 rounded-2xl border border-slate-200/80 relative shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="0">
                    <div class="w-10 h-10 rounded-xl bg-lime-500 text-slate-950 font-extrabold text-sm flex items-center justify-center mb-4">
                        01
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Buka di Browser Gadget Anda</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Akses FilaPOS Smart langsung melalui Google Chrome, Safari, atau browser pilihan Anda tanpa ribet download APK atau root perangkat.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="reveal bg-slate-50 p-7 rounded-2xl border border-slate-200/80 relative shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="150">
                    <div class="w-10 h-10 rounded-xl bg-lime-500 text-slate-950 font-extrabold text-sm flex items-center justify-center mb-4">
                        02
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pilih Layanan yang Ingin Digunakan</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Input pesanan barang toko di menu POS kasir, atau masukkan nomor pelanggan untuk isi token PLN, pulsa, game, dan akses AI.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="reveal bg-slate-50 p-7 rounded-2xl border border-slate-200/80 relative shadow-xs hover:shadow-md hover:-translate-y-1 transition-all duration-200" data-delay="300">
                    <div class="w-10 h-10 rounded-xl bg-lime-500 text-slate-950 font-extrabold text-sm flex items-center justify-center mb-4">
                        03
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Terima Bayar & Cetak Bukti Struk</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Cetak bukti pembayaran via printer thermal atau kirim struk digital ke pelanggan secara cepat dan transparan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- INTERACTIVE FAQ SECTION (Scroll Reveal)                  -->
    <!-- ======================================================== -->
    <section id="faq" class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Tanya Jawab</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Pertanyaan yang Sering Diajukan</h2>
                <p class="mt-2 text-sm text-slate-600">Pelajari lebih lanjut tentang integrasi Kasir POS, PPOB, dan Layanan AI FilaPOS Smart.</p>
            </div>

            <!-- FAQ Accordion Container -->
            <div class="space-y-3.5">
                <!-- Item 1 -->
                <div class="reveal border border-slate-200 rounded-2xl overflow-hidden bg-white hover:border-slate-300 transition-colors" data-delay="0">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none cursor-pointer" onclick="toggleFaq(this)">
                        <span>Apakah saya bisa menjalankan kasir toko dan jualan PPOB secara bersamaan?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Tentu saja! FilaPOS Smart dirancang sebagai ekosistem bisnis terpadu. Kasir Anda dapat melayani pesanan produk fisik toko (makanan, minuman, ritel) sekaligus memproses transaksi digital seperti pulsa, token listrik PLN, top-up e-wallet, dan voucher game dari satu layar kerja.
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="reveal border border-slate-200 rounded-2xl overflow-hidden bg-white hover:border-slate-300 transition-colors" data-delay="100">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none cursor-pointer" onclick="toggleFaq(this)">
                        <span>Apa saja jenis layanan AI Subscription yang tersedia di platform?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Kami menyediakan akses langganan tools AI terpopuler dunia seperti ChatGPT Plus (GPT-4o), Midjourney, Claude Pro, Canva Pro, dan asisten pintar copywriter toko. Anda dapat menggunakannya untuk menunjang promosi bisnis sendiri atau menjualnya kembali kepada pelanggan toko Anda dengan margin keuntungan yang menarik.
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="reveal border border-slate-200 rounded-2xl overflow-hidden bg-white hover:border-slate-300 transition-colors" data-delay="200">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none cursor-pointer" onclick="toggleFaq(this)">
                        <span>Apakah struk transaksi PPOB (seperti token PLN) bisa dicetak di printer thermal?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Bisa! Sistem mendukung cetak struk ESC/POS standar (ukuran kertas 58mm dan 80mm) via Bluetooth atau kabel USB. Nomor token listrik PLN 20-digit, kode voucher game, dan rincian transaksi akan tercetak rapi layaknya struk kasir profesional.
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="reveal border border-slate-200 rounded-2xl overflow-hidden bg-white hover:border-slate-300 transition-colors" data-delay="300">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none cursor-pointer" onclick="toggleFaq(this)">
                        <span>Perangkat apa saja yang didukung oleh FilaPOS Smart?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Semua perangkat yang memiliki peramban web (browser modern) seperti smartphone Android, iPhone, iPad, tablet, laptop, dan komputer kasir PC. Tampilan kami 100% responsif dan otomatis menyesuaikan orientasi layar Anda.
                    </div>
                </div>

                <!-- Item 5 -->
                <div class="reveal border border-slate-200 rounded-2xl overflow-hidden bg-white hover:border-slate-300 transition-colors" data-delay="400">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none cursor-pointer" onclick="toggleFaq(this)">
                        <span>Bagaimana cara mulai menggunakan platform FilaPOS Smart?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Cukup klik tombol "Buka Kasir POS" untuk langsung masuk ke terminal kasir penjualan, atau klik tombol "Portal Admin" untuk mengelola pengaturan toko, produk, dan laporan keuangan bisnis Anda.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- FINAL HIGH-CONVERSION CTA (Scroll Reveal)                -->
    <!-- ======================================================== -->
    <section class="py-16 bg-slate-900 text-white relative overflow-hidden">
        <!-- Glow gradient -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-lime-500/20 rounded-full blur-3xl pointer-events-none anim-glow"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center reveal">
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Upgrade Bisnis Anda ke Ekosistem Digital Terlengkap Sekarang
            </h2>
            <p class="mt-4 text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-normal">
                Kelola kasir toko lebih efisien, gandakan keuntungan lewat transaksi PPOB, dan nikmati kemudahan langganan AI dalam satu genggaman.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 text-sm font-bold text-slate-950 bg-lime-400 hover:bg-lime-300 active:bg-lime-500 rounded-xl shadow-lg shadow-lime-400/20 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <span>Buka Aplikasi Kasir Sekarang</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
                <a href="{{ url('/admin') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl hover:-translate-y-0.5 transition-all duration-200">
                    <span>Akses Portal Admin</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- FOOTER                                                   -->
    <!-- ======================================================== -->
    <footer class="bg-white border-t border-slate-200 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Brand Info -->
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-lime-500 text-slate-950 flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-base font-extrabold tracking-tight text-slate-900">Fila<span class="text-lime-600">POS</span> Smart</span>
                    <span class="text-xs text-slate-400 pl-2 border-l border-slate-200">Kasir Digital • PPOB • AI Hub</span>
                </div>

                <!-- Footer Nav Links -->
                <div class="flex flex-wrap items-center justify-center gap-6 text-xs text-slate-600">
                    <a href="#layanan" class="hover:text-lime-600 transition-colors">Ekosistem</a>
                    <a href="#pos-terminal" class="hover:text-lime-600 transition-colors">Kasir POS</a>
                    <a href="#ppob-digital" class="hover:text-lime-600 transition-colors">PPOB & PLN</a>
                    <a href="#ai-subscription" class="hover:text-lime-600 transition-colors">AI Subscriptions</a>
                    <a href="#simulasi" class="hover:text-lime-600 transition-colors">Simulasi Cuan</a>
                    <a href="#faq" class="hover:text-lime-600 transition-colors">FAQ</a>
                    <a href="{{ route('login') }}" class="hover:text-lime-600 font-semibold transition-colors">Login Kasir</a>
                    <a href="{{ url('/admin') }}" class="hover:text-lime-600 font-semibold transition-colors">Portal Admin</a>
                </div>

                <!-- Copyright -->
                <div class="text-xs text-slate-600 text-center md:text-right">
                    &copy; {{ date('Y') }} FilaPOS Smart. Seluruh hak cipta dilindungi.
                </div>
            </div>
        </div>
    </footer>

    <!-- ======================================================== -->
    <!-- JAVASCRIPT LOGIC (Interactions, Mobile Menu, Calculator,  -->
    <!-- Scroll Reveal Observer)                                  -->
    <!-- ======================================================== -->
    <script>
        // Mobile Drawer Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const openIcon = document.getElementById('menuIconOpen');
            const closeIcon = document.getElementById('menuIconClose');
            if (!menu) return;

            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                menu.classList.remove('hidden');
                if (openIcon) openIcon.classList.add('hidden');
                if (closeIcon) closeIcon.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
                if (openIcon) openIcon.classList.remove('hidden');
                if (closeIcon) closeIcon.classList.add('hidden');
            }
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const openIcon = document.getElementById('menuIconOpen');
            const closeIcon = document.getElementById('menuIconClose');
            if (menu) menu.classList.add('hidden');
            if (openIcon) openIcon.classList.remove('hidden');
            if (closeIcon) closeIcon.classList.add('hidden');
        }

        // Close mobile menu on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });

        // Interactive Combined Profit Calculator
        function updateCalculator() {
            const txEl = document.getElementById('txRange');
            const ppobEl = document.getElementById('ppobRange');
            const aiEl = document.getElementById('aiRange');
            if (!txEl || !ppobEl || !aiEl) return;

            const tx = parseInt(txEl.value, 10);
            const ppob = parseInt(ppobEl.value, 10);
            const ai = parseInt(aiEl.value, 10);

            const txValText = document.getElementById('txValueText');
            const ppobValText = document.getElementById('ppobValueText');
            const aiValText = document.getElementById('aiValueText');

            if (txValText) txValText.innerText = tx;
            if (ppobValText) ppobValText.innerText = ppob;
            if (aiValText) aiValText.innerText = ai;

            // Calculations:
            const monthlyPpob = ppob * 2500 * 30;
            const monthlyAi = ai * 50000;
            const monthlyPos = tx * 1000 * 30;

            const totalMonthly = monthlyPpob + monthlyAi + monthlyPos;

            const calcMoney = document.getElementById('calcMoneySaved');
            const calcPpob = document.getElementById('calcPpobProfit');
            const calcAi = document.getElementById('calcAiProfit');

            if (calcMoney) calcMoney.innerText = 'Rp ' + totalMonthly.toLocaleString('id-ID');
            if (calcPpob) calcPpob.innerText = 'Rp ' + monthlyPpob.toLocaleString('id-ID');
            if (calcAi) calcAi.innerText = 'Rp ' + monthlyAi.toLocaleString('id-ID');
        }

        // FAQ Accordion
        function toggleFaq(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('svg');
            const isHidden = content.classList.contains('hidden');

            // Close other FAQs
            document.querySelectorAll('#faq .px-5.pb-4').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('#faq svg').forEach(el => el.classList.remove('rotate-180'));

            if (isHidden) {
                content.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
            }
        }

        // High-Performance Scroll Reveal with IntersectionObserver
        function initScrollReveal() {
            const reveals = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                reveals.forEach(el => el.classList.add('revealed'));
                return;
            }

            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const el = entry.target;
                        const delay = el.getAttribute('data-delay') || 0;
                        if (delay > 0) {
                            setTimeout(() => {
                                el.classList.add('revealed');
                            }, parseInt(delay, 10));
                        } else {
                            el.classList.add('revealed');
                        }
                        obs.unobserve(el);
                    }
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -30px 0px'
            });

            reveals.forEach(el => observer.observe(el));
        }

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', function() {
            updateCalculator();
            initScrollReveal();
        });
    </script>
</body>

</html>
