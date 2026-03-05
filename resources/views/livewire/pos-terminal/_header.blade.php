{{-- Header --}}
<header class="pos-header h-16 sm:h-20 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 z-10">
    <div class="flex flex-col min-w-0">
        <h1 class="pos-store-name text-lg sm:text-2xl font-bold text-slate-900 truncate">{{ $storeName }}</h1>
        <p class="pos-store-date text-xs sm:text-sm text-slate-500">{{ now()->translatedFormat('l, d M Y') }}
        </p>
    </div>
    <div class="flex items-center gap-4">
        <div class="hidden xl:flex gap-4 text-xs text-slate-500 font-medium items-center">
            <span class="flex items-center gap-1"><kbd
                    class="bg-white px-1.5 py-0.5 rounded text-slate-700 border border-slate-200 text-[11px] font-bold">F2</kbd>
                Cari</span>
            <span class="flex items-center gap-1"><kbd
                    class="bg-white px-1.5 py-0.5 rounded text-slate-700 border border-slate-200 text-[11px] font-bold">F9</kbd>
                Bayar</span>
            <span class="flex items-center gap-1"><kbd
                    class="bg-white px-1.5 py-0.5 rounded text-slate-700 border border-slate-200 text-[11px] font-bold">F10</kbd>
                Pajak</span>
        </div>
        <div class="relative w-48 sm:w-72 lg:w-96 group">
            <input
                class="w-full pl-10 sm:pl-12 pr-20 sm:pr-24 py-2.5 sm:py-3 bg-white border border-slate-200 rounded-xl placeholder:text-slate-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:shadow-lg focus:shadow-primary/5 text-slate-800 outline-none text-sm sm:text-base transition-all duration-200"
                placeholder=" Cari / Scan produk..." wire:model.live.debounce.300ms="search"
                wire:keydown.enter="handleBarcodeScan($event.target.value); $event.target.value = '';"
                id="search-input" />
            <div class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-slate-300 text-lg sm:text-xl">barcode_scanner</span>
                <kbd
                    class="hidden sm:inline-flex bg-slate-100 text-slate-400 text-[10px] font-bold px-1.5 py-0.5 rounded border border-slate-200">F2</kbd>
            </div>
        </div>
        {{-- Mobile user avatar --}}
        <div class="lg:hidden relative" x-data="{ mobileUser: false }">
            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm cursor-pointer"
                @click="mobileUser = !mobileUser">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div x-show="mobileUser" @click.outside="mobileUser = false" x-cloak
                class="absolute right-0 top-full mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                    class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-secondary hover:text-primary transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">dashboard</span> Admin Panel
                </a>
                <button wire:click="openProfileModal()"
                    class="w-full text-left px-4 py-2.5 text-sm text-slate-600 hover:bg-secondary hover:text-primary transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">person</span> Profil Saya
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2 border-t border-slate-100">
                        <span class="material-symbols-outlined text-lg">logout</span> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>