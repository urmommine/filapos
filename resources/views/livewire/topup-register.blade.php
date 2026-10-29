<div class="min-h-[85vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-10 relative">
    
    <!-- Ambient Background Glow -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
        <div class="w-96 h-96 rounded-full bg-lime-400/10 blur-3xl -top-20"></div>
        <div class="w-80 h-80 rounded-full bg-emerald-400/10 blur-3xl bottom-10"></div>
    </div>

    <div class="w-full max-w-md space-y-6 relative z-10">
        
        <!-- Header Brand -->
        <div class="text-center space-y-2">
            <img src="{{ asset('images/filapos-icon.png') }}" alt="FilaPOS" class="inline-flex size-14 object-contain rounded-2xl shadow-lg shadow-lime-500/25 mb-1 transform hover:scale-105 transition-transform duration-200">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Daftar Akun Member
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto">
                Daftar sekali untuk simpan saldo, bayar top up game 1-klik, dan pantau mutasi transaksi.
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

            <form wire:submit.prevent="register" class="space-y-4">
                
                <!-- Name Input -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input wire:model="name" type="text" placeholder="Contoh: Budi Santoso" autofocus
                               class="w-full pl-11 pr-4 h-11 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-lime-500 focus:ring-4 focus:ring-lime-500/10 focus:outline-none transition-all">
                    </div>
                    @error('name') <span class="text-[11px] text-rose-600 font-medium block pl-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input wire:model="email" type="email" placeholder="nama@email.com"
                               class="w-full pl-11 pr-4 h-11 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-lime-500 focus:ring-4 focus:ring-lime-500/10 focus:outline-none transition-all">
                    </div>
                    @error('email') <span class="text-[11px] text-rose-600 font-medium block pl-1">{{ $message }}</span> @enderror
                </div>

                <!-- Phone Input (WhatsApp) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input wire:model="phone" type="tel" placeholder="08123456789 atau 62812..."
                               class="w-full pl-11 pr-4 h-11 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-lime-500 focus:ring-4 focus:ring-lime-500/10 focus:outline-none transition-all">
                    </div>
                    <p class="text-[10px] text-slate-400 pl-1">Dapat digunakan sebagai ID login akun Anda.</p>
                    @error('phone') <span class="text-[11px] text-rose-600 font-medium block pl-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5" x-data="{ show: false }">
                    <label class="block text-xs font-bold text-slate-700">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <input wire:model="password" :type="show ? 'text' : 'password'" placeholder="Minimal 6 karakter..."
                               class="w-full pl-11 pr-11 h-11 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-lime-500 focus:ring-4 focus:ring-lime-500/10 focus:outline-none transition-all">
                        
                        <!-- Toggle Password Button (SVG) -->
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

                <!-- Password Confirmation Input -->
                <div class="space-y-1.5" x-data="{ show: false }">
                    <label class="block text-xs font-bold text-slate-700">
                        Ulangi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <input wire:model="password_confirmation" :type="show ? 'text' : 'password'" placeholder="Ulangi kata sandi..."
                               class="w-full pl-11 pr-11 h-11 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-lime-500 focus:ring-4 focus:ring-lime-500/10 focus:outline-none transition-all">
                        
                        <!-- Toggle Password Button (SVG) -->
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
                    @error('password_confirmation') <span class="text-[11px] text-rose-600 font-medium block pl-1">{{ $message }}</span> @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" wire:loading.attr="disabled"
                        class="w-full mt-3 h-12 rounded-2xl bg-slate-900 hover:bg-slate-800 active:bg-slate-950 disabled:opacity-50 text-white font-bold text-xs tracking-wider uppercase transition-all shadow-md shadow-slate-900/10 flex items-center justify-center gap-2 cursor-pointer">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span>Daftar Sekarang</span>
                        <svg class="w-4 h-4 text-lime-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <span class="w-4 h-4 border-2 border-lime-400 border-t-transparent rounded-full animate-spin"></span>
                        <span>Mendaftarkan...</span>
                    </span>
                </button>
            </form>

            <!-- Bottom Login Link -->
            <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                <span>Sudah memiliki akun member?</span>
                <a href="{{ route('topups.login') }}" class="font-bold text-lime-700 hover:text-lime-800 underline ml-1">
                    Masuk di Sini
                </a>
            </div>
        </div>

        <!-- Trust Badges -->
        <div class="flex items-center justify-center gap-4 text-[11px] font-medium text-slate-400">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Privasi Terjaga</span>
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Akses 24 Jam</span>
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-lime-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>Deposit Otomatis</span>
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
