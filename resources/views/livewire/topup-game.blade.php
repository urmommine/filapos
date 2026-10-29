<div x-data="topupWizard({
        allGames: {{ Js::from($allGames) }},
        catalog: {{ Js::from($catalog) }},
        initialGame: '{{ $selectedGame }}'
    })" 
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Clean Page Title & Status -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-6 border-b border-slate-200/90 gap-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Top Up Game & Voucher Digital
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Pilih game, masukkan ID akun, dan bayar instan via Saldo atau QRIS.
            </p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/80 text-xs font-bold text-emerald-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Server H2H Online</span>
            </span>
        </div>
    </div>

    <!-- Main Two-Column Layout (Left: Brand Selector & Grid | Right: Instant Wizard Panel) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- ======================================================== -->
        <!-- LEFT COLUMN: BRAND SELECTION (Dropdown + Categories + Grid) -->
        <!-- ======================================================== -->
        <div class="lg:col-span-7 xl:col-span-7 space-y-4">

            <!-- 1. Searchable Combobox Dropdown: "Select Brand" (Red Box in Reference Image 3) -->
            <div class="relative z-30" @click.outside="dropdownOpen = false">
                <button @click="dropdownOpen = !dropdownOpen" type="button" 
                        class="w-full flex items-center justify-between px-4 py-3 bg-white border border-slate-200 hover:border-lime-500 focus:border-lime-500 focus:ring-2 focus:ring-lime-500/20 rounded-2xl text-left transition-all shadow-xs cursor-pointer">
                    <div class="flex items-center gap-3 truncate">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pilih Brand</span>
                        <span class="text-slate-300">|</span>
                        <div class="flex items-center gap-2 truncate">
                            <template x-if="selectedGame">
                                <div class="flex items-center gap-2 truncate">
                                    <div class="w-6 h-6 rounded-lg overflow-hidden shrink-0 border border-slate-200 bg-slate-100 flex items-center justify-center">
                                        <template x-if="currentGameData.brand && currentGameData.brand.thumbnail">
                                            <img :src="currentGameData.brand.thumbnail" :alt="currentGameData.brand.name" width="24" height="24" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!currentGameData.brand || !currentGameData.brand.thumbnail">
                                            <span class="w-2 h-2 rounded-full bg-lime-500 shrink-0"></span>
                                        </template>
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 truncate" x-text="currentGameData.brand ? currentGameData.brand.name : selectedGame"></span>
                                </div>
                            </template>
                            <template x-if="!selectedGame">
                                <span class="text-sm text-slate-400">Pilih salah satu game atau brand...</span>
                            </template>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Popover List (Matching User Image 3 - Instant 0ms filter) -->
                <div x-show="dropdownOpen" x-cloak 
                     class="absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl z-50 p-2 space-y-1 max-h-80 overflow-y-auto">
                    <!-- Search Input Inside Dropdown -->
                    <div class="p-1 sticky top-0 bg-white border-b border-slate-100 pb-2 z-10">
                        <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus-within:bg-white focus-within:border-lime-500 transition-all">
                            <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
                            <input x-model="dropdownSearch" type="text" placeholder="Search options..." 
                                   class="w-full bg-transparent text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none">
                            <button x-show="dropdownSearch.length > 0" @click="dropdownSearch = ''" type="button" class="text-slate-400 hover:text-slate-600">
                                <span class="material-symbols-outlined text-base">close</span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between px-1 pt-1.5 text-[10px] text-slate-400">
                            <span>Menampilkan <strong class="text-slate-700" x-text="dropdownFilteredGames.length"></strong> brand</span>
                            <span x-show="dropdownSearch">Hasil filter</span>
                        </div>
                    </div>

                    <!-- Options List (Seluruh Brand Termuat) -->
                    <div class="py-1 space-y-0.5">
                        <template x-for="g in dropdownFilteredGames" :key="g.slug">
                            <button @click="selectGame(g.slug); dropdownOpen = false" type="button"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-left transition-colors cursor-pointer"
                                    :class="selectedGame === g.slug ? 'bg-lime-50 text-lime-950 font-bold border border-lime-200' : 'hover:bg-slate-50 text-slate-800'">
                                <div class="flex items-center gap-2.5 truncate">
                                    <div class="w-7 h-7 rounded-lg overflow-hidden bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 border border-slate-200/80">
                                        <template x-if="g.thumbnail">
                                            <img :src="g.thumbnail" :alt="g.name" loading="lazy" decoding="async" width="28" height="28" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!g.thumbnail">
                                            <span class="text-[9px] font-black uppercase text-slate-600" x-text="g.name ? g.name.substring(0, 2) : 'GP'"></span>
                                        </template>
                                    </div>
                                    <span class="text-xs truncate font-semibold" x-text="g.name"></span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium px-2 py-0.5 rounded bg-slate-100" x-text="g.category || 'Game'"></span>
                            </button>
                        </template>
                        <div x-show="dropdownFilteredGames.length === 0" class="py-4 text-center text-xs text-slate-400">
                            Tidak ada brand yang cocok
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Category Filter Pills (Instant 0ms client-side filter) -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-xs">
                @foreach($categories as $key => $label)
                    <button @click="setCategory('{{ $key }}')" type="button"
                            class="px-3.5 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all cursor-pointer border"
                            :class="activeCategory === '{{ $key }}' 
                                ? 'bg-lime-500 border-lime-500 text-slate-950 shadow-xs' 
                                : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50'">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Mobile Active Brand Banner (Shown on mobile when a game is selected) -->
            <div x-show="selectedGame" x-cloak
                 class="lg:hidden bg-white border border-slate-200/90 rounded-2xl p-3 sm:p-3.5 shadow-xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 truncate">
                    <div class="relative w-10 h-10 rounded-xl overflow-hidden shrink-0 border border-slate-200/90 bg-slate-100 flex items-center justify-center">
                        <template x-if="currentGameData.brand && currentGameData.brand.thumbnail">
                            <img :src="currentGameData.brand.thumbnail" :alt="currentGameData.brand.name" width="40" height="40" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!currentGameData.brand || !currentGameData.brand.thumbnail">
                            <span class="text-xs font-black uppercase text-slate-700" x-text="selectedGame.substring(0, 2).toUpperCase()"></span>
                        </template>
                    </div>
                    <div class="truncate">
                        <span class="text-[10px] font-bold text-lime-700 bg-lime-50 px-1.5 py-0.5 rounded border border-lime-200">Game Terpilih</span>
                        <h3 class="text-xs font-extrabold text-slate-900 truncate mt-0.5" x-text="currentGameData.brand ? currentGameData.brand.name : selectedGame"></h3>
                    </div>
                </div>

                <button @click="mobileBrandGridOpen = !mobileBrandGridOpen" type="button"
                        class="shrink-0 px-3 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
                        :class="mobileBrandGridOpen ? 'bg-slate-900 text-white border-slate-900' : 'bg-lime-500 hover:bg-lime-600 text-slate-950 border-lime-500 shadow-2xs'">
                    <span class="material-symbols-outlined text-base" x-text="mobileBrandGridOpen ? 'close' : 'grid_view'"></span>
                    <span x-text="mobileBrandGridOpen ? 'Tutup Grid' : 'Ganti Brand'"></span>
                </button>
            </div>

            <!-- 3. Brand Cards Grid with Clean SVG Icons (Instant 0ms filter & select) -->
            <div :class="(mobileBrandGridOpen || !selectedGame) ? 'block' : 'hidden lg:block'"
                 class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs space-y-3 transition-all">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 pb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-800">
                            Daftar Brand (<span x-text="filteredGames.length"></span>)
                        </span>
                        <span class="text-slate-300">|</span>
                        <span class="text-[11px] text-slate-400">Hal <span x-text="brandPage"></span>/<span x-text="totalBrandPages"></span></span>
                    </div>

                    <!-- Instant Search Bar for Brands -->
                    <div class="relative w-full sm:w-56">
                        <span class="material-symbols-outlined absolute left-2.5 top-2 text-slate-400 text-sm">search</span>
                        <input x-model="searchQuery" @input="brandPage = 1" type="text" placeholder="Cari nama brand / game..." 
                               class="w-full pl-7 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-lime-500 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Grid Paginated Brands (Maksimal 10 row) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="game in paginatedGames" :key="game.slug">
                        <button @click="selectGame(game.slug)" type="button"
                                class="group relative flex flex-col items-center justify-center p-3.5 rounded-2xl border text-center transition-all duration-150 cursor-pointer"
                                :class="selectedGame === game.slug 
                                    ? 'bg-lime-50/70 border-2 border-lime-500 shadow-xs ring-2 ring-lime-500/20' 
                                    : 'bg-slate-50/60 hover:bg-white border-slate-200 hover:border-lime-400 hover:shadow-xs'">
                            
                            <!-- Optimized Official Brand Icon (0ms Local WebP + Lazy + Zero-CLS Fallback) -->
                            <div class="relative w-12 h-12 rounded-2xl overflow-hidden mb-2.5 transition-transform duration-200 group-hover:scale-105 border shrink-0 flex items-center justify-center bg-slate-900"
                                 :class="selectedGame === game.slug ? 'border-lime-500 ring-2 ring-lime-500/30 shadow-xs' : 'border-slate-200/90 shadow-2xs'"
                                 x-data="{ imgLoaded: false, imgError: false }">
                                
                                <!-- Monogram Fallback: Muncul instan saat loading atau jika tidak ada icon -->
                                <div x-show="!imgLoaded || imgError || !game.thumbnail"
                                     class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 text-lime-400 font-black text-xs uppercase tracking-wider select-none">
                                    <span x-text="game.name ? game.name.substring(0, 2) : 'GP'"></span>
                                </div>

                                <!-- Official WebP Game Logo -->
                                <template x-if="game.thumbnail">
                                    <img :src="game.thumbnail" 
                                         :alt="game.name" 
                                         loading="lazy" 
                                         decoding="async" 
                                         width="48" 
                                         height="48" 
                                         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-200"
                                         :class="imgLoaded && !imgError ? 'opacity-100' : 'opacity-0'"
                                         x-on:load="imgLoaded = true"
                                         x-on:error="imgError = true">
                                </template>
                            </div>

                            <span class="w-full text-xs font-bold text-slate-900 truncate group-hover:text-lime-700 transition-colors" x-text="game.name"></span>
                            <span class="text-[10px] text-slate-400 truncate w-full mt-0.5" x-text="game.publisher || game.category"></span>

                            <template x-if="selectedGame === game.slug">
                                <div class="absolute top-2 right-2 w-4 h-4 rounded-full bg-lime-500 text-slate-950 flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </template>
                        </button>
                    </template>
                </div>

                <div x-show="paginatedGames.length === 0" class="py-8 text-center text-xs text-slate-400">
                    Tidak ada brand yang ditemukan.
                </div>

                <!-- Brand Pagination Bar (Maksimal 10 row per halaman) -->
                <div x-show="totalBrandPages > 1" class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                    <span class="text-[11px] text-slate-500">
                        Item <span class="font-bold text-slate-800" x-text="((brandPage - 1) * brandsPerPage) + 1"></span>-<span class="font-bold text-slate-800" x-text="Math.min(brandPage * brandsPerPage, filteredGames.length)"></span> dari <span class="font-bold text-slate-800" x-text="filteredGames.length"></span>
                    </span>
                    <div class="flex items-center gap-1.5">
                        <button @click="prevBrandPage()" :disabled="brandPage === 1" type="button"
                                class="px-2.5 py-1 rounded-lg border text-xs font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-100 bg-white border-slate-200 text-slate-700 cursor-pointer">
                            &larr; Prev
                        </button>
                        <span class="px-2 text-xs font-bold text-slate-800">
                            <span x-text="brandPage"></span> / <span x-text="totalBrandPages"></span>
                        </span>
                        <button @click="nextBrandPage()" :disabled="brandPage === totalBrandPages" type="button"
                                class="px-2.5 py-1 rounded-lg border text-xs font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-100 bg-white border-slate-200 text-slate-700 cursor-pointer">
                            Next &rarr;
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- RIGHT COLUMN: THE WIZARD STEP / CART PANEL               -->
        <!-- (Instant 0ms Step transitions via Alpine.js)             -->
        <!-- ======================================================== -->
        <div id="topup-wizard-panel" class="lg:col-span-5 xl:col-span-5 scroll-mt-20">

            <!-- EMPTY STATE: MATCHING REFERENCE IMAGE 2 & 3 -->
            <div x-show="!selectedGame" 
                 class="bg-white border border-slate-200/90 rounded-2xl p-8 sm:p-12 text-center flex flex-col items-center justify-center min-h-[420px] shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Pilih Produk & Brand</h3>
                <p class="text-xs text-slate-500 max-w-xs mt-1.5 leading-relaxed">
                    Select a product to enter the target and review your order.
                </p>
            </div>

            <!-- ACTIVE WIZARD STEP CARD (Instant 0ms Reactive Flow) -->
            <div x-show="selectedGame" 
                 class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden sticky top-24">
                
                <!-- Wizard Header -->
                <div class="p-4 sm:p-5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                    <div class="flex items-center gap-2.5 truncate">
                        <div class="relative w-9 h-9 rounded-xl overflow-hidden shrink-0 border border-slate-200/90 shadow-2xs flex items-center justify-center bg-slate-100">
                            <template x-if="currentGameData.brand && currentGameData.brand.thumbnail">
                                <img :src="currentGameData.brand.thumbnail" :alt="currentGameData.brand.name" width="36" height="36" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!currentGameData.brand || !currentGameData.brand.thumbnail">
                                <div class="w-full h-full bg-lime-500 text-slate-950 flex items-center justify-center font-black text-xs uppercase" x-text="selectedGame.substring(0, 2).toUpperCase()"></div>
                            </template>
                        </div>
                        <div class="truncate">
                            <h3 class="text-sm font-extrabold text-slate-900 truncate" x-text="currentGameData.brand ? currentGameData.brand.name : selectedGame"></h3>
                            <p class="text-[11px] text-slate-500">Order Wizard</p>
                        </div>
                    </div>

                    <!-- Reset / Change Game -->
                    <button @click="selectedGame = ''; mobileBrandGridOpen = true" type="button" class="text-xs text-slate-400 hover:text-slate-700 font-semibold flex items-center gap-1 cursor-pointer">
                        <span>Ganti</span>
                        <span class="material-symbols-outlined text-sm">swap_horiz</span>
                    </button>
                </div>

                <!-- Wizard Stepper Navigation Bar -->
                <div class="px-4 py-2.5 bg-white border-b border-slate-100 flex items-center justify-between text-xs">
                    <!-- Step 1 -->
                    <button @click="goToStep(1)" type="button" 
                            class="flex items-center gap-1.5 font-bold cursor-pointer"
                            :class="wizardStep === 1 ? 'text-lime-700' : (wizardStep > 1 ? 'text-slate-900' : 'text-slate-400')">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]"
                              :class="wizardStep === 1 ? 'bg-lime-500 text-slate-950 font-black' : (wizardStep > 1 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-600')">
                            1
                        </span>
                        <span>Nominal</span>
                    </button>

                    <span class="text-slate-300">→</span>

                    <!-- Step 2 -->
                    <button @click="goToStep(2)" type="button" 
                            class="flex items-center gap-1.5 font-bold cursor-pointer"
                            :class="wizardStep === 2 ? 'text-lime-700' : (wizardStep > 2 ? 'text-slate-900' : 'text-slate-400')">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]"
                              :class="wizardStep === 2 ? 'bg-lime-500 text-slate-950 font-black' : (wizardStep > 2 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-600')">
                            2
                        </span>
                        <span>Akun Target</span>
                    </button>

                    <span class="text-slate-300">→</span>

                    <!-- Step 3 -->
                    <button @click="goToStep(3)" type="button" 
                            class="flex items-center gap-1.5 font-bold cursor-pointer"
                            :class="wizardStep === 3 ? 'text-lime-700' : 'text-slate-400'">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]"
                              :class="wizardStep === 3 ? 'bg-lime-500 text-slate-950 font-black' : 'bg-slate-200 text-slate-600'">
                            3
                        </span>
                        <span>Bayar</span>
                    </button>
                </div>

                <!-- WIZARD STEP BODY -->
                <div class="p-4 sm:p-5">

                    <!-- ============================================== -->
                    <!-- STEP 1: PILIH DENOMINASI / PRODUK (Instant)    -->
                    <!-- ============================================== -->
                    <div x-show="wizardStep === 1" class="space-y-3.5">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Pilih Paket Produk
                            </h4>
                            <span class="text-[11px] text-slate-400">Aturan min Rp 5.000</span>
                        </div>

                        <!-- Filter Controls: Search, Product Type, Server, Sort -->
                        <div class="space-y-2">
                            <!-- Quick Search Input -->
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-2.5 top-2 text-slate-400 text-base">search</span>
                                <input x-model="productSearch" @input="productPage = 1" type="text" placeholder="Cari nominal / nama paket..."
                                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-lime-500 focus:outline-none transition-all shadow-2xs">
                            </div>

                            <!-- Dropdown Filters Row -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <!-- 1. Product Type Filter -->
                                <div class="relative">
                                    <select x-model="productTypeFilter" @change="productPage = 1"
                                            style="background-image: none !important;"
                                            class="w-full pl-3 pr-8 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-lime-500 focus:ring-1 focus:ring-lime-500/20 focus:outline-none font-medium text-slate-800 transition-all shadow-2xs appearance-none">
                                        <option value="ALL">Semua Tipe</option>
                                        <template x-for="t in availableProductTypes" :key="t">
                                            <option :value="t" x-text="t"></option>
                                        </template>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base leading-none">expand_more</span>
                                </div>

                                <!-- 2. Server Filter (Aktif dinamis jika ada server) -->
                                <div class="relative">
                                    <select x-model="serverFilter" @change="productPage = 1" :disabled="availableServers.length <= 1"
                                            style="background-image: none !important;"
                                            class="w-full pl-3 pr-8 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-lime-500 focus:ring-1 focus:ring-lime-500/20 focus:outline-none font-medium text-slate-800 transition-all shadow-2xs appearance-none disabled:opacity-50 disabled:bg-slate-50">
                                        <option value="ALL" x-text="availableServers.length > 1 ? 'Semua Server' : (availableServers[0] || 'Global Server')"></option>
                                        <template x-if="availableServers.length > 1">
                                            <template x-for="s in availableServers" :key="s">
                                                <option :value="s" x-text="s"></option>
                                            </template>
                                        </template>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base leading-none">expand_more</span>
                                </div>

                                <!-- 3. Sorting Dropdown -->
                                <div class="relative">
                                    <select x-model="productSort" @change="productPage = 1"
                                            style="background-image: none !important;"
                                            class="w-full pl-3 pr-8 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:border-lime-500 focus:ring-1 focus:ring-lime-500/20 focus:outline-none font-bold text-slate-800 transition-all shadow-2xs appearance-none">
                                        <option value="price_asc">Harga Terendah</option>
                                        <option value="price_desc">Harga Tertinggi</option>
                                        <option value="name_asc">Nama A &rarr; Z</option>
                                        <option value="name_desc">Nama Z &rarr; A</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base leading-none">swap_vert</span>
                                </div>
                            </div>

                            <!-- Filter Reset & Info Bar -->
                            <div x-show="productTypeFilter !== 'ALL' || serverFilter !== 'ALL' || productSearch" x-cloak
                                 class="flex items-center justify-between text-[11px] px-1 text-slate-500 pt-0.5">
                                <span>Menampilkan <strong class="text-slate-800" x-text="filteredProducts.length"></strong> paket produk</span>
                                <button @click="productTypeFilter = 'ALL'; serverFilter = 'ALL'; productSearch = ''; productPage = 1" type="button"
                                        class="text-lime-700 hover:text-lime-800 font-bold underline cursor-pointer">
                                    Reset Filter
                                </button>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div x-show="isLoadingProducts" class="py-12 flex flex-col items-center justify-center text-center bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                            <div class="w-7 h-7 border-3 border-lime-500 border-t-transparent rounded-full animate-spin mb-2"></div>
                            <p class="text-xs font-semibold text-slate-700">Memuat data...</p>
                            <span class="text-[10px] text-slate-400">Sinkronisasi data katalog H2H</span>
                        </div>

                        <!-- Product Grid (Tepat 10 row per halaman = 20 item) -->
                        <div x-show="!isLoadingProducts" class="grid grid-cols-2 gap-2.5 max-h-[460px] overflow-y-auto pr-1">
                            <template x-for="product in paginatedProducts" :key="product.sku_code">
                                <button @click="selectSku(product.sku_code)" type="button"
                                        class="p-3 rounded-xl border text-left transition-all duration-150 flex flex-col justify-between cursor-pointer"
                                        :class="selectedSku === product.sku_code 
                                            ? 'bg-lime-50/80 border-2 border-lime-500 shadow-2xs ring-2 ring-lime-500/20' 
                                            : 'bg-slate-50/60 hover:bg-white border-slate-200 hover:border-lime-400'">
                                    <div>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase px-1.5 py-0.5 rounded bg-white border border-slate-200" x-text="product.type || 'Item'"></span>
                                        <h5 class="text-xs font-bold text-slate-900 mt-1.5 leading-snug" x-text="product.name"></h5>
                                    </div>
                                    <div class="mt-2.5 pt-1.5 border-t border-slate-200/60 flex items-center justify-between">
                                        <span class="text-xs font-black text-lime-700 font-mono" x-text="product.formatted_price"></span>
                                        <template x-if="selectedSku === product.sku_code">
                                            <span class="w-3.5 h-3.5 rounded-full bg-lime-500 text-slate-950 flex items-center justify-center">
                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </span>
                                        </template>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <div x-show="!isLoadingProducts && paginatedProducts.length === 0" class="py-8 text-center text-xs text-slate-400">
                            Tidak ada paket produk yang cocok.
                        </div>

                        <!-- Product Pagination Bar (Tepat 10 row per halaman) -->
                        <div x-show="totalProductPages > 1 && !isLoadingProducts" class="flex items-center justify-between pt-2.5 border-t border-slate-100 text-xs">
                            <span class="text-[11px] text-slate-500">
                                Baris <span class="font-bold text-slate-800" x-text="((productPage - 1) * 10) + 1"></span>-<span class="font-bold text-slate-800" x-text="Math.min(productPage * 10, Math.ceil(filteredProducts.length / 2))"></span> (<span x-text="filteredProducts.length"></span> item)
                            </span>
                            <div class="flex items-center gap-1.5">
                                <button @click="prevProductPage()" :disabled="productPage === 1" type="button"
                                        class="px-2.5 py-1 rounded-lg border text-xs font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-100 bg-white border-slate-200 text-slate-700 cursor-pointer">
                                    &larr; Prev
                                </button>
                                <span class="px-2 text-xs font-bold text-slate-800">
                                    Hal <span x-text="productPage"></span> / <span x-text="totalProductPages"></span>
                                </span>
                                <button @click="nextProductPage()" :disabled="productPage === totalProductPages" type="button"
                                        class="px-2.5 py-1 rounded-lg border text-xs font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed hover:bg-slate-100 bg-white border-slate-200 text-slate-700 cursor-pointer">
                                    Next &rarr;
                                </button>
                            </div>
                        </div>

                        <!-- Action Next Step & Selected Item Preview -->
                        <div id="step-1-action" class="mt-4 pt-3 border-t border-slate-100 scroll-mt-24 space-y-2.5">
                            <!-- Selected Item Feedback Chip -->
                            <template x-if="selectedProduct">
                                <div class="p-3 rounded-xl bg-lime-50 border border-lime-300 flex items-center justify-between transition-all">
                                    <div class="truncate">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-lime-600 animate-pulse"></span>
                                            <span class="text-[10px] font-bold text-lime-900 uppercase tracking-wider">Item Terpilih</span>
                                        </div>
                                        <p class="text-xs font-black text-slate-900 truncate mt-0.5" x-text="selectedProduct.name"></p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-black text-lime-800 font-mono" x-text="selectedProduct.formatted_price"></span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selectedProduct">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                                    <p class="text-xs text-slate-500 font-medium">Silakan klik salah satu nominal paket di atas untuk lanjut</p>
                                </div>
                            </template>

                            <button @click="goToStep(2)" type="button"
                                    :disabled="!selectedProduct"
                                    class="w-full py-3.5 rounded-xl font-bold text-xs tracking-wide shadow-xs transition-all flex items-center justify-center gap-1.5"
                                    :class="selectedProduct ? 'bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'">
                                <span>Lanjut: Masukkan Data Akun</span>
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </button>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- STEP 2: MASUKKAN DATA TARGET AKUN              -->
                    <!-- ============================================== -->
                    <div x-show="wizardStep === 2" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Data Akun Player
                            </h4>
                            <span class="text-[11px] text-slate-400">Pastikan ID benar</span>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    <span x-text="targetLabel"></span> <span class="text-red-500">*</span>
                                </label>
                                <input id="input-target-account" x-model="target" type="text" :placeholder="targetPlaceholder" 
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 font-mono focus:outline-none focus:border-lime-500 focus:ring-2 focus:ring-lime-500/20 transition-all">
                            </div>

                            <div x-show="needsZone" x-cloak>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    <span x-text="zoneLabel"></span> <span class="text-red-500">*</span>
                                </label>
                                <input x-model="zone" type="text" :placeholder="zonePlaceholder" 
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 font-mono focus:outline-none focus:border-lime-500 focus:ring-2 focus:ring-lime-500/20 transition-all">
                                <p x-show="selectedGame.includes('mobile-legends')" class="text-[10px] text-slate-400 mt-1">
                                    Untuk Mobile Legends, masukkan 4-5 digit angka Zone ID yang berada di dalam tanda kurung, misal (2045)
                                </p>
                            </div>

                            <!-- Bantuan Cek Username (Halaman Terpisah) -->
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/90 flex items-center justify-between text-xs text-slate-600">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lime-600 text-lg">help_outline</span>
                                    <span>Ingin memastikan nama player?</span>
                                </div>
                                <a :href="'{{ route('topups.check-username') }}?game=' + selectedGame + '&target=' + encodeURIComponent(target) + '&zone=' + encodeURIComponent(zone)"
                                   target="_blank"
                                   class="font-bold text-lime-700 hover:text-lime-800 underline flex items-center gap-0.5">
                                    <span>Cek Username</span>
                                    <span class="material-symbols-outlined text-xs">open_in_new</span>
                                </a>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    No. WhatsApp / HP (Opsional)
                                </label>
                                <input x-model="customerPhone" type="text" placeholder="08xxxxxxxxxx" 
                                       class="w-full px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-xs text-slate-900 font-mono focus:outline-none focus:border-lime-500">
                                <p class="text-[10px] text-slate-400 mt-0.5">Untuk bukti struk transaksi</p>
                            </div>
                        </div>

                        <!-- Back & Next Buttons -->
                        <div class="flex items-center gap-2 pt-2">
                            <button @click="goToStep(1)" type="button" 
                                    class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                                ← Kembali
                            </button>
                            <button @click="goToStep(3)" type="button" 
                                    class="flex-1 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs tracking-wide shadow-xs transition-colors flex items-center justify-center gap-1 cursor-pointer">
                                <span>Lanjut: Konfirmasi & Bayar</span>
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </button>
                        </div>
                    </div>

                    <!-- ============================================== -->
                    <!-- STEP 3: REVIEW & PEMBAYARAN QRIS               -->
                    <!-- ============================================== -->
                    <div x-show="wizardStep === 3" class="space-y-4">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Review & Konfirmasi Pesanan
                        </h4>

                        <!-- Table Breakdown -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2.5">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Game / Brand</span>
                                <div class="flex items-center gap-1.5 truncate">
                                    <template x-if="currentGameData.brand && currentGameData.brand.thumbnail">
                                        <img :src="currentGameData.brand.thumbnail" :alt="currentGameData.brand.name" width="20" height="20" class="w-5 h-5 rounded-md object-cover border border-slate-200">
                                    </template>
                                    <span class="font-bold text-slate-900 truncate" x-text="currentGameData.brand ? currentGameData.brand.name : selectedGame"></span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Paket Item</span>
                                <span class="font-bold text-slate-900" x-text="selectedProduct ? selectedProduct.name : '-'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500" x-text="targetLabel"></span>
                                <span class="font-mono font-semibold text-slate-900" x-text="target || '-'"></span>
                            </div>
                            <template x-if="needsZone && zone">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500" x-text="zoneLabel"></span>
                                    <span class="font-mono font-semibold text-slate-900" x-text="zone"></span>
                                </div>
                            </template>
                            <template x-if="nickname">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Nickname</span>
                                    <span class="font-bold text-lime-700" x-text="nickname"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Pilihan Metode Pembayaran (Saldo vs QRIS) -->
                        <div class="space-y-2 pt-2 border-t border-slate-200/80">
                            <label class="block text-xs font-bold text-slate-700">Pilih Metode Pembayaran</label>
                            
                            @auth
                                <!-- Opsi 1: Saldo Akun -->
                                <div @click="selectPaymentMethod('balance')"
                                     class="p-3 rounded-2xl border transition-all cursor-pointer flex items-center justify-between"
                                     :class="selectedPaymentMethod === 'balance' ? 'bg-lime-50 border-lime-500 ring-2 ring-lime-500/20' : 'bg-white hover:bg-slate-50 border-slate-200'">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-lime-400 flex items-center justify-center font-bold">
                                            <span class="material-symbols-outlined text-lg">account_balance_wallet</span>
                                        </div>
                                        <div>
                                            <div class="text-xs font-extrabold text-slate-900">Saldo Akun (Instan 1-Detik)</div>
                                            <div class="text-[11px] font-mono text-slate-500">
                                                Tersedia: <strong class="text-slate-900 font-mono">Rp <span x-text="new Intl.NumberFormat('id-ID').format(userBalance)"></span></strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border flex items-center justify-center"
                                         :class="selectedPaymentMethod === 'balance' ? 'border-lime-500 bg-lime-500 text-slate-950' : 'border-slate-300'">
                                        <template x-if="selectedPaymentMethod === 'balance'">
                                            <span class="material-symbols-outlined text-xs font-bold">check</span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Warning jika saldo kurang saat opsi Saldo dipilih -->
                                <template x-if="selectedPaymentMethod === 'balance' && selectedProduct && userBalance < selectedProduct.price">
                                    <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 flex items-center justify-between">
                                        <span>Saldo tidak cukup (Kurang Rp <span x-text="new Intl.NumberFormat('id-ID').format(selectedProduct.price - userBalance)"></span>)</span>
                                        <a href="{{ route('topups.account') }}" target="_blank" class="font-bold underline text-amber-900 hover:text-amber-950">Isi Saldo</a>
                                    </div>
                                </template>
                            @else
                                <!-- Guest Banner: Info Login -->
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between">
                                    <span>Punya akun member? Bayar instan tanpa scan.</span>
                                    <a href="{{ route('topups.login') }}" class="font-bold text-lime-700 hover:text-lime-800 underline">Masuk</a>
                                </div>
                            @endauth

                            <!-- Opsi 2: QRIS BorderPay -->
                            <div @click="selectPaymentMethod('qris')"
                                 class="p-3 rounded-2xl border transition-all cursor-pointer flex items-center justify-between"
                                 :class="selectedPaymentMethod === 'qris' ? 'bg-lime-50 border-lime-500 ring-2 ring-lime-500/20' : 'bg-white hover:bg-slate-50 border-slate-200'">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-lime-500 text-slate-950 flex items-center justify-center font-bold">
                                        <span class="material-symbols-outlined text-lg">qr_code_2</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-extrabold text-slate-900">QRIS BorderPay (Langsung)</div>
                                        <div class="text-[11px] text-slate-500">GoPay, OVO, DANA, BCA, Mandiri, dll</div>
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded-full border flex items-center justify-center"
                                     :class="selectedPaymentMethod === 'qris' ? 'border-lime-500 bg-lime-500 text-slate-950' : 'border-slate-300'">
                                    <template x-if="selectedPaymentMethod === 'qris'">
                                        <span class="material-symbols-outlined text-xs font-bold">check</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Total Harga -->
                        <div class="py-2 flex justify-between items-baseline border-b border-slate-100">
                            <span class="text-xs font-semibold text-slate-500">Total Pembayaran</span>
                            <span class="text-2xl font-black text-slate-900 font-mono" x-text="selectedProduct ? selectedProduct.formatted_price : 'Rp 0'"></span>
                        </div>

                        <!-- Checkout Button -->
                        <div id="submit-payment-action" class="space-y-2 scroll-mt-24">
                            <button id="btn-submit-payment" @click="submitPayment()" type="button"
                                    :disabled="isSubmitting"
                                    class="w-full py-3.5 rounded-xl font-extrabold text-sm shadow-md transition-all flex items-center justify-center gap-2"
                                    :class="isSubmitting 
                                        ? 'bg-slate-300 text-slate-500 cursor-wait' 
                                        : 'bg-lime-500 hover:bg-lime-600 active:bg-lime-700 text-slate-950 shadow-lime-500/25 cursor-pointer'">
                                <template x-if="isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <span class="w-4 h-4 border-2 border-slate-900 border-t-transparent rounded-full animate-spin"></span>
                                        <span>Memproses Transaksi H2H...</span>
                                    </span>
                                </template>
                                <template x-if="!isSubmitting && selectedPaymentMethod === 'balance'">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-xl">bolt</span>
                                        <span>Bayar Pakai Saldo (Instan)</span>
                                    </span>
                                </template>
                                <template x-if="!isSubmitting && selectedPaymentMethod !== 'balance'">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-xl">qr_code_scanner</span>
                                        <span>Bayar Sekarang (QRIS)</span>
                                    </span>
                                </template>
                            </button>

                            <button @click="goToStep(2)" type="button"
                                    class="w-full py-2 text-xs text-slate-500 hover:text-slate-800 font-semibold cursor-pointer">
                                ← Ubah Data Akun
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- ======================================================== -->
    <!-- MODAL POPUP QRIS BORDERPAY                               -->
    <!-- ======================================================== -->
    @if($showQrisModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs"
             wire:poll.5s="checkPaymentStatus">
            
            <div class="relative w-full max-w-md rounded-2xl bg-white border border-slate-200 shadow-2xl p-6 sm:p-7 text-center overflow-hidden">
                
                <!-- Close Button -->
                <button wire:click="closeQrisModal" type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-1 cursor-pointer">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>

                @if($topupStatus === 'success')
                    <!-- STATE: TOP UP SUKSES -->
                    <div class="py-4 space-y-4">
                        <div class="w-14 h-14 rounded-full bg-lime-100 text-lime-700 flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Top Up Berhasil Terkirim!</h3>
                        <p class="text-xs text-slate-600">
                            Pembayaran QRIS telah diverifikasi dan item game telah dikirim ke akun Anda.
                        </p>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-left space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Invoice</span>
                                <span class="font-mono font-semibold text-slate-900">{{ $qrisInvoice }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Serial Number (SN)</span>
                                <span class="font-mono text-lime-700 font-bold">{{ $topupSn }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Produk</span>
                                <span class="text-slate-900 font-semibold">{{ $selectedProduct['name'] ?? '' }}</span>
                            </div>
                        </div>

                        <button wire:click="closeQrisModal" type="button" 
                                class="w-full py-3 rounded-xl bg-lime-500 hover:bg-lime-600 text-slate-950 font-bold text-xs tracking-wide shadow-sm transition-colors cursor-pointer">
                            Selesai & Beli Lagi
                        </button>
                    </div>

                @elseif($paymentStatus === 'expired')
                    <!-- STATE: EXPIRED -->
                    <div class="py-4 space-y-4">
                        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                            <span class="material-symbols-outlined text-3xl">error</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Waktu Pembayaran Habis</h3>
                        <p class="text-xs text-slate-500">Kode QRIS ini telah kedaluwarsa. Silakan lakukan pemesanan ulang.</p>
                        <button wire:click="closeQrisModal" type="button" 
                                class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>

                @elseif($topupStatus === 'failed')
                    <!-- STATE: FAILED PROVIDER -->
                    <div class="py-4 space-y-4">
                        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                            <span class="material-symbols-outlined text-3xl">warning</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Kendala Pengiriman Produk</h3>
                        <p class="text-xs text-rose-600 font-medium">{{ $topupMessage ?: 'Terjadi kendala saat memproses pesanan ke server game.' }}</p>
                        <p class="text-[11px] text-slate-400">Pembayaran Anda aman. Silakan hubungi admin atau periksa kembali User ID akun game Anda.</p>
                        <button wire:click="closeQrisModal" type="button" 
                                class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>

                @elseif($paymentStatus === 'paid' && $topupStatus === 'processing')
                    <!-- STATE: SUDAH DIBAYAR, SEDANG DIPROSES KE PROVIDER -->
                    <div class="py-6 space-y-4">
                        <div class="w-16 h-16 rounded-full bg-lime-100 text-lime-700 flex items-center justify-center mx-auto shadow-inner">
                            <span class="w-8 h-8 border-3 border-lime-600 border-t-transparent rounded-full animate-spin"></span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Pembayaran Diterima!</h3>
                        <p class="text-xs text-slate-500">
                            Sedang menghubungi server provider Melostore H2H untuk memproses item game Anda...
                        </p>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-[11px] font-mono text-slate-600 border border-slate-200">
                            <span>{{ $qrisInvoice }}</span>
                        </div>
                    </div>

                @else
                    <!-- STATE: WAITING QRIS PAYMENT -->
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-[11px] font-mono text-slate-600 border border-slate-200 mb-2">
                            <span>{{ $qrisInvoice }}</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Scan QRIS BorderPay</h3>
                        <p class="text-xs text-slate-500 mb-4">Gunakan GoPay, OVO, DANA, BCA, Livin, ShopeePay atau Mobile Banking</p>

                        <!-- Canvas QR Container -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs inline-block mx-auto mb-4">
                            <div id="qris-topup-canvas" class="flex items-center justify-center"></div>
                        </div>

                        <!-- Nominal Tagihan -->
                        <div class="mb-4">
                            <p class="text-xs text-slate-500">Total Pembayaran</p>
                            <p class="text-2xl font-black text-slate-900 font-mono">
                                {{ $selectedProduct['formatted_price'] ?? 'Rp 0' }}
                            </p>
                        </div>

                        <!-- Status Polling Badge -->
                        <div class="flex items-center justify-center gap-2 text-xs py-2 px-3.5 rounded-xl border bg-amber-50 border-amber-200 text-amber-900">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span class="font-medium">Menunggu Pembayaran Anda...</span>
                        </div>

                        <p class="text-[11px] text-slate-400 mt-4 leading-normal">
                            QRIS berlaku selama <strong class="text-slate-700">15 menit</strong>. Halaman ini otomatis memverifikasi saat pembayaran Anda berhasil.
                        </p>

                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <button wire:click="closeQrisModal" type="button" 
                                    class="text-xs text-slate-400 hover:text-rose-600 font-semibold transition-colors cursor-pointer flex items-center justify-center gap-1 mx-auto">
                                <span class="material-symbols-outlined text-sm">cancel</span>
                                <span>Batalkan Pesanan Ini</span>
                            </button>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif

    <!-- QR Code Script Hook & Alpine Wizard Controller -->
    <script>
        function topupWizard(config) {
            return {
                allGames: config.allGames || [],
                catalog: config.catalog || {},
                activeCategory: 'All',
                searchQuery: '',
                dropdownOpen: false,
                dropdownSearch: '',
                mobileBrandGridOpen: false,

                // Pagination States (Tepat 10 Row / Baris per Halaman)
                brandPage: 1,
                brandsPerPage: 40, // 10 baris x 4 kolom = 40 item
                productPage: 1,
                productsPerPage: 20, // 10 baris x 2 kolom = 20 item
                productSearch: '',

                selectedGame: config.initialGame || '',
                selectedSku: '',
                target: '{{ addslashes($target) }}',
                zone: '{{ addslashes($zone) }}',
                customerPhone: '{{ addslashes($customerPhone) }}',
                wizardStep: 1,
                isSubmitting: false,

                // Payment Method & Member Balance States
                selectedPaymentMethod: '{{ (Auth::check() && (float)Auth::user()->balance > 0) ? 'balance' : 'qris' }}',
                userBalance: {{ Auth::check() ? (float)Auth::user()->balance : 0 }},
                isLoggedIn: {{ Auth::check() ? 'true' : 'false' }},

                // Product Filter & Sorting States
                productTypeFilter: 'ALL',
                serverFilter: 'ALL',
                productSort: 'price_asc', // 'price_asc', 'price_desc', 'name_asc', 'name_desc'

                isCheckingNickname: false,
                isLoadingProducts: false,
                nickname: '{{ addslashes($nickname) }}',
                nicknameValid: {{ $nicknameValid ? 'true' : 'false' }},
                nicknameMessage: '{{ addslashes($nicknameMessage) }}',

                init() {
                    window.addEventListener('nickname-checked', e => {
                        const detail = e.detail || (e[0] ? e[0] : {});
                        this.isCheckingNickname = false;
                        this.nicknameValid = detail.valid || false;
                        this.nickname = detail.nickname || '';
                        this.nicknameMessage = detail.message || '';
                    });

                    window.addEventListener('balance-updated', e => {
                        const detail = (e.detail && e.detail.balance !== undefined) ? e.detail : (e.detail && e.detail[0] ? e.detail[0] : {});
                        if (detail.balance !== undefined) {
                            this.userBalance = parseFloat(detail.balance);
                        }
                    });
                },

                // 1. Dropdown Filtered Games (Load All Items, Instant 0ms Filter)
                get dropdownFilteredGames() {
                    const q = (this.dropdownSearch || '').trim().toLowerCase();
                    let list = this.allGames;
                    if (q) {
                        list = list.filter(g => g.name.toLowerCase().includes(q) || (g.slug && g.slug.toLowerCase().includes(q)));
                    }
                    return list; // Load SEMUA item tanpa limit 10
                },

                // 2. Brand Grid Filtered & Paginated (Maksimal 10 row)
                get filteredGames() {
                    let list = this.allGames;
                    if (this.activeCategory !== 'All') {
                        list = list.filter(g => (g.category || 'Game') === this.activeCategory);
                    }
                    if (this.searchQuery && this.searchQuery.trim()) {
                        const q = this.searchQuery.toLowerCase();
                        list = list.filter(g => g.name.toLowerCase().includes(q) || (g.slug && g.slug.toLowerCase().includes(q)));
                    }
                    return list;
                },

                get totalBrandPages() {
                    return Math.max(1, Math.ceil(this.filteredGames.length / this.brandsPerPage));
                },

                get paginatedGames() {
                    const start = (this.brandPage - 1) * this.brandsPerPage;
                    return this.filteredGames.slice(start, start + this.brandsPerPage);
                },

                nextBrandPage() {
                    if (this.brandPage < this.totalBrandPages) this.brandPage++;
                },

                prevBrandPage() {
                    if (this.brandPage > 1) this.brandPage--;
                },

                get currentGameData() {
                    return this.catalog[this.selectedGame] || { brand: { name: 'Pilih Brand' }, products: [], inquiry_fields: [] };
                },

                get currentProducts() {
                    return this.currentGameData.products || [];
                },

                get availableProductTypes() {
                    const types = this.currentProducts.map(p => p.type).filter(Boolean);
                    return [...new Set(types)].sort();
                },

                get availableServers() {
                    const servers = this.currentProducts.map(p => p.server_name).filter(Boolean);
                    return [...new Set(servers)].sort();
                },

                // 3. Product Items Filtered, Sorted & Paginated (Maksimal 10 row = 20 item)
                get filteredProducts() {
                    let list = this.currentProducts;

                    // 1. Filter Search Text
                    if (this.productSearch && this.productSearch.trim()) {
                        const q = this.productSearch.toLowerCase();
                        list = list.filter(p => p.name.toLowerCase().includes(q) || (p.type && p.type.toLowerCase().includes(q)) || (p.server_name && p.server_name.toLowerCase().includes(q)));
                    }

                    // 2. Filter Product Type
                    if (this.productTypeFilter !== 'ALL') {
                        list = list.filter(p => p.type === this.productTypeFilter);
                    }

                    // 3. Filter Server
                    if (this.serverFilter !== 'ALL') {
                        list = list.filter(p => (p.server_name || '') === this.serverFilter);
                    }

                    // 4. Sort
                    list = [...list].sort((a, b) => {
                        if (this.productSort === 'price_asc') return a.price - b.price;
                        if (this.productSort === 'price_desc') return b.price - a.price;
                        if (this.productSort === 'name_asc') return a.name.localeCompare(b.name);
                        if (this.productSort === 'name_desc') return b.name.localeCompare(a.name);
                        return 0;
                    });

                    return list;
                },

                get totalProductPages() {
                    return Math.max(1, Math.ceil(this.filteredProducts.length / this.productsPerPage));
                },

                get paginatedProducts() {
                    const start = (this.productPage - 1) * this.productsPerPage;
                    return this.filteredProducts.slice(start, start + this.productsPerPage);
                },

                nextProductPage() {
                    if (this.productPage < this.totalProductPages) this.productPage++;
                },

                prevProductPage() {
                    if (this.productPage > 1) this.productPage--;
                },

                get selectedProduct() {
                    return this.currentProducts.find(p => p.sku_code === this.selectedSku) || null;
                },

                get inquiryFields() {
                    return this.currentGameData.inquiry_fields || [];
                },

                get needsZone() {
                    const fields = this.inquiryFields;
                    if (fields.length > 1) return true;
                    // Auto-detect games that always require Zone ID / Server ID
                    const multiFieldSlugs = [
                        'mobile-legends', 'genshin-impact', 'honkai-star-rail', 'ragnarok', 
                        'lifeafter', 'dragon-raja', 'tower-of-fantasy', 'aether-gazer', 
                        'metal-slug-awakening', 'point-blank', 'seal-m', 'undawn'
                    ];
                    if (multiFieldSlugs.some(s => this.selectedGame.includes(s))) return true;
                    return fields.some(f => {
                        const k = (f.key || '').toLowerCase();
                        const n = (f.name || '').toLowerCase();
                        return k.includes('zone') || k.includes('server') || n.includes('zone') || n.includes('server');
                    });
                },

                get targetLabel() {
                    const fields = this.inquiryFields;
                    return fields[0]?.name || (this.selectedGame.includes('mobile-legends') ? 'User ID' : 'User ID / Player ID');
                },

                get targetPlaceholder() {
                    const fields = this.inquiryFields;
                    return fields[0]?.placeholder || (this.selectedGame.includes('mobile-legends') ? 'Contoh: 12345678' : 'Masukkan ID akun player');
                },

                get zoneLabel() {
                    const fields = this.inquiryFields;
                    return fields[1]?.name || (this.selectedGame.includes('mobile-legends') ? 'Zone ID' : 'Zone ID / Server ID');
                },

                get zonePlaceholder() {
                    const fields = this.inquiryFields;
                    return fields[1]?.placeholder || (this.selectedGame.includes('mobile-legends') ? 'Contoh: 2045' : 'Masukkan Zone / Server');
                },

                async selectGame(slug) {
                    this.selectedGame = slug;
                    this.mobileBrandGridOpen = false;
                    this.target = '';
                    this.zone = '';
                    this.nickname = '';
                    this.nicknameValid = false;
                    this.nicknameMessage = '';
                    this.wizardStep = 1;
                    this.productPage = 1;
                    this.productSearch = '';
                    this.productTypeFilter = 'ALL';
                    this.serverFilter = 'ALL';
                    this.productSort = 'price_asc';

                    if (!this.catalog[slug] && this.$wire) {
                        this.isLoadingProducts = true;
                        try {
                            const data = await this.$wire.fetchGameProducts(slug);
                            if (data) {
                                this.catalog[slug] = data;
                            }
                        } catch (e) {
                            console.error('Gagal mengambil produk:', e);
                        } finally {
                            this.isLoadingProducts = false;
                        }
                    }

                    this.selectedSku = '';

                    this.$nextTick(() => {
                        const panel = document.getElementById('topup-wizard-panel');
                        if (panel) {
                            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                },

                selectSku(sku) {
                    this.selectedSku = sku;
                    this.$nextTick(() => {
                        const actionEl = document.getElementById('step-1-action');
                        if (actionEl) {
                            actionEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    });
                },

                setCategory(cat) {
                    this.activeCategory = cat;
                    this.brandPage = 1;
                    if (window.innerWidth < 1024) {
                        this.mobileBrandGridOpen = true;
                    }
                },

                goToStep(step) {
                    if (step === 2 && !this.selectedProduct) {
                        alert('Silakan pilih nominal produk terlebih dahulu.');
                        return;
                    }
                    if (step === 3) {
                        if (!this.target.trim()) {
                            alert('Silakan isi ' + this.targetLabel + ' akun Anda.');
                            return;
                        }
                        if (this.needsZone && !this.zone.trim()) {
                            alert('Silakan isi ' + this.zoneLabel + ' akun Anda.');
                            return;
                        }
                    }
                    this.wizardStep = step;

                    this.$nextTick(() => {
                        const panel = document.getElementById('topup-wizard-panel');
                        if (panel) {
                            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                        if (step === 2) {
                            const input = document.getElementById('input-target-account');
                            if (input) input.focus();
                        }
                    });
                },

                selectPaymentMethod(method) {
                    this.selectedPaymentMethod = method;
                    this.$nextTick(() => {
                        const btn = document.getElementById('submit-payment-action');
                        if (btn) {
                            btn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    });
                },

                checkAccount() {
                    if (!this.target.trim()) {
                        alert('Silakan masukkan ' + this.targetLabel + ' terlebih dahulu.');
                        return;
                    }
                    if (this.needsZone && !this.zone.trim()) {
                        alert('Silakan masukkan ' + this.zoneLabel + ' terlebih dahulu.');
                        return;
                    }
                    this.isCheckingNickname = true;
                    this.nicknameMessage = 'Sedang memeriksa akun ke server game...';
                    @this.checkAccount(this.selectedGame, this.target, this.zone);
                },

                async submitPayment() {
                    if (this.isSubmitting) return;

                    if (!this.target.trim()) {
                        alert('Silakan masukkan ' + this.targetLabel + ' akun Anda.');
                        return;
                    }
                    if (this.needsZone && !this.zone.trim()) {
                        alert('Silakan masukkan ' + this.zoneLabel + ' akun Anda.');
                        return;
                    }
                    if (!this.selectedProduct) {
                        alert('Silakan pilih nominal produk top up.');
                        return;
                    }

                    if (this.selectedPaymentMethod === 'balance') {
                        const price = this.selectedProduct ? this.selectedProduct.price : 0;
                        if (this.userBalance < price) {
                            alert('Saldo akun Anda tidak mencukupi (Tersedia: Rp ' + new Intl.NumberFormat('id-ID').format(this.userBalance) + '). Silakan pilih QRIS atau lakukan isi saldo terlebih dahulu.');
                            return;
                        }
                    }

                    this.isSubmitting = true;
                    try {
                        if (this.selectedPaymentMethod === 'balance') {
                            await this.$wire.payWithBalance(this.selectedGame, this.selectedSku, this.target, this.zone, this.customerPhone);
                        } else {
                            await this.$wire.initiatePayment(this.selectedGame, this.selectedSku, this.target, this.zone, this.customerPhone);
                        }
                    } catch (err) {
                        console.error('Payment error:', err);
                        alert('Terjadi kesalahan saat memproses pembayaran: ' + (err.message || err));
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('render-qris-topup', (event) => {
                const qrString = event.qrString || (event[0] ? event[0].qrString : null);
                setTimeout(() => {
                    const container = document.getElementById('qris-topup-canvas');
                    if (container && qrString) {
                        container.innerHTML = '';
                        if (typeof QRCode !== 'undefined') {
                            new QRCode(container, {
                                text: qrString,
                                width: 220,
                                height: 220,
                                colorDark: "#000000",
                                colorLight: "#ffffff",
                                correctLevel: QRCode.CorrectLevel.M
                            });
                        }
                    }
                }, 150);
            });
        });
    </script>
</div>
