<div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 relative">
    
    <!-- Ambient Background Glow -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
        <div class="w-96 h-96 rounded-full bg-lime-400/10 blur-3xl -top-20"></div>
        <div class="w-80 h-80 rounded-full bg-emerald-400/10 blur-3xl bottom-10"></div>
    </div>

    <div class="w-full max-w-md space-y-6 relative z-10">
        
        <!-- Header Brand -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-lime-500 text-slate-950 shadow-lg shadow-lime-500/25 mb-1 transform hover:scale-105 transition-transform duration-200">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Masuk Akun Member
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto">
                Masuk untuk bayar instan pakai Saldo Akun & pantau mutasi riwayat top up game Anda.
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 space-y-5">
            
            @if(!empty($errorMessage))
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-start gap-2.5 animate-in fade-in">
                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="font-medium leading-relaxed">{{ $errorMessage }}</span>
                </div>
            @endif

            <form wire:submit.prevent="login" class="space-y-4">
                
                <!-- Identifier Input (Email / WhatsApp) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Email atau Nomor WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <!-- Icon Left (Centered & Click-through) -->
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input wire:model="identifier" type="text" placeholder="nama@email.com atau 08123456789" autofocus
                               class="w-full pl-11 pr-4 h-11 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-lime-500 focus:ring-4 focus:ring-lime-500/10 focus:outline-none transition-all">
                    </div>
                    @error('identifier') <span class="text-[11px] text-rose-600 font-medium block pl-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5" x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                    </div>
                    <div class="relative">
                        <!-- Icon Left -->
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <input wire:model="password" :type="show ? 'text' : 'password'" placeholder="Masukkan kata sandi..."
                               class="w-full pl-11 pr-11 h-11 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-lime-500 focus:ring-4 focus:ring-lime-500/10 focus:outline-none transition-all">
                        
                        <!-- Toggle Password Button (Robust SVG, Zero Font Lag) -->
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                            <button type="button" @click="show = !show" class="text-slate-400 hover:text-slate-600 focus:outline-none p-1 transition-colors cursor-pointer" tabindex="-1">
                                <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('password') <span class="text-[11px] text-rose-600 font-medium block pl-1">{{ $message }}</span> @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded text-lime-600 focus:ring-lime-500 border-slate-300">
                        <span class="text-xs text-slate-600 font-medium">Ingat Saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full h-12 rounded-2xl bg-slate-900 hover:bg-slate-800 active:bg-slate-950 disabled:opacity-50 text-white font-bold text-xs tracking-wider uppercase transition-all shadow-md shadow-slate-900/10 flex items-center justify-center gap-2 cursor-pointer">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span>Masuk ke Akun</span>
                        <svg class="w-4 h-4 text-lime-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <span class="w-4 h-4 border-2 border-lime-400 border-t-transparent rounded-full animate-spin"></span>
                        <span>Memverifikasi...</span>
                    </span>
                </button>
            </form>

            <!-- Bottom Register Link -->
            <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                <span>Belum punya akun member?</span>
                <a href="{{ route('topups.register') }}" class="font-bold text-lime-700 hover:text-lime-800 underline ml-1">
                    Daftar Sekarang
                </a>
            </div>
        </div>

        <!-- Quick Benefits Badge -->
        <div class="flex items-center justify-center gap-4 text-[11px] font-medium text-slate-400">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Bayar 1-Klik Saldo</span>
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Riwayat Terpantau</span>
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Bebas Biaya Admin</span>
            </span>
        </div>

        <!-- Back to Portal -->
        <div class="text-center pt-2">
            <a href="{{ route('topups') }}" class="text-xs text-slate-500 hover:text-slate-900 inline-flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Portal Top Up</span>
            </a>
        </div>

    </div>
</div>
