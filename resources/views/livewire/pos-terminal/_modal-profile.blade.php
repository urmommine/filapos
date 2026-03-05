{{-- Profile Modal --}}
@if($showProfileModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
        wire:click.self="closeModal">
        <div class="bg-white border border-slate-200 rounded-xl w-full max-w-md overflow-hidden shadow-2xl" wire:click.stop>
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <x-heroicon-o-user class="w-6 h-6 text-primary" /> Pengaturan Profil
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="space-y-2">
                    <label class="text-slate-600 text-sm font-bold">Nama Lengkap</label>
                    <input type="text" wire:model="profileName"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    @error('profileName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-slate-600 text-sm font-bold">Email</label>
                    <input type="email" wire:model="profileEmail"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                    @error('profileEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="pt-4 mt-4 border-t border-slate-200">
                    <p class="text-slate-400 text-xs mb-3 italic">Kosongkan jika tidak ingin mengubah password</p>
                    <div class="space-y-3">
                        <div class="space-y-2">
                            <label class="text-slate-600 text-sm font-bold">Password Baru</label>
                            <input type="password" wire:model="profilePassword"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                            @error('profilePassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-slate-600 text-sm font-bold">Konfirmasi Password</label>
                            <input type="password" wire:model="profilePasswordConfirmation"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-5 border-t border-slate-100 flex gap-3 bg-slate-50">
                <button
                    class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                    wire:click="closeModal">Batal</button>
                <button
                    class="flex-1 py-3 px-4 rounded-xl bg-primary text-white hover:bg-primary-hover transition-colors font-bold"
                    wire:click="updateProfile">Simpan</button>
            </div>
        </div>
    </div>
@endif