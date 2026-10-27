<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilaPOS Smart - Aplikasi Kasir Digital Web-Based untuk UMKM & Bisnis Modern</title>
    <meta name="description" content="FilaPOS Smart adalah aplikasi kasir (POS) berbasis web modern yang cepat, responsif di HP/tablet/laptop, dan siap cetak struk thermal tanpa mesin kasir mahal.">
    <meta name="keywords" content="aplikasi kasir, web pos, pos smart, software kasir umkm, point of sale indonesia, cetak struk thermal, kasir web">
    <meta name="theme-color" content="#84cc16">

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="FilaPOS Smart - Aplikasi Kasir Digital Web-Based Modern">
    <meta property="og:description" content="Solusi kasir digital praktis di browser Anda. Ringan, cepat, hemat biaya tanpa investasi mesin kasir khusus.">

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

        /* Subtle grid background */
        .bg-grid-pattern {
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(100, 116, 139, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(100, 116, 139, 0.05) 1px, transparent 1px);
        }

        /* Thermal receipt paper zigzag effect */
        .receipt-tear {
            background: radial-gradient(circle, transparent, transparent 50%, #ffffff 50%, #ffffff 100%);
            background-size: 14px 14px;
        }

        /* Custom range slider styling */
        input[type="range"] {
            accent-color: #84cc16;
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
                    <div class="w-10 h-10 rounded-xl bg-lime-500 text-white flex items-center justify-center shadow-md shadow-lime-500/25 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900">Fila<span class="text-lime-600">POS</span></span>
                            <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-lime-100 text-lime-800 rounded">Smart</span>
                        </div>
                        <span class="text-[11px] font-medium text-slate-500 -mt-0.5">Kasir Digital Web</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">Fitur</a>
                    <a href="#keunggulan" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">Keunggulan</a>
                    <a href="#simulasi" class="text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors">Simulasi Hemat</a>
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
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-lime-600 hover:bg-lime-700 active:bg-lime-800 rounded-lg shadow-sm shadow-lime-600/30 hover:shadow-md hover:shadow-lime-600/40 transition-all duration-200">
                        <span>Buka Kasir POS</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center sm:hidden">
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
        <div id="mobileMenu" class="hidden sm:hidden bg-white border-b border-slate-200 px-4 pt-3 pb-6 space-y-3">
            <div class="flex flex-col space-y-2">
                <a href="#fitur" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Fitur Kasir</a>
                <a href="#keunggulan" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Keunggulan Bisnis</a>
                <a href="#simulasi" onclick="closeMobileMenu()" class="px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 rounded-lg">Simulasi Hemat</a>
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
                <a href="{{ route('login') }}" class="w-full py-2.5 px-4 text-xs font-bold text-center text-white bg-lime-600 hover:bg-lime-700 rounded-lg shadow-sm">
                    Buka Kasir POS Sekarang
                </a>
            </div>
        </div>
    </header>

    <!-- ======================================================== -->
    <!-- HERO SECTION                                             -->
    <!-- ======================================================== -->
    <section class="relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden bg-grid-pattern">
        <!-- Background Glow Accent -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-lime-200/40 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <!-- Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-lime-100/90 border border-lime-300 text-lime-900 text-xs font-semibold mb-6 shadow-xs">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-lime-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-lime-600"></span>
                    </span>
                    <span>Aplikasi Kasir Web-Based Modern • Bebas Biaya Mesin Khusus</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-950 tracking-tight leading-[1.15]">
                    Transaksi Kasir <span class="text-lime-600 underline decoration-lime-300 decoration-wavy underline-offset-8">Lebih Cepat</span>, Ringan di Semua Gadget.
                </h1>

                <!-- Subtitle -->
                <p class="mt-6 text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                    Kelola penjualan, cetak struk thermal instan, dan pantau stok toko tanpa ribet instalasi aplikasi. Cukup buka browser di smartphone, tablet, atau laptop Anda.
                </p>

                <!-- CTA Buttons -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 text-sm font-bold text-white bg-lime-600 hover:bg-lime-700 active:bg-lime-800 rounded-xl shadow-lg shadow-lime-600/30 hover:shadow-xl hover:shadow-lime-600/40 transition-all duration-200">
                        <span>Buka Aplikasi Kasir</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="{{ url('/admin') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl shadow-xs hover:border-slate-400 transition-all">
                        <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Portal Manajemen Admin</span>
                    </a>
                </div>

                <!-- Trust Chips -->
                <div class="mt-10 flex flex-wrap items-center justify-center gap-y-2 gap-x-6 text-xs font-medium text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>100% Berbasis Web</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Support Printer ESC/POS Thermal</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Multi-Perangkat & Responsif</span>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- MULTI-DEVICE MOCKUP SHOWCASE (LAPTOP + HP + THERMAL STRUK) -->
            <!-- ======================================================== -->
            <div class="mt-14 relative max-w-5xl mx-auto">
                <!-- Main Tablet / Laptop Display Frame -->
                <div class="rounded-2xl bg-slate-900 p-2 sm:p-3.5 shadow-2xl shadow-slate-950/20 border border-slate-800">
                    <!-- Browser Header Bar -->
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
                            <span>https://filapos.store/pos</span>
                        </div>
                        <div class="text-[11px] font-medium text-lime-400 bg-lime-950/80 px-2 py-0.5 rounded border border-lime-800/40">
                            Kasir Aktif
                        </div>
                    </div>

                    <!-- Inner Mockup UI (POS Screen) -->
                    <div class="bg-slate-100 rounded-b-xl overflow-hidden p-3 sm:p-4 text-slate-800 grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4">
                        <!-- Left Catalog (8 cols) -->
                        <div class="md:col-span-8 flex flex-col space-y-3">
                            <!-- Category Filter Pill Bar -->
                            <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar text-xs">
                                <span class="px-3 py-1.5 font-bold bg-lime-600 text-white rounded-lg whitespace-nowrap shadow-xs">Semua (18)</span>
                                <span class="px-3 py-1.5 font-medium bg-white text-slate-600 border border-slate-200 rounded-lg whitespace-nowrap">Minuman (5)</span>
                                <span class="px-3 py-1.5 font-medium bg-white text-slate-600 border border-slate-200 rounded-lg whitespace-nowrap">Makanan (4)</span>
                                <span class="px-3 py-1.5 font-medium bg-white text-slate-600 border border-slate-200 rounded-lg whitespace-nowrap">Snack (5)</span>
                            </div>

                            <!-- Products Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                <!-- Product Card 1 -->
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-xs flex flex-col justify-between hover:border-lime-400 transition-colors">
                                    <div class="h-20 bg-lime-50 rounded-lg flex items-center justify-center text-lime-700 font-bold text-xs mb-2">
                                        <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-xs text-slate-900 leading-tight">Kopi Arabica</h4>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs font-extrabold text-lime-700">Rp 25.000</span>
                                            <span class="text-[10px] text-slate-600 bg-slate-100 px-1 rounded font-medium">Stok: 50</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Card 2 -->
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-xs flex flex-col justify-between hover:border-lime-400 transition-colors">
                                    <div class="h-20 bg-amber-50 rounded-lg flex items-center justify-center text-amber-700 font-bold text-xs mb-2">
                                        <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-xs text-slate-900 leading-tight">Es Teh Manis</h4>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs font-extrabold text-lime-700">Rp 8.000</span>
                                            <span class="text-[10px] text-slate-600 bg-slate-100 px-1 rounded font-medium">Stok: 100</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Card 3 -->
                                <div class="hidden sm:flex bg-white p-2.5 rounded-xl border border-slate-200 shadow-xs flex-col justify-between hover:border-lime-400 transition-colors">
                                    <div class="h-20 bg-orange-50 rounded-lg flex items-center justify-center text-orange-700 font-bold text-xs mb-2">
                                        <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-xs text-slate-900 leading-tight">Nasi Goreng Spesial</h4>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs font-extrabold text-lime-700">Rp 20.000</span>
                                            <span class="text-[10px] text-slate-600 bg-slate-100 px-1 rounded font-medium">Stok: 40</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Cart / Order Panel (4 cols) -->
                        <div class="hidden md:flex md:col-span-4 bg-white rounded-xl border border-slate-200 p-3 flex-col justify-between shadow-xs">
                            <div>
                                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                    <span class="font-bold text-xs text-slate-900">Pesanan Aktif</span>
                                    <span class="text-[10px] bg-lime-100 text-lime-800 font-bold px-1.5 py-0.5 rounded">2 Item</span>
                                </div>
                                <div class="divide-y divide-slate-100 py-1 text-xs">
                                    <div class="py-2 flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-800 text-[11px]">1x Kopi Arabica</p>
                                            <p class="text-[10px] text-slate-600">@ Rp 25.000</p>
                                        </div>
                                        <span class="font-bold text-slate-900 text-xs">Rp 25.000</span>
                                    </div>
                                    <div class="py-2 flex items-center justify-between">
                                        <div>
                                            <p class="font-semibold text-slate-800 text-[11px]">1x Es Teh Manis</p>
                                            <p class="text-[10px] text-slate-600">@ Rp 8.000</p>
                                        </div>
                                        <span class="font-bold text-slate-900 text-xs">Rp 8.000</span>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-slate-100">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-slate-500 font-medium">Subtotal</span>
                                    <span class="font-semibold text-slate-700">Rp 33.000</span>
                                </div>
                                <div class="flex justify-between text-xs font-extrabold text-slate-900 mb-3">
                                    <span>Total Bayar</span>
                                    <span class="text-lime-700 text-sm">Rp 33.000</span>
                                </div>
                                <button type="button" class="w-full py-2 bg-lime-600 text-white font-bold text-xs rounded-lg flex items-center justify-center gap-1.5 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Bayar Tunai / QRIS (F8)</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Mobile Smartphone Mockup Frame (Overlaid on Bottom Right) -->
                <div class="absolute -bottom-8 -right-2 sm:-bottom-10 sm:right-6 w-44 sm:w-56 bg-slate-950 p-2 sm:p-2.5 rounded-3xl shadow-2xl border-2 border-slate-800 hidden xs:block">
                    <!-- Phone Notch & Speaker -->
                    <div class="w-16 h-3 bg-slate-800 rounded-full mx-auto mb-1.5 flex items-center justify-center">
                        <div class="w-2 h-2 rounded-full bg-slate-900"></div>
                    </div>
                    <!-- Phone Screen Content -->
                    <div class="bg-white rounded-2xl overflow-hidden p-2 text-slate-800 text-[10px]">
                        <div class="flex items-center justify-between pb-1 mb-1.5 border-b border-slate-100">
                            <span class="font-extrabold text-slate-900">FilaPOS Mobile</span>
                            <span class="text-[9px] text-lime-700 font-bold bg-lime-100 px-1 py-0.5 rounded">Kasir 1</span>
                        </div>
                        <div class="space-y-1.5">
                            <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-100 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-slate-800">Kopi Arabica</p>
                                    <p class="text-lime-700 font-extrabold text-[9px]">Rp 25.000</p>
                                </div>
                                <span class="bg-lime-600 text-white px-1.5 py-0.5 rounded font-bold text-[9px]">+ Tambah</span>
                            </div>
                            <div class="bg-slate-50 p-1.5 rounded-lg border border-slate-100 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-slate-800">Es Teh Manis</p>
                                    <p class="text-lime-700 font-extrabold text-[9px]">Rp 8.000</p>
                                </div>
                                <span class="bg-lime-600 text-white px-1.5 py-0.5 rounded font-bold text-[9px]">+ Tambah</span>
                            </div>
                        </div>
                        <div class="mt-2 pt-1 border-t border-slate-100 flex items-center justify-between bg-lime-50 p-1 rounded font-bold text-lime-900">
                            <span>2 Item</span>
                            <span>Rp 33.000 ➔</span>
                        </div>
                    </div>
                </div>

                <!-- Floating Thermal Receipt Card (Overlaid on Bottom Left) -->
                <div class="absolute -top-6 -left-2 sm:-top-8 sm:-left-6 w-48 sm:w-60 bg-white rounded-xl shadow-2xl p-3 border border-slate-200/90 font-mono text-[10px] transform -rotate-2 hover:rotate-0 transition-transform duration-300">
                    <div class="text-center pb-2 border-b border-dashed border-slate-300">
                        <div class="font-extrabold text-slate-900 tracking-wider">FILA POS SMART</div>
                        <div class="text-[9px] text-slate-600">Jl. Contoh No. 123, Kota</div>
                        <div class="text-[8px] text-slate-500 mt-0.5">Nota: #ORD-8821 • 11:35 WIB</div>
                    </div>
                    <div class="py-1.5 space-y-1 text-slate-700">
                        <div class="flex justify-between">
                            <span>1x Kopi Arabica</span>
                            <span>25.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span>1x Es Teh Manis</span>
                            <span>8.000</span>
                        </div>
                    </div>
                    <div class="pt-1.5 border-t border-dashed border-slate-300 space-y-0.5">
                        <div class="flex justify-between font-bold text-slate-900">
                            <span>TOTAL:</span>
                            <span class="text-lime-700">Rp 33.000</span>
                        </div>
                        <div class="flex justify-between text-slate-600 text-[9px]">
                            <span>TUNAI:</span>
                            <span>Rp 50.000</span>
                        </div>
                        <div class="flex justify-between text-slate-600 text-[9px]">
                            <span>KEMBALI:</span>
                            <span>Rp 17.000</span>
                        </div>
                    </div>
                    <div class="mt-2 pt-1 text-center text-[8px] text-emerald-600 font-bold bg-emerald-50 py-0.5 rounded border border-emerald-200 flex items-center justify-center gap-1">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>STRUK TERCETAK (ESC/POS)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- METRICS / VALUE STRIP                                    -->
    <!-- ======================================================== -->
    <section class="border-y border-slate-200 bg-white py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="p-4">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Rp 0,-</div>
                    <div class="mt-1 text-xs font-semibold text-lime-700 uppercase tracking-wider">Hardware Baru</div>
                    <p class="mt-1 text-xs text-slate-500">Gunakan HP / Laptop toko yang sudah ada</p>
                </div>
                <div class="p-4">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">&lt; 2 Detik</div>
                    <div class="mt-1 text-xs font-semibold text-lime-700 uppercase tracking-wider">Kecepatan Checkout</div>
                    <p class="mt-1 text-xs text-slate-500">Antrean cepat tanpa pembeli menunggu</p>
                </div>
                <div class="p-4">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">100% Cloud</div>
                    <div class="mt-1 text-xs font-semibold text-lime-700 uppercase tracking-wider">Akses Fleksibel</div>
                    <p class="mt-1 text-xs text-slate-500">Pantau penjualan dari mana saja secara real-time</p>
                </div>
                <div class="p-4">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">58 & 80 mm</div>
                    <div class="mt-1 text-xs font-semibold text-lime-700 uppercase tracking-wider">Printer Thermal</div>
                    <p class="mt-1 text-xs text-slate-500">Mendukung standar Bluetooth, USB & LAN</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- CORE FEATURES SECTION                                    -->
    <!-- ======================================================== -->
    <section id="fitur" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Fitur Lengkap Kasir</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Dirancang Khusus untuk Alur Kerja Kasir Cepat</h2>
                <p class="mt-3 text-sm text-slate-600">Semua fitur esensial yang Anda butuhkan untuk mengelola transaksi ritel, F&B, dan layanan jasa harian.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-lime-300 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-lime-50 text-lime-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Multi-Device Mobile First</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Tampilan otomatis menyesuaikan layar sentuh smartphone vertikal maupun mode layar lebar tablet/desktop kasir dengan tombol praktis.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-lime-300 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-lime-50 text-lime-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Cetak Struk Thermal ESC/POS</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Integrasi native ke printer thermal ukuran 58mm atau 80mm via USB atau Bluetooth. Format nota rapi, jelas, dan otomatis terpotong.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-lime-300 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-lime-50 text-lime-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Inventori & Kontrol Stok Akurat</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Stok berkurang otomatis setiap ada penjualan. Lengkap dengan notifikasi peringatan stok menipis agar Anda tidak kehabisan barang.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-lime-300 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-lime-50 text-lime-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Tunai, Transfer, & QRIS</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Kalkulator otomatis untuk uang diterima dan kembalian tunai. Mendukung pembayaran non-tunai yang fleksibel sesuai kebutuhan pembeli.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-lime-300 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-lime-50 text-lime-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Laporan Rekap Penjualan Cepat</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Lihat omzet harian, transaksi per kasir, dan menu produk terlaris tanpa repot menghitung nota fisik satu per satu saat tutup toko.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md hover:border-lime-300 transition-all duration-200 group">
                    <div class="w-12 h-12 rounded-xl bg-lime-50 text-lime-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Proteksi Role Kasir & Admin</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Akses kasir dibuat fokus hanya untuk input penjualan, sedangkan data modal, margin keuntungan, dan pengaturan sistem aman di tangan pemilik toko.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- INTERACTIVE COST-SAVING CALCULATOR                       -->
    <!-- ======================================================== -->
    <section id="simulasi" class="py-20 bg-white border-y border-slate-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Kalkulator Penghematan</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Hitung Penghematan Bisnis Anda Bersama FilaPOS Smart</h2>
                <p class="mt-2 text-sm text-slate-600">Lihat seberapa banyak waktu kerja dan biaya operasional yang dapat Anda pangkas dibandingkan sistem kasir konvensional.</p>
            </div>

            <!-- Calculator Card -->
            <div class="bg-slate-50 rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-lg shadow-slate-200/50">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left: Sliders Input (7 cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Slider 1: Transaksi Harian -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="txRange" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Transaksi Harian Per Outlet</label>
                                <span class="text-sm font-extrabold text-lime-700 bg-lime-100 px-2.5 py-0.5 rounded-lg">
                                    <span id="txValueText">60</span> Transaksi / hari
                                </span>
                            </div>
                            <input id="txRange" type="range" min="15" max="300" step="5" value="60" class="w-full h-2 bg-slate-200 rounded-lg cursor-pointer" oninput="updateCalculator()">
                            <div class="flex justify-between text-[11px] text-slate-600 mt-1 font-medium">
                                <span>15 (Toko Kecil)</span>
                                <span>150 (Kafe Sibuk)</span>
                                <span>300+ (Grosir/Ritel)</span>
                            </div>
                        </div>

                        <!-- Slider 2: Jumlah Kasir / Outlet -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label for="outletRange" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Jumlah Kasir / Outlet Anda</label>
                                <span class="text-sm font-extrabold text-lime-700 bg-lime-100 px-2.5 py-0.5 rounded-lg">
                                    <span id="outletValueText">1</span> Kasir / Cabang
                                </span>
                            </div>
                            <input id="outletRange" type="range" min="1" max="5" step="1" value="1" class="w-full h-2 bg-slate-200 rounded-lg cursor-pointer" oninput="updateCalculator()">
                            <div class="flex justify-between text-[11px] text-slate-600 mt-1 font-medium">
                                <span>1 Outlet</span>
                                <span>3 Outlet</span>
                                <span>5 Outlet</span>
                            </div>
                        </div>

                        <!-- Comparison Note -->
                        <div class="bg-white p-3.5 rounded-xl border border-slate-200 text-xs text-slate-600 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-lime-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>
                                Tanpa biaya sewa mesin POS mahal (Rp 300rb–500rb/bln) dan bebas selisih nota salah hitung manual setiap tutup kasir harian.
                            </span>
                        </div>
                    </div>

                    <!-- Right: Dynamic Outcome Panel (5 cols) -->
                    <div class="lg:col-span-5 bg-slate-900 rounded-2xl p-6 text-white flex flex-col justify-between space-y-6 shadow-xl">
                        <div>
                            <span class="text-[11px] uppercase tracking-wider font-bold text-lime-400">Estimasi Penghematan</span>
                            <div class="mt-2">
                                <div class="text-xs text-slate-400">Hemat Biaya Hardware & Alat Khusus:</div>
                                <div id="calcMoneySaved" class="text-3xl font-extrabold text-lime-400 tracking-tight mt-0.5">
                                    Rp 4.200.000
                                </div>
                                <div class="text-[11px] text-slate-400">per tahun / outlet</div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-800 grid grid-cols-2 gap-3">
                                <div>
                                    <div class="text-[11px] text-slate-400">Waktu Dihemat:</div>
                                    <div id="calcHoursSaved" class="text-lg font-bold text-white">45 Jam</div>
                                    <div class="text-[10px] text-slate-500">per bulan</div>
                                </div>
                                <div>
                                    <div class="text-[11px] text-slate-400">Akurasi Kasir:</div>
                                    <div class="text-lg font-bold text-emerald-400">100%</div>
                                    <div class="text-[10px] text-slate-500">Anti salah nota</div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('login') }}" class="w-full py-3 bg-lime-500 hover:bg-lime-600 active:bg-lime-700 text-slate-950 font-extrabold text-xs text-center rounded-xl transition-all shadow-md">
                            Mulai Hemat Sekarang ➔
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- 3 SIMPLE STEPS TO GET STARTED                            -->
    <!-- ======================================================== -->
    <section id="keunggulan" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Alur Mudah</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Mulai Jualan dalam 3 Langkah Praktis</h2>
                <p class="mt-2 text-sm text-slate-600">Tidak ada teknisi rumit atau waktu tunggu berminggu-minggu.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-white p-7 rounded-2xl border border-slate-200/80 relative shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-lime-500 text-white font-extrabold text-sm flex items-center justify-center mb-4">
                        01
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Buka di Browser Gadget Anda</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Akses FilaPOS Smart langsung melalui Google Chrome, Safari, atau Firefox di HP, tablet, maupun PC kasir Anda tanpa perlu download APK.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white p-7 rounded-2xl border border-slate-200/80 relative shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-lime-500 text-white font-extrabold text-sm flex items-center justify-center mb-4">
                        02
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Masukkan Produk & Harga Toko</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Kelola katalog, kategori barang, dan harga modal/jual via portal admin yang bersih dan rapi.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white p-7 rounded-2xl border border-slate-200/80 relative shadow-xs">
                    <div class="w-10 h-10 rounded-xl bg-lime-500 text-white font-extrabold text-sm flex items-center justify-center mb-4">
                        03
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Siap Transaksi & Cetak Struk</h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Klik produk, terima pembayaran pelanggan, dan cetak struk via printer thermal bluetooth/USB dalam hitungan detik.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- INTERACTIVE FAQ SECTION                                  -->
    <!-- ======================================================== -->
    <section id="faq" class="py-20 bg-white border-t border-slate-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-extrabold tracking-wider text-lime-700 uppercase bg-lime-100 px-3 py-1 rounded-full">Tanya Jawab</span>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Pertanyaan yang Sering Diajukan</h2>
                <p class="mt-2 text-sm text-slate-600">Punya pertanyaan seputar cara kerja FilaPOS Smart? Temukan jawabannya di sini.</p>
            </div>

            <!-- FAQ Accordion Container -->
            <div class="space-y-3.5">
                <!-- Item 1 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none" onclick="toggleFaq(this)">
                        <span>Apakah saya harus membeli mesin kasir atau hardware khusus?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Tidak perlu sama sekali! FilaPOS Smart 100% berbasis web (web-based). Anda bisa menggunakan smartphone Android/iOS, tablet, laptop, atau komputer kasir yang sudah Anda miliki cukup dengan membuka web browser.
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none" onclick="toggleFaq(this)">
                        <span>Printer thermal apa saja yang didukung oleh FilaPOS Smart?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        FilaPOS Smart mendukung printer kasir thermal standar industri (protokol ESC/POS) baik koneksi USB, Bluetooth (printer thermal portable/mini), maupun Network (LAN/Wi-Fi) dengan ukuran kertas 58mm dan 80mm.
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none" onclick="toggleFaq(this)">
                        <span>Apakah kasir toko bisa melihat data keuntungan pemilik?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Tidak bisa. FilaPOS Smart memiliki sistem hak akses terpisah. Pengguna dengan akun Kasir hanya dapat mengakses antarmuka transaksi penjualan kasir (POS), sedangkan harga modal, margin keuntungan, dan laporan keuangan komprehensif hanya dapat dibuka oleh akun Administrator toko.
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none" onclick="toggleFaq(this)">
                        <span>Bagaimana jika saya memiliki lebih dari satu kasir atau cabang?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Sistem mendukung multi-kasir. Anda dapat membuat beberapa akun kasir untuk melayani transaksi secara bersamaan dari berbagai perangkat berbeda, dengan data stok dan penjualan tersinkronisasi langsung ke sistem pusat.
                    </div>
                </div>

                <!-- Item 5 -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/50">
                    <button type="button" class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm focus:outline-none" onclick="toggleFaq(this)">
                        <span>Bagaimana cara mulai menggunakan FilaPOS Smart?</span>
                        <svg class="w-4 h-4 text-slate-500 shrink-0 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="px-5 pb-4 text-xs text-slate-600 leading-relaxed hidden">
                        Sangat mudah! Anda dapat langsung mengklik tombol "Buka Kasir POS" untuk login ke terminal kasir, atau mengakses portal admin untuk mengelola katalog produk, stok, dan pengaturan toko Anda.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- FINAL HIGH-CONVERSION CTA                                -->
    <!-- ======================================================== -->
    <section class="py-16 bg-slate-900 text-white relative overflow-hidden">
        <!-- Glow gradient -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-lime-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Tingkatkan Kecepatan Kasir Toko Anda Mulai Hari Ini
            </h2>
            <p class="mt-4 text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-normal">
                Beralih ke sistem kasir web-based yang fleksibel, tanpa biaya hardware mahal, dan siap cetak nota kapan pun dibutuhkan.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 text-sm font-bold text-slate-950 bg-lime-400 hover:bg-lime-300 active:bg-lime-500 rounded-xl shadow-lg shadow-lime-400/20 transition-all duration-200">
                    <span>Buka Aplikasi Kasir Sekarang</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
                <a href="{{ url('/admin') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-all">
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
                    <div class="w-8 h-8 rounded-lg bg-lime-500 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <span class="text-base font-extrabold tracking-tight text-slate-900">Fila<span class="text-lime-600">POS</span> Smart</span>
                    <span class="text-xs text-slate-400 pl-2 border-l border-slate-200">Point of Sale Digital Modern</span>
                </div>

                <!-- Footer Nav Links -->
                <div class="flex flex-wrap items-center justify-center gap-6 text-xs text-slate-600">
                    <a href="#fitur" class="hover:text-lime-600 transition-colors">Fitur</a>
                    <a href="#keunggulan" class="hover:text-lime-600 transition-colors">Keunggulan</a>
                    <a href="#simulasi" class="hover:text-lime-600 transition-colors">Simulasi</a>
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



        // Interactive Cost Calculator
        function updateCalculator() {
            const tx = parseInt(document.getElementById('txRange').value, 10);
            const outlets = parseInt(document.getElementById('outletRange').value, 10);

            document.getElementById('txValueText').innerText = tx;
            document.getElementById('outletValueText').innerText = outlets;

            // Calculations:
            // Hardware savings: Conventional POS lease/hardware is ~Rp 350.000/month/outlet => Rp 4.200.000/yr/outlet
            const moneySaved = outlets * 4200000;
            // Time saved: ~15 seconds per manual transaction calculation & receipt write => in hours/month
            const minutesSavedDaily = (tx * 0.4) * outlets;
            const hoursSavedMonthly = Math.round((minutesSavedDaily * 30) / 60);

            // Format IDR
            const formattedMoney = 'Rp ' + moneySaved.toLocaleString('id-ID');
            document.getElementById('calcMoneySaved').innerText = formattedMoney;
            document.getElementById('calcHoursSaved').innerText = hoursSavedMonthly + ' Jam';
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
                icon.classList.add('rotate-180');
            }
        }

        // Initialize Calculator on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCalculator();
        });
    </script>
</body>

</html>
