<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
     x-data="{
         selectedGame: @entangle('selectedGame'),
         searchGame: '',
         dropdownOpen: false,
         games: {{ Js::from($gamesList) }},
         get filteredGamesList() {
             const q = (this.searchGame || '').trim().toLowerCase();
             if (!q) return this.games;
             return this.games.filter(g => g.name.toLowerCase().includes(q));
         },
         get currentGameObj() {
             return this.games.find(g => g.slug === this.selectedGame) || { name: 'Pilih Game', slug: this.selectedGame };
         },
         get needsZone() {
             const g = (this.selectedGame || '').toLowerCase();
             const multiField = [
                 'mobile-legends', 'genshin-impact', 'honkai-star-rail', 'ragnarok',
                 'lifeafter', 'dragon-raja', 'tower-of-fantasy', 'aether-gazer',
                 'metal-slug-awakening', 'point-blank', 'seal-m', 'undawn'
             ];
             return multiField.some(slug => g.includes(slug));
         },
         get targetLabel() {
             if (this.selectedGame.includes('mobile-legends')) return 'User ID';
             if (this.selectedGame.includes('free-fire')) return 'Player ID';
             if (this.selectedGame.includes('genshin')) return 'UID Akun';
             return 'User ID / Player ID';
         },
         get targetPlaceholder() {
             if (this.selectedGame.includes('mobile-legends')) return 'Contoh: 12345678';
             if (this.selectedGame.includes('free-fire')) return 'Contoh: 849201948';
             return 'Masukkan User ID / Akun';
         },
         get zoneLabel() {
             if (this.selectedGame.includes('mobile-legends')) return 'Zone ID (Server)';
             if (this.selectedGame.includes('genshin')) return 'Server Wilayah';
             return 'Zone ID / Server ID';
         },
         get zonePlaceholder() {
             if (this.selectedGame.includes('mobile-legends')) return 'Contoh: 2045';
             return 'Masukkan Zone ID / Server';
         }
     }">

    <!-- Top Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-8 border-b border-slate-200/90 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-lime-500 text-slate-950 flex items-center justify-center shrink-0 shadow-sm shadow-lime-500/25">
                    <span class="material-symbols-outlined text-xl">verified_user</span>
                </span>
                <span>Cek Username & ID Akun Game</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Verifikasi nickname dan validasi ID resmi server game sebelum melakukan pembayaran top up.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200/80 text-xs font-bold text-emerald-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Inquiry Server Aktif</span>
            </span>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Kolom Form Cek Akun (Left) -->
        <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Formulir Verifikasi Akun
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pilih game dan masukkan parameter ID akun untuk memeriksa nickname
                    </p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-lime-50 border border-lime-200 text-[11px] font-bold text-lime-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-lime-500"></span>
                    <span>39 Kuota Gratis Harian</span>
                </div>
            </div>

            <!-- 1. Dropdown Pilih Game (Searchable All Brands) -->
            <div class="space-y-1.5 relative" @click.outside="dropdownOpen = false">
                <label class="block text-xs font-bold text-slate-700">
                    Pilih Game <span class="text-red-500">*</span>
                </label>
                
                <button @click="dropdownOpen = !dropdownOpen" type="button"
                        class="w-full flex items-center justify-between px-4 py-3 bg-white border border-slate-300 hover:border-lime-500 focus:border-lime-500 rounded-2xl text-left transition-all shadow-2xs cursor-pointer">
                    <div class="flex items-center gap-3 truncate">
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 overflow-hidden border border-slate-200">
                            <template x-if="currentGameObj.thumbnail">
                                <img :src="currentGameObj.thumbnail" :alt="currentGameObj.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!currentGameObj.thumbnail">
                                <span class="material-symbols-outlined text-lg">sports_esports</span>
                            </template>
                        </div>
                        <div class="truncate">
                            <span class="text-xs font-bold text-slate-900 block truncate" x-text="currentGameObj.name"></span>
                            <span class="text-[10px] text-slate-400 font-mono" x-text="selectedGame"></span>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Popover List: Game Categories Only -->
                <div x-show="dropdownOpen" x-cloak
                     class="absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-2 space-y-1 max-h-80 overflow-y-auto">
                    <!-- Search Input -->
                    <div class="p-1 sticky top-0 bg-white border-b border-slate-100 pb-2 z-10">
                        <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus-within:bg-white focus-within:border-lime-500 transition-all">
                            <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
                            <input x-model="searchGame" type="text" placeholder="Cari nama game (ML, FF, Genshin...)"
                                   class="w-full bg-transparent text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none">
                            <button x-show="searchGame.length > 0" @click="searchGame = ''" type="button" class="text-slate-400 hover:text-slate-600">
                                <span class="material-symbols-outlined text-base">close</span>
                            </button>
                        </div>
                    </div>

                    <!-- Items List (Hanya Game) -->
                    <div class="py-1 space-y-0.5">
                        <template x-for="g in filteredGamesList" :key="g.slug">
                            <button @click="$wire.selectGame(g.slug); selectedGame = g.slug; dropdownOpen = false" type="button"
                                    class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-left transition-colors cursor-pointer"
                                    :class="selectedGame === g.slug ? 'bg-lime-50 text-lime-950 font-bold border border-lime-300' : 'hover:bg-slate-50 text-slate-800'">
                                <div class="flex items-center gap-2.5 truncate">
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 overflow-hidden border border-slate-200">
                                        <template x-if="g.thumbnail">
                                            <img :src="g.thumbnail" :alt="g.name" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!g.thumbnail">
                                            <span class="material-symbols-outlined text-sm">sports_esports</span>
                                        </template>
                                    </div>
                                    <span class="text-xs truncate font-semibold" x-text="g.name"></span>
                                </div>
                                <span class="text-[10px] text-slate-400 px-2 py-0.5 rounded bg-slate-100 shrink-0">Game</span>
                            </button>
                        </template>
                        <div x-show="filteredGamesList.length === 0" class="py-4 text-center text-xs text-slate-400">
                            Tidak ada game yang cocok dengan pencarian
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Popular Game Badges -->
            <div class="flex items-center gap-1.5 flex-wrap text-xs">
                <span class="text-[11px] text-slate-400 font-semibold mr-1">Populer:</span>
                @foreach([
                    ['name' => 'Mobile Legends', 'slug' => 'mobile-legends'],
                    ['name' => 'Free Fire', 'slug' => 'free-fire'],
                    ['name' => 'Genshin Impact', 'slug' => 'genshin-impact'],
                    ['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile'],
                    ['name' => 'Honor of Kings', 'slug' => 'honor-of-kings']
                ] as $pop)
                    <button type="button" 
                            @click="$wire.selectGame('{{ $pop['slug'] }}'); selectedGame = '{{ $pop['slug'] }}'"
                            class="px-2.5 py-1 rounded-lg border text-[11px] font-semibold transition-all cursor-pointer"
                            :class="selectedGame === '{{ $pop['slug'] }}' ? 'bg-lime-500 border-lime-500 text-slate-950 font-bold shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 border-slate-200 text-slate-600'">
                        {{ $pop['name'] }}
                    </button>
                @endforeach
            </div>

            <!-- 2. Input Parameter ID Akun -->
            <div class="space-y-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        <span x-text="targetLabel"></span> <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="target" type="text" :placeholder="targetPlaceholder"
                           class="w-full px-4 py-3 rounded-2xl bg-white border border-slate-300 text-sm text-slate-900 font-mono focus:outline-none focus:border-lime-500 focus:ring-2 focus:ring-lime-500/20 transition-all shadow-2xs">
                </div>

                <div x-show="needsZone" x-cloak>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        <span x-text="zoneLabel"></span> <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="zone" type="text" :placeholder="zonePlaceholder"
                           class="w-full px-4 py-3 rounded-2xl bg-white border border-slate-300 text-sm text-slate-900 font-mono focus:outline-none focus:border-lime-500 focus:ring-2 focus:ring-lime-500/20 transition-all shadow-2xs">
                    <p x-show="selectedGame.includes('mobile-legends')" class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs text-amber-500">info</span>
                        <span>Untuk Mobile Legends, masukkan 4-5 digit angka Zone ID dalam tanda kurung, misal <strong>2045</strong>.</span>
                    </p>
                </div>
            </div>

            <!-- Tombol Submit Pengecekan -->
            <div class="pt-2">
                <button wire:click="checkAccount" type="button" :disabled="$wire.isChecking"
                        class="w-full py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 disabled:opacity-60 text-white font-bold text-sm tracking-wide shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <template x-if="!$wire.isChecking">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lime-400 text-lg">search_check</span>
                            <span>Periksa Username Sekarang</span>
                        </div>
                    </template>
                    <template x-if="$wire.isChecking">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 border-2 border-lime-400 border-t-transparent rounded-full animate-spin"></div>
                            <span>Menghubungi Server Resmi Game...</span>
                        </div>
                    </template>
                </button>
            </div>

            <!-- Petunjuk Penggunaan -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-500 space-y-1.5">
                <div class="font-bold text-slate-700 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm text-lime-600">tips_and_updates</span>
                    <span>Informasi Pengecekan:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-500">
                    <li>Pengecekan akun memverifikasi keberadaan akun langsung dari server game resmi.</li>
                    <li>Jika nickname terdeteksi, akun dipastikan 100% valid dan siap untuk di-top up.</li>
                    <li>Setelah nickname terverifikasi, Anda dapat langsung mengklik tombol "Lanjut Top Up" di samping.</li>
                </ul>
            </div>
        </div>

        <!-- Kolom Hasil Verifikasi & Kartu Akun (Right) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- 1. Status Hasil Verifikasi -->
            @if($hasChecked)
                @if($isValid && $verifiedData)
                    <!-- KARTU AKUN SUKSES TERVERIFIKASI -->
                    <div class="bg-white border-2 border-lime-500 rounded-3xl p-6 shadow-md shadow-lime-500/10 space-y-5 animate-in fade-in duration-300">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-2xl bg-lime-500 text-slate-950 flex items-center justify-center font-bold shadow-sm">
                                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                                </div>
                                <div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-lime-100 text-lime-900 uppercase tracking-wider">
                                        Akun Valid
                                    </span>
                                    <h3 class="text-xs text-slate-400 font-medium mt-0.5">
                                        Terverifikasi Melostore H2H
                                    </h3>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $verifiedData['checked_at'] }}</span>
                        </div>

                        <!-- Data Box -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Nickname Resmi</span>
                                <span class="text-xl font-extrabold text-slate-900 tracking-tight block mt-0.5">
                                    {{ $verifiedData['nickname'] }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/80 text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Game / Brand</span>
                                    <span class="font-bold text-slate-800">{{ $verifiedData['game'] }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block">User ID</span>
                                    <span class="font-bold text-slate-800 font-mono">{{ $verifiedData['target'] }}</span>
                                </div>
                                @if(!empty($verifiedData['zone']))
                                    <div class="col-span-2">
                                        <span class="text-[10px] text-slate-400 block">Zone / Server</span>
                                        <span class="font-bold text-slate-800 font-mono">{{ $verifiedData['zone'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Tombol Langsung Lanjut Top Up Game Ini -->
                        <a href="{{ route('topups', ['game' => $verifiedData['slug'], 'target' => $verifiedData['target'], 'zone' => $verifiedData['zone']]) }}"
                           class="w-full py-3.5 rounded-2xl bg-lime-500 hover:bg-lime-400 text-slate-950 font-black text-xs tracking-wider uppercase transition-all shadow-md shadow-lime-500/25 flex items-center justify-center gap-2 group">
                            <span>Lanjut Top Up Game Ini</span>
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                @else
                    <!-- KARTU GAGAL / TIDAK DITEMUKAN -->
                    <div class="bg-white border border-amber-300 rounded-3xl p-6 shadow-xs space-y-4 animate-in fade-in duration-300">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
                                <span class="material-symbols-outlined text-2xl">error</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Pengecekan Gagal</h3>
                                <p class="text-xs text-amber-700 font-medium">{{ $message }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500">
                            Pastikan User ID dan Zone ID telah diketik dengan benar tanpa spasi atau karakter khusus. Untuk Mobile Legends, periksa kembali angka Zone ID di dalam tanda kurung pada profil akun Anda.
                        </p>
                    </div>
                @endif
            @else
                <!-- INITIAL STATE: BELUM DICEK -->
                <div class="bg-white border border-dashed border-slate-300 rounded-3xl p-8 text-center space-y-4 shadow-2xs">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl">badge</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">
                            Belum Ada Data yang Diperiksa
                        </h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
                            Masukkan parameter akun di formulir sebelah kiri dan klik tombol periksa untuk melihat nama player resmi.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Quick Link Kembali ke Order -->
            <div class="p-5 bg-white border border-slate-200 rounded-3xl flex items-center justify-between shadow-2xs">
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Ingin Langsung Top Up?</h4>
                    <p class="text-[11px] text-slate-400">Buka katalog lengkap produk & voucher digital</p>
                </div>
                <a href="{{ route('topups') }}" 
                   class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors">
                    Katalog Top Up &rarr;
                </a>
            </div>

        </div>

    </div>

</div>
