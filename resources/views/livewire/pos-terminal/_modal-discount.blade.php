{{-- Discount Modal --}}
@if($showDiscountModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
        wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()"
        @keydown.window.f1.prevent="$wire.set('discountType', 0)" @keydown.window.f2.prevent="$wire.set('discountType', 1)">
        <div class="bg-white border border-slate-200 rounded-xl w-full max-w-sm overflow-hidden shadow-2xl" wire:click.stop>
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <x-heroicon-o-tag class="w-6 h-6 text-yellow-600" /> Tambah Diskon
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="flex bg-slate-100 p-1 rounded-lg">
                    <button
                        class="flex-1 py-2 rounded-md text-sm font-bold transition-all {{ $discountType == 0 ? 'bg-primary text-white shadow' : 'text-slate-500' }}"
                        wire:click="$set('discountType', 0)">Nominal (Rp) (F1)</button>
                    <button
                        class="flex-1 py-2 rounded-md text-sm font-bold transition-all {{ $discountType == 1 ? 'bg-primary text-white shadow' : 'text-slate-500' }}"
                        wire:click="$set('discountType', 1)">Persen (%) (F2)</button>
                </div>
                <input type="number" wire:model="discountValue"
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-900 text-xl font-bold focus:ring-2 focus:ring-primary/50 focus:border-transparent text-center outline-none"
                    placeholder="0" x-init="$nextTick(() => $el.focus())" wire:keydown.enter="applyDiscount">
            </div>
            <div class="p-5 border-t border-slate-100 flex gap-3 bg-slate-50">
                <button
                    class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                    wire:click="closeModal">Batal</button>
                <button
                    class="flex-1 py-3 px-4 rounded-xl bg-primary text-white hover:bg-primary-hover transition-colors font-bold"
                    wire:click="applyDiscount">Terapkan (Enter)</button>
            </div>
        </div>
    </div>
@endif