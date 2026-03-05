{{-- Sidebar Navigation --}}
<nav class="w-20 bg-white border-r border-slate-200 flex-col items-center py-6 shrink-0 z-20 hidden lg:flex">
    <div class="mb-8">
        <div
            class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/30">
            <span class="material-symbols-outlined text-3xl">storefront</span>
        </div>
    </div>

    <div class="flex-1 flex flex-col gap-3 w-full px-2 overflow-y-auto no-scrollbar">
        <button wire:click="selectCategory(null)"
            class="w-full aspect-square rounded-xl flex items-center justify-center transition-colors group relative {{ !$selectedCategory ? 'bg-secondary text-primary-hover' : 'text-slate-400 hover:bg-secondary/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-2xl">grid_view</span>
            <span
                class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 pointer-events-none">Semua</span>
        </button>
    </div>

    <div class="mt-auto flex flex-col items-center gap-4 pt-4" x-data="{ userMenu: false }">
        <button
            class="w-10 h-10 rounded-full hover:bg-secondary/50 text-slate-400 hover:text-primary flex items-center justify-center transition-colors"
            wire:click="openProfileModal()">
            <span class="material-symbols-outlined">settings</span>
        </button>
        <div class="relative">
            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm cursor-pointer ring-2 ring-white shadow-sm"
                @click="userMenu = !userMenu">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div x-show="userMenu" @click.outside="userMenu = false" x-cloak
                class="absolute left-full bottom-0 ml-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>
                <a href="{{ route('filament.admin.pages.dashboard') }}"
                    class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-secondary hover:text-primary transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">dashboard</span>
                    Admin Panel
                </a>
                <button wire:click="openProfileModal(); userMenu = false"
                    class="w-full text-left px-4 py-2.5 text-sm text-slate-600 hover:bg-secondary hover:text-primary transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">person</span>
                    Profil Saya
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2 border-t border-slate-100">
                        <span class="material-symbols-outlined text-lg">logout</span>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>