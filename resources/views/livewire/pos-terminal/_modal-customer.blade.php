{{-- Customer Modal --}}
@if($showCustomerModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
        wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()">
        <div class="bg-white border border-slate-200 rounded-xl w-full max-w-md overflow-hidden shadow-2xl" wire:click.stop>
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <x-heroicon-o-users class="w-6 h-6 text-primary" /> Pilih Pelanggan
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <input type="text" wire:model.live.debounce.500ms="customerSearch"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none"
                    placeholder="Cari nama, email, atau telepon..." x-init="$nextTick(() => $el.focus())">
                <div class="max-h-[300px] overflow-y-auto space-y-2">
                    @forelse($customers as $customer)
                        <div class="p-3 bg-slate-50 rounded-lg cursor-pointer hover:bg-secondary hover:border-primary border border-slate-100 transition-all"
                            wire:click="selectCustomer({{ $customer->id }})">
                            <p class="text-slate-900 font-bold">{{ $customer->name }}</p>
                            <p class="text-xs text-slate-500">{{ $customer->phone ?? '-' }} • {{ $customer->email ?? '-' }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 py-4">
                            @if(strlen($customerSearch) > 0)
                                Tidak ada pelanggan ditemukan.
                            @else
                                Ketik untuk mencari pelanggan.
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="p-5 border-t border-slate-100 flex gap-3 bg-slate-50">
                <button
                    class="w-full py-3 px-4 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                    wire:click="closeModal">Batal</button>
            </div>
        </div>
    </div>
@endif