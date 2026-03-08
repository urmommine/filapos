{{-- Create Customer Modal --}}
@if($showCreateCustomerModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
        wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()">
        <div class="bg-white border border-slate-200 rounded-xl w-full max-w-md overflow-hidden shadow-2xl" wire:click.stop>
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">person_add</span> Tambah Pelanggan Baru
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama <span
                            class="text-red-500">*</span></label>
                    <input type="text" wire:model="newCustomerName" x-init="$nextTick(() => $el.focus())"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                        placeholder="Masukkan nama pelanggan">
                    @error('newCustomerName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon <span
                            class="text-red-500">*</span></label>
                    <input type="text" wire:model="newCustomerPhone"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                        placeholder="Contoh: 08123456789">
                    @error('newCustomerPhone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email (Opsional)</label>
                    <input type="email" wire:model="newCustomerEmail"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                        placeholder="Contoh: pelanggan@email.com">
                    @error('newCustomerEmail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Alamat (Opsional)</label>
                    <textarea wire:model="newCustomerAddress" rows="2"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none resize-none"
                        placeholder="Masukkan alamat lengkap"></textarea>
                    @error('newCustomerAddress') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="p-5 border-t border-slate-100 flex gap-3 bg-slate-50 justify-end">
                <button
                    class="py-3 px-6 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                    wire:click="closeModal">Batal</button>
                <button
                    class="py-3 px-6 rounded-xl bg-primary text-white hover:bg-primary-hover shadow-lg shadow-primary/25 transition-colors font-bold flex items-center gap-2"
                    wire:click="createCustomer">
                    <span class="material-symbols-outlined text-[18px]">save</span> Simpan
                </button>
            </div>
        </div>
    </div>
@endif