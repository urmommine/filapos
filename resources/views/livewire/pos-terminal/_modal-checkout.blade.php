{{-- Checkout Modal --}}
@if($showCheckoutModal)
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
        wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()"
        @keydown.window.f1.prevent="$wire.setExactAmount()">
        <div class="bg-white border border-slate-200 rounded-xl w-full max-w-md overflow-hidden shadow-2xl" wire:click.stop>
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <x-heroicon-o-credit-card class="w-6 h-6 text-blue-500" /> Pembayaran
                </h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="text-center p-4 bg-secondary rounded-xl border border-primary/20">
                    <p class="text-slate-500 text-sm mb-1">Total Tagihan</p>
                    <p class="text-4xl font-bold text-slate-900">Rp {{ number_format($total, 0, ',', '.') }}</p>
                </div>

                <div class="flex gap-3">
                    @foreach(['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer'] as $key => $label)
                        <button
                            class="flex-1 p-3 rounded-lg border-2 {{ $paymentMethod === $key ? 'border-primary bg-secondary text-primary' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-400' }} transition-all font-bold"
                            wire:click="setPaymentMethod('{{ $key }}')">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                @if($paymentMethod === 'cash')
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-slate-500 text-xs uppercase font-bold tracking-wider">Uang Diterima</label>
                            <input type="number" wire:model.live="amountPaid"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-900 text-xl font-bold focus:ring-2 focus:ring-primary/50 focus:border-transparent outline-none"
                                placeholder="0" x-init="$nextTick(() => $el.focus())" wire:keydown.enter="processPayment">
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            @php $suggestions = [20000, 50000, 100000]; @endphp
                            @foreach($suggestions as $amt)
                                <button
                                    class="py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:border-primary hover:text-primary hover:bg-secondary/30 transition-colors"
                                    wire:click="setQuickAmount({{ $amt }})">
                                    {{ number_format($amt / 1000) }}k
                                </button>
                            @endforeach
                            <button
                                class="py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:border-primary hover:text-primary hover:bg-secondary/30 transition-colors"
                                wire:click="setExactAmount">Pas</button>
                        </div>

                        @if($amountPaid >= $total)
                            <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg border border-green-200">
                                <span class="text-green-600 font-bold">Kembalian</span>
                                <span class="text-green-700 text-xl font-bold">Rp
                                    {{ number_format($change, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="p-5 border-t border-slate-100 flex gap-3 bg-slate-50">
                <button
                    class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                    wire:click="closeModal">Batal</button>
                <button
                    class="flex-[2] py-3 px-4 rounded-xl bg-primary text-white hover:bg-primary-hover transition-colors font-bold disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-primary/25"
                    wire:click="processPayment" @if($paymentMethod === 'cash' && $amountPaid < $total) disabled @endif>
                    Proses Pembayaran (Enter)
                </button>
            </div>
        </div>
    </div>
@endif