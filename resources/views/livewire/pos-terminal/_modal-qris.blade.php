{{-- Modal QRIS Dinamis BorderPay --}}
@if($showQrisModal)
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        wire:click.self="cancelQrisPayment"
        x-data="{
            timeLeft: 300,
            pollInterval: null,
            timerInterval: null,
            init() {
                this.renderCode();
                this.startTimer();
                this.startPolling();
            },
            startTimer() {
                if (this.timerInterval) clearInterval(this.timerInterval);
                this.timerInterval = setInterval(() => {
                    if (this.timeLeft > 0) {
                        this.timeLeft--;
                    } else {
                        clearInterval(this.timerInterval);
                        clearInterval(this.pollInterval);
                    }
                }, 1000);
            },
            startPolling() {
                if (this.pollInterval) clearInterval(this.pollInterval);
                this.pollInterval = setInterval(() => {
                    if (this.timeLeft > 0 && @js($showQrisModal)) {
                        $wire.checkQrisStatus();
                    }
                }, 3000);
            },
            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            },
            renderCode() {
                this.$nextTick(() => {
                    const container = document.getElementById('qris-qrcode-render');
                    const qrStr = @js($qrisQrString);
                    if (container && qrStr) {
                        container.innerHTML = '';
                        if (typeof QRCode !== 'undefined') {
                            new QRCode(container, {
                                text: qrStr,
                                width: 220,
                                height: 220,
                                colorDark: '#0f172a',
                                colorLight: '#ffffff',
                                correctLevel: QRCode.CorrectLevel.M
                            });
                        }
                    }
                });
            },
            destroy() {
                if (this.pollInterval) clearInterval(this.pollInterval);
                if (this.timerInterval) clearInterval(this.timerInterval);
            }
        }"
        x-init="init()"
        @render-qris.window="renderCode()"
        @keydown.window.escape="$wire.cancelQrisPayment()">

        <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl transition-all animate-in fade-in zoom-in-95 duration-200"
            wire:click.stop>

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="size-8 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 font-bold text-xs shadow-sm">
                        QR
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Pembayaran QRIS</h3>
                        <p class="text-[11px] font-medium text-slate-500">Scan via BCA, Mandiri, GoPay, OVO, Dana</p>
                    </div>
                </div>

                {{-- Status Pulse --}}
                <div class="flex items-center gap-1.5 px-2 py-1 rounded-full bg-emerald-50 border border-emerald-200">
                    <span class="relative flex size-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full size-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-semibold text-emerald-700">Live</span>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-6 flex flex-col items-center text-center space-y-4">

                {{-- Tagihan Card --}}
                <div class="w-full bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider block">Total Tagihan</span>
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight block mt-0.5">
                        Rp {{ number_format($qrisAmount, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] font-mono text-slate-500 block mt-1 select-all">
                        {{ $qrisReferenceId }}
                    </span>
                </div>

                {{-- QR Code Display Container --}}
                <div class="relative p-3.5 bg-white rounded-2xl border-2 border-slate-200 shadow-md">
                    <div id="qris-qrcode-render" class="flex items-center justify-center min-h-[220px] min-w-[220px]">
                        {{-- Placeholder loading sebelum ter-render --}}
                        <div class="text-center text-slate-400 animate-pulse text-xs">
                            <span class="material-symbols-outlined text-4xl block mb-1">qr_code_2</span>
                            Memuat QRIS...
                        </div>
                    </div>

                    {{-- Overlay jika waktu habis --}}
                    <div x-show="timeLeft <= 0" x-cloak
                        class="absolute inset-0 bg-slate-900/85 backdrop-blur-xs rounded-2xl flex flex-col items-center justify-center p-4 text-white">
                        <span class="material-symbols-outlined text-3xl text-amber-400 mb-1">timer_off</span>
                        <p class="text-xs font-bold">Waktu Pembayaran Habis</p>
                        <p class="text-[10px] text-slate-300 mt-1 mb-3">Silakan buat ulang QRIS baru</p>
                        <button type="button" wire:click="initiateQrisPayment"
                            class="px-3 py-1.5 text-xs font-bold bg-white text-slate-900 rounded-lg hover:bg-slate-100 transition-colors shadow-sm">
                            Buat QRIS Baru
                        </button>
                    </div>
                </div>

                {{-- Timer Countdown & Indikator --}}
                <div class="flex items-center justify-between w-full px-2 text-xs">
                    <div class="flex items-center gap-1.5 text-slate-600">
                        <span class="material-symbols-outlined text-sm text-slate-400">schedule</span>
                        <span class="font-medium text-slate-500">Kedaluwarsa:</span>
                        <span class="font-bold text-slate-800 font-mono" x-text="formatTime(timeLeft)">05:00</span>
                    </div>

                    <div class="flex items-center gap-1 text-[11px] text-slate-500">
                        <span wire:loading.class="animate-spin" wire:target="checkQrisStatus" class="material-symbols-outlined text-sm text-emerald-600">
                            sync
                        </span>
                        <span wire:loading.remove wire:target="checkQrisStatus">Auto-checking</span>
                        <span wire:loading wire:target="checkQrisStatus" class="text-emerald-600 font-semibold">Memeriksa...</span>
                    </div>
                </div>

                @if($qrisErrorMessage)
                    <div class="w-full p-2.5 bg-rose-50 border border-rose-200 rounded-lg text-rose-700 text-xs text-left">
                        {{ $qrisErrorMessage }}
                    </div>
                @endif
            </div>

            {{-- Footer Actions --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50/70 flex gap-2.5">
                <button type="button"
                    wire:click="cancelQrisPayment"
                    class="flex-1 py-2.5 px-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors font-semibold text-xs flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">close</span>
                    Batalkan
                </button>

                <button type="button"
                    wire:click="checkQrisStatus"
                    wire:loading.attr="disabled"
                    class="flex-[1.5] py-2.5 px-3 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-colors font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm shadow-emerald-600/30 disabled:opacity-50">
                    <span wire:loading.remove wire:target="checkQrisStatus" class="material-symbols-outlined text-sm">verified</span>
                    <span wire:loading wire:target="checkQrisStatus" class="material-symbols-outlined text-sm animate-spin">refresh</span>
                    <span>Cek Status</span>
                </button>
            </div>
        </div>
    </div>
@endif
