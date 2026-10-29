<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200/90 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Halo, {{ $user->name }} 👋
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Kelola saldo dompet, deposit instan QRIS, dan pantau riwayat transaksi Anda.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('topups') }}" class="px-4 py-2.5 rounded-xl bg-lime-500 hover:bg-lime-400 text-slate-950 font-bold text-xs shadow-xs flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-base">shopping_cart</span>
                <span>Order PPOB</span>
            </a>
        </div>
    </div>

    <!-- Overview Cards (Balance + Profile + Security) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- 1. Balance Card (Hero Style) -->
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-7 shadow-xl shadow-slate-950/10 relative overflow-hidden flex flex-col justify-between border border-slate-800">
            <!-- Decorative Accent -->
            <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-lime-500/10 blur-2xl pointer-events-none"></div>

            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Saldo Tersedia</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-lime-500/20 text-lime-400 border border-lime-500/30 rounded-md">
                        Aktif 24 Jam
                    </span>
                </div>
                <div class="text-3xl sm:text-4xl font-black font-mono tracking-tight text-white pt-2">
                    Rp {{ number_format($user->balance, 0, ',', '.') }}
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800/80 flex items-center justify-between">
                <span class="text-xs text-slate-400">Gunakan untuk bayar instan tanpa scan</span>
                <button wire:click="setTab('deposit')" type="button"
                        class="px-3.5 py-1.5 rounded-xl bg-lime-500 hover:bg-lime-400 text-slate-950 text-xs font-bold transition-all flex items-center gap-1 shadow-sm cursor-pointer">
                    <span class="material-symbols-outlined text-sm font-bold">add</span>
                    <span>Isi Saldo</span>
                </button>
            </div>
        </div>

        <!-- 2. Profile Summary Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="space-y-3">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Informasi Member</span>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 font-extrabold text-lg">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <h3 class="text-sm font-extrabold text-slate-900 truncate">{{ $user->name }}</h3>
                        <p class="text-xs text-slate-500 truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 space-y-1.5 text-xs">
                <div class="flex items-center justify-between text-slate-600">
                    <span class="flex items-center gap-1 text-slate-500">
                        <span class="material-symbols-outlined text-sm">call</span> WhatsApp:
                    </span>
                    <span class="font-bold text-slate-800 font-mono">{{ $user->phone ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span class="flex items-center gap-1 text-slate-500">
                        <span class="material-symbols-outlined text-sm">verified</span> Status:
                    </span>
                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px]">Member PPOB</span>
                </div>
            </div>
        </div>

        <!-- 3. Security & Tips Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="space-y-2">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Keuntungan Member</span>
                <ul class="text-xs text-slate-600 space-y-2">
                    <li class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-lime-600 text-base shrink-0">bolt</span>
                        <span><strong>Checkout 1-Detik:</strong> Langsung potong saldo tanpa menunggu scan QR.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-lime-600 text-base shrink-0">history</span>
                        <span><strong>Riwayat Lengkap:</strong> SN dan invoice tersimpan selamanya di akun Anda.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-lime-600 text-base shrink-0">shield</span>
                        <span><strong>Aman & Terjamin:</strong> Transaksi diproses via Porvider H2H terpercaya.</span>
                    </li>
                </ul>
            </div>
            
            <div class="pt-3 text-[11px] text-slate-400 border-t border-slate-100 flex items-center gap-1">
                <span class="material-symbols-outlined text-sm text-slate-400">lock</span>
                <span>Enkripsi akun & proteksi saldo aktif</span>
            </div>
        </div>

    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 overflow-x-auto">
        <button wire:click="setTab('deposit')" type="button"
                class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer
                {{ $activeTab === 'deposit' 
                    ? 'bg-slate-900 text-white shadow-sm' 
                    : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
            <span class="material-symbols-outlined text-base {{ $activeTab === 'deposit' ? 'text-lime-400' : 'text-slate-400' }}">payments</span>
            <span>Isi Saldo (QRIS)</span>
        </button>

        <button wire:click="setTab('mutations')" type="button"
                class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer
                {{ $activeTab === 'mutations' 
                    ? 'bg-slate-900 text-white shadow-sm' 
                    : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
            <span class="material-symbols-outlined text-base {{ $activeTab === 'mutations' ? 'text-lime-400' : 'text-slate-400' }}">account_balance_wallet</span>
            <span>Mutasi Saldo</span>
        </button>

        <button wire:click="setTab('orders')" type="button"
                class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer
                {{ $activeTab === 'orders' 
                    ? 'bg-slate-900 text-white shadow-sm' 
                    : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200' }}">
            <span class="material-symbols-outlined text-base {{ $activeTab === 'orders' ? 'text-lime-400' : 'text-slate-400' }}">receipt_long</span>
            <span>Riwayat</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: FORM ISI SALDO (DEPOSIT QRIS)      -->
    <!-- ========================================== -->
    @if($activeTab === 'deposit')
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6 max-w-3xl">
            
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Pengisian Saldo Akun</h2>
                <p class="text-xs text-slate-500">Pilih atau masukkan nominal saldo yang ingin Anda isi via QRIS.</p>
            </div>

            @if(!empty($errorMessage))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-rose-500 text-lg">error</span>
                    <span>{{ $errorMessage }}</span>
                </div>
            @endif

            <!-- Preset Nominal Buttons -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Pilih Nominal Cepat</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($presetAmounts as $amount)
                        <button type="button" wire:click="selectPreset({{ $amount }})"
                                class="py-3 px-4 rounded-2xl border text-left transition-all cursor-pointer flex flex-col justify-between
                                {{ $depositAmount === $amount 
                                    ? 'bg-lime-50 border-lime-500 ring-2 ring-lime-500/20 text-slate-950 font-extrabold' 
                                    : 'bg-slate-50/60 hover:bg-white border-slate-200 text-slate-700 font-semibold' }}">
                            <span class="text-[11px] text-slate-400 uppercase tracking-wider">Topup</span>
                            <span class="text-sm font-black font-mono">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Custom Amount Input -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700">Atau Masukkan Nominal Lainnya (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-sm font-bold text-slate-400 font-mono">Rp</span>
                    <input wire:model.live.debounce.300ms="depositAmount" type="number" min="1000" max="5000000" step="1000"
                           placeholder="Minimal 1.000"
                           class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 font-bold text-sm focus:bg-white focus:border-lime-500 focus:ring-2 focus:ring-lime-500/20 focus:outline-none transition-all">
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400">
                    <span>Minimal deposit: <strong>Rp 1.000</strong></span>
                    <span>Maksimal deposit: <strong>Rp 5.000.000</strong></span>
                </div>
            </div>

            <!-- Detail Biaya -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Nominal Deposit</span>
                    <span class="font-bold font-mono text-slate-900">Rp {{ number_format((int)$depositAmount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Biaya Layanan (PG)</span>
                    <span class="font-bold text-emerald-600">GRATIS (Rp 0)</span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline font-bold">
                    <span class="text-slate-900">Total Pembayaran</span>
                    <span class="text-lg font-black font-mono text-slate-900">Rp {{ number_format((int)$depositAmount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Submit Button -->
            <button wire:click="createDeposit" type="button" wire:loading.attr="disabled"
                    class="w-full py-3.5 rounded-2xl bg-lime-500 hover:bg-lime-600 active:bg-lime-700 disabled:opacity-50 text-slate-950 font-extrabold text-xs tracking-wider uppercase transition-all shadow-md shadow-lime-500/20 flex items-center justify-center gap-2 cursor-pointer">
                <span wire:loading.remove class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">qr_code_scanner</span>
                    <span>Buat Kode QRIS Deposit</span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <span class="w-4 h-4 border-2 border-slate-950 border-t-transparent rounded-full animate-spin"></span>
                    <span>Menghubungi Payment Gateway...</span>
                </span>
            </button>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 2: RIWAYAT MUTASI SALDO                -->
    <!-- ========================================== -->
    @if($activeTab === 'mutations')
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-base font-extrabold text-slate-900">Riwayat Mutasi Saldo</h2>
                <p class="text-xs text-slate-500">Catatan masuk dan keluar saldo dompet akun Anda.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Tanggal & Waktu</th>
                            <th class="py-3 px-4">Referensi / Deskripsi</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4 text-right">Jumlah</th>
                            <th class="py-3 px-4 text-right">Saldo Akhir</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mutations as $item)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 font-mono text-slate-500 whitespace-nowrap">
                                    {{ $item->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $item->description }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $item->reference_id }}</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($item->type === 'deposit')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            + Deposit
                                        </span>
                                    @elseif($item->type === 'purchase')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            - Beli Produk
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Refund
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap
                                    {{ $item->type === 'deposit' ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $item->type === 'deposit' ? '+' : '-' }}Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono text-slate-600 whitespace-nowrap">
                                    Rp {{ number_format($item->closing_balance, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($item->status === 'completed')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sukses
                                        </span>
                                    @elseif($item->status === 'pending')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">account_balance_wallet</span>
                                    <span>Belum ada riwayat mutasi saldo.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($mutations->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $mutations->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- ========================================== -->
    <!-- TAB 3: RIWAYAT PEMBELIAN GAME              -->
    <!-- ========================================== -->
    @if($activeTab === 'orders')
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-base font-extrabold text-slate-900">Riwayat Pembelian</h2>
                <p class="text-xs text-slate-500">Daftar pesanan top up yang telah dibuat.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Invoice / Waktu</th>
                            <th class="py-3 px-4">Game & Produk</th>
                            <th class="py-3 px-4">Tujuan Akun</th>
                            <th class="py-3 px-4">Metode & Total</th>
                            <th class="py-3 px-4">Serial Number (SN)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $order)
                            @php
                                $notes = json_decode($order->notes ?? '{}', true) ?: [];
                                $firstItem = $order->items->first();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-900">{{ $order->invoice_number }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $notes['game'] ?? 'Game Item' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $firstItem->product_name ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-mono font-semibold text-slate-800">
                                        {{ $notes['target'] ?? '-' }} {{ !empty($notes['zone']) ? '('.$notes['zone'].')' : '' }}
                                    </div>
                                    @if(!empty($notes['nickname']))
                                        <div class="text-[10px] text-emerald-700 font-medium">Nickname: {{ $notes['nickname'] }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-mono font-black text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                    <div class="text-[10px] uppercase font-bold text-slate-400">
                                        {{ $order->payment_method === 'balance' ? 'Saldo Akun' : 'QRIS BorderPay' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono text-[11px]">
                                    @if(!empty($notes['sn']))
                                        <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded select-all">{{ $notes['sn'] }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($order->payment_status === 'paid')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sukses
                                        </span>
                                    @elseif($order->payment_status === 'pending')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu Bayar
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">receipt_long</span>
                                    <span>Belum ada riwayat pesanan game.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MODAL POPUP DEPOSIT QRIS                   -->
    <!-- ========================================== -->
    @if($showDepositModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
             wire:poll.5s="checkDepositStatus">
            
            <div class="relative w-full max-w-md rounded-3xl bg-white border border-slate-200 shadow-2xl p-6 sm:p-8 text-center overflow-hidden">
                
                <!-- Close Button -->
                <button wire:click="closeDepositModal" type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-1 cursor-pointer">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>

                @if($depositStatus === 'paid')
                    <!-- STATE: DEPOSIT BERHASIL -->
                    <div class="py-4 space-y-4">
                        <div class="w-16 h-16 rounded-full bg-lime-100 text-lime-700 flex items-center justify-center mx-auto shadow-inner">
                            <span class="material-symbols-outlined text-3xl font-bold">check_circle</span>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900">Deposit Saldo Berhasil!</h3>
                        <p class="text-xs text-slate-600">
                            Pembayaran QRIS telah terverifikasi. Saldo akun Anda telah otomatis bertambah.
                        </p>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Referensi</span>
                                <span class="font-mono font-semibold text-slate-900">{{ $depositReferenceId }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Saldo Baru Anda</span>
                                <span class="font-mono text-lime-700 font-black text-sm">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button wire:click="closeDepositModal" type="button" 
                                class="w-full py-3 rounded-2xl bg-lime-500 hover:bg-lime-600 text-slate-950 font-bold text-xs tracking-wide shadow-sm transition-colors cursor-pointer">
                            Selesai & Tutup
                        </button>
                    </div>

                @elseif($depositStatus === 'expired')
                    <!-- STATE: EXPIRED -->
                    <div class="py-4 space-y-4">
                        <div class="w-14 h-14 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto">
                            <span class="material-symbols-outlined text-3xl">error</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Waktu Pembayaran Habis</h3>
                        <p class="text-xs text-slate-500">Kode QRIS deposit ini telah kedaluwarsa. Silakan buat permintaan isi saldo baru.</p>
                        <button wire:click="closeDepositModal" type="button" 
                                class="w-full py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>

                @else
                    <!-- STATE: MENUNGGU PEMBAYARAN QRIS -->
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-[11px] font-mono text-slate-600 border border-slate-200 mb-2">
                            <span>{{ $depositReferenceId }}</span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900">Scan QRIS untuk Isi Saldo</h3>
                        <p class="text-xs text-slate-500 mb-4">Gunakan BCA, Mandiri, BRI, BNI, GoPay, OVO, DANA, ShopeePay atau e-wallet lainnya</p>

                        <!-- Canvas QR Container -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs inline-block mx-auto mb-4">
                            <div id="deposit-qris-canvas" class="flex items-center justify-center"></div>
                        </div>

                        <!-- Nominal Tagihan -->
                        <div class="mb-4">
                            <p class="text-xs text-slate-500">Nominal Transfer Tepat</p>
                            <p class="text-2xl font-black text-slate-900 font-mono">
                                Rp {{ number_format((int)$depositAmount, 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Polling Status -->
                        <div class="flex items-center justify-center gap-2 text-xs py-2 px-3.5 rounded-xl border bg-amber-50 border-amber-200 text-amber-900">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span class="font-medium">Menunggu Pembayaran (Otomatis Masuk)...</span>
                        </div>

                        <p class="text-[11px] text-slate-400 mt-4 leading-normal">
                            QRIS berlaku selama <strong class="text-slate-700">15 menit</strong>. Sistem mengecek status setiap beberapa detik.
                        </p>

                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <button wire:click="cancelDeposit" type="button" 
                                    class="text-xs text-slate-400 hover:text-rose-600 font-semibold transition-colors cursor-pointer flex items-center justify-center gap-1 mx-auto">
                                <span class="material-symbols-outlined text-sm">cancel</span>
                                <span>Batalkan Deposit Ini</span>
                            </button>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif

    <!-- QR Code Script Hook for Deposit -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('render-deposit-qris', (event) => {
                const qrString = event.qrString || (event[0] ? event[0].qrString : null);
                setTimeout(() => {
                    const container = document.getElementById('deposit-qris-canvas');
                    if (container && qrString) {
                        container.innerHTML = '';
                        if (typeof QRCode !== 'undefined') {
                            new QRCode(container, {
                                text: qrString,
                                width: 220,
                                height: 220,
                                colorDark: "#000000",
                                colorLight: "#ffffff",
                                correctLevel: QRCode.CorrectLevel.M
                            });
                        }
                    }
                }, 150);
            });
        });
    </script>
</div>
