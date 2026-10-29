<div class="flex min-h-screen items-center justify-center bg-slate-50 p-4">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('images/filapos-icon.png') }}" alt="FilaPOS" class="mx-auto size-16 object-contain rounded-2xl mb-4 shadow-md shadow-lime-500/20">
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Reset Password</h1>
            <p class="mt-2 text-slate-600">
                @if($step === 1) Masukkan email Anda untuk menerima kode OTP.
                @elseif($step === 2) Masukkan kode OTP yang dikirim ke email Anda.
                @else Buat password baru untuk akun Anda.
                @endif
            </p>
        </div>

        <div class="rounded-2xl bg-white p-8 shadow-xl shadow-slate-200/50">
            @if($step === 1)
                <form wire:submit="sendOtp" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email Address</label>
                        <div class="mt-1">
                            <input wire:model="email" id="email" type="email" required
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-primary focus:bg-white focus:ring-primary transition-all duration-200"
                                placeholder="name@company.com">
                        </div>
                        @error('email') <span class="mt-1 text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        class="flex w-full justify-center rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow-[0_4px_14px_rgba(132,204,22,0.4)] hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all duration-200">
                        <span wire:loading.remove>Kirim Kode OTP</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </form>
            @elseif($step === 2)
                <form wire:submit="verifyOtp" class="space-y-6">
                    <div>
                        <label for="otp" class="block text-sm font-medium text-slate-700 text-center">Kode OTP</label>
                        <div class="mt-4 flex justify-center">
                            <input wire:model="otp" id="otp" type="text" maxlength="6" required
                                class="block w-48 rounded-xl border-slate-200 bg-slate-50 px-4 py-4 text-center text-2xl font-bold tracking-[0.5em] text-slate-900 focus:border-primary focus:bg-white focus:ring-primary transition-all duration-200"
                                placeholder="000000">
                        </div>
                        @error('otp') <span class="mt-1 block text-center text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit"
                        class="flex w-full justify-center rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow-[0_4px_14px_rgba(132,204,22,0.4)] hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all duration-200">
                        Verifikasi OTP
                    </button>
                    
                    <div class="text-center">
                        <button type="button" wire:click="sendOtp" class="text-sm font-medium text-primary hover:text-primary-hover">
                            Kirim ulang kode?
                        </button>
                    </div>
                </form>
            @elseif($step === 3)
                <form wire:submit="resetPassword" class="space-y-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
                        <div class="mt-1">
                            <input wire:model="password" id="password" type="password" required
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary transition-all duration-200">
                        </div>
                        @error('password') <span class="mt-1 text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                        <div class="mt-1">
                            <input wire:model="password_confirmation" id="password_confirmation" type="password" required
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:border-primary focus:bg-white focus:ring-primary transition-all duration-200">
                        </div>
                    </div>

                    <button type="submit"
                        class="flex w-full justify-center rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow-[0_4px_14px_rgba(132,204,22,0.4)] hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all duration-200">
                        Reset Password
                    </button>
                </form>
            @endif
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-primary transition-colors duration-200" wire:navigate>
                &larr; Kembali ke Halaman Login
            </a>
        </div>
    </div>
</div>
