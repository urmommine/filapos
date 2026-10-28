<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\BorderPayService;
use App\Services\MeloStoreService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.topup')]
class TopupGame extends Component
{
    // Katalog & Game
    public string $search = '';
    public string $activeCategory = 'All';
    public string $selectedGame = '';
    public int $wizardStep = 1; // 1: Pilih Produk, 2: Isi Akun, 3: Konfirmasi & Bayar

    // Form Input Akun Pembeli
    public string $target = '';
    public string $zone = '';
    public string $nickname = '';
    public bool $isCheckingNickname = false;
    public string $nicknameMessage = '';
    public bool $nicknameValid = false;

    // Pilihan Produk / Denominasi
    public string $selectedSku = '';
    public ?array $selectedProduct = null;
    public string $customerPhone = '';

    // Modal QRIS BorderPay & Transaksi
    public bool $showQrisModal = false;
    public string $qrisInvoice = '';
    public string $qrisQrString = '';
    public ?string $qrisExpiresAt = null;
    public int $qrisTimeLeft = 300; // 5 menit
    public string $paymentStatus = 'pending'; // 'pending', 'paid', 'expired', 'failed'
    public ?int $orderId = null;

    // Status Pengiriman Melostore
    public string $topupStatus = 'waiting_payment'; // 'waiting_payment', 'processing', 'success', 'failed'
    public string $topupSn = '';
    public string $topupMessage = '';

    public function mount(): void
    {
        $this->loadGames();
        
        $queryGame = request()->query('game');
        $queryTarget = request()->query('target');
        $queryZone = request()->query('zone');

        if (!empty($queryGame)) {
            $this->selectedGame = $queryGame;
            $this->loadGameDetails($this->selectedGame);
        }

        if (!empty($queryTarget)) {
            $this->target = trim($queryTarget);
        }
        if (!empty($queryZone)) {
            $this->zone = trim($queryZone);
        }

        // Auto-Resume: Buka kembali modal jika user memiliki transaksi QRIS pending yang masih aktif
        $this->resumePendingOrderIfAny();
    }

    #[Computed]
    public function gamesList(): array
    {
        return MeloStoreService::getGameCategories();
    }

    #[Computed]
    public function gameDetails(): array
    {
        if (empty($this->selectedGame)) {
            return [];
        }

        return MeloStoreService::getGameProducts($this->selectedGame);
    }

    public function loadGames(): void
    {
        // Dynamically resolved via computed gamesList
    }

    public function setCategory(string $category): void
    {
        $this->activeCategory = $category;
    }

    public function goToStep(int $step): void
    {
        if ($step === 2 && !$this->selectedProduct) {
            $this->dispatch('topup-alert', type: 'error', message: 'Silakan pilih nominal produk terlebih dahulu.');
            return;
        }

        if ($step === 3) {
            if (empty(trim($this->target))) {
                $this->dispatch('topup-alert', type: 'error', message: 'Silakan masukkan User ID / Player ID akun game Anda.');
                return;
            }
            $inquiryFields = $this->gameDetails()['inquiry_fields'] ?? [];
            if (count($inquiryFields) > 1 && empty(trim($this->zone))) {
                $this->dispatch('topup-alert', type: 'error', message: 'Silakan masukkan Server / Zone ID akun game Anda.');
                return;
            }
        }

        $this->wizardStep = $step;
    }

    public function loadGameDetails(string $slug): void
    {
        $this->selectedGame = $slug;

        // Reset form input ketika berganti game
        $this->target = '';
        $this->zone = '';
        $this->nickname = '';
        $this->nicknameValid = false;
        $this->nicknameMessage = '';
        $this->selectedSku = '';
        $this->selectedProduct = null;
        $this->wizardStep = 1;
    }

    public function clearGame(): void
    {
        $this->selectedGame = '';
        $this->selectedSku = '';
        $this->selectedProduct = null;
        $this->target = '';
        $this->zone = '';
        $this->nickname = '';
        $this->nicknameValid = false;
        $this->nicknameMessage = '';
        $this->wizardStep = 1;
    }

    public function selectGame(string $slug): void
    {
        $this->loadGameDetails($slug);
    }

    public function fetchGameProducts(string $slug): array
    {
        return MeloStoreService::getGameProducts($slug);
    }

    public function selectProduct(string $sku): void
    {
        $this->selectedSku = $sku;
        $products = $this->gameDetails()['products'] ?? [];
        foreach ($products as $p) {
            if ($p['sku_code'] === $sku) {
                $this->selectedProduct = $p;
                break;
            }
        }
    }

    /**
     * Pengecekan Nickname Game ke Melostore H2H API.
     */
    public function checkAccount(?string $game = null, ?string $target = null, ?string $zone = null): void
    {
        if (!empty($game)) {
            $this->selectedGame = $game;
            $this->loadGameDetails($game);
        }
        if (!empty($target)) {
            $this->target = trim($target);
        }
        if ($zone !== null) {
            $this->zone = trim($zone);
        }

        if (empty(trim($this->target))) {
            $this->nicknameValid = false;
            $this->nicknameMessage = 'Silakan masukkan User ID akun game terlebih dahulu.';
            $this->dispatch('nickname-checked', valid: false, nickname: '', message: $this->nicknameMessage);
            return;
        }

        $this->isCheckingNickname = true;
        $this->nicknameMessage = 'Sedang memeriksa akun ke server game...';

        try {
            $result = MeloStoreService::checkNickname($this->selectedGame, $this->target, $this->zone);

            if ($result['success']) {
                $this->nickname = $result['nickname'];
                $this->nicknameValid = true;
                $this->nicknameMessage = 'Nama Akun: ' . $this->nickname;
            } else {
                $this->nickname = '';
                $this->nicknameValid = false;
                $this->nicknameMessage = $result['message'] ?? 'ID atau Zone tidak valid.';
            }

            $this->dispatch('nickname-checked', 
                valid: $this->nicknameValid, 
                nickname: $this->nickname, 
                message: $this->nicknameMessage
            );
        } catch (\Throwable $e) {
            $this->nicknameValid = false;
            $this->nicknameMessage = 'Gagal menghubungi server verifikasi.';
            Log::error('checkAccount error: ' . $e->getMessage());
            $this->dispatch('nickname-checked', valid: false, nickname: '', message: $this->nicknameMessage);
        } finally {
            $this->isCheckingNickname = false;
        }
    }

    /**
     * Inisialisasi Pembayaran QRIS BorderPay.
     */
    public function initiatePayment(
        ?string $game = null, 
        ?string $sku = null, 
        ?string $target = null, 
        ?string $zone = null, 
        ?string $phone = null
    ): void {
        if (!empty($game)) {
            $this->selectedGame = $game;
        }
        if (!empty($sku)) {
            $this->selectProduct($sku);
        }
        if (!empty($target)) {
            $this->target = trim($target);
        }
        if ($zone !== null) {
            $this->zone = trim($zone);
        }
        if ($phone !== null) {
            $this->customerPhone = trim($phone);
        }

        // Validasi input
        if (empty(trim($this->target))) {
            $this->dispatch('topup-alert', type: 'error', message: 'Harap isi User ID game Anda.');
            return;
        }

        // Cek field zone jika game membutuhkan zone / server
        $inquiryFields = $this->gameDetails()['inquiry_fields'] ?? [];
        $needsZone = false;
        if (count($inquiryFields) > 1) {
            $needsZone = true;
        } else {
            foreach ($inquiryFields as $f) {
                $key = strtolower($f['key'] ?? '');
                $name = strtolower($f['name'] ?? '');
                if (str_contains($key, 'zone') || str_contains($key, 'server') || str_contains($name, 'zone') || str_contains($name, 'server')) {
                    $needsZone = true;
                    break;
                }
            }
        }

        if ($needsZone && empty(trim($this->zone))) {
            $this->dispatch('topup-alert', type: 'error', message: 'Harap isi Server / Zone ID akun game Anda.');
            return;
        }

        if (!$this->selectedProduct) {
            $this->dispatch('topup-alert', type: 'error', message: 'Silakan pilih nominal produk top up.');
            return;
        }

        $price = (float) $this->selectedProduct['price'];
        if ($price < 5000) {
            $price = 5000;
        }

        try {
            DB::beginTransaction();

            // Kasir default untuk transaksi online
            $cashier = User::where('role', 'kasir')->first() ?? User::first();
            $cashierId = $cashier ? $cashier->id : 2;

            $invoiceNumber = BorderPayService::generateInvoiceNumber();
            $gameName = $this->gameDetails()['brand']['name'] ?? ucwords(str_replace('-', ' ', $this->selectedGame));
            $productName = $this->selectedProduct['name'];

            $orderNotes = json_encode([
                'type' => 'ppob_game_topup',
                'provider' => 'melostore_h2h',
                'game' => $gameName,
                'game_slug' => $this->selectedGame,
                'target' => $this->target,
                'zone' => $this->zone,
                'nickname' => $this->nickname,
                'sku_code' => $this->selectedSku,
                'customer_phone' => $this->customerPhone,
                'buyer_user_id' => Auth::id(),
            ]);

            // Buat record Order
            $order = Order::create([
                'user_id' => Auth::check() ? Auth::id() : $cashierId,
                'customer_id' => null,
                'channel' => 'ppob',
                'invoice_number' => $invoiceNumber,
                'subtotal' => $price,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => $price,
                'payment_method' => 'qris',
                'amount_paid' => 0,
                'change' => 0,
                'payment_status' => 'pending',
                'notes' => $orderNotes,
            ]);

            // Buat OrderItem
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null,
                'product_name' => "Top Up {$gameName} - {$productName}",
                'quantity' => 1,
                'unit_price' => $price,
                'total_price' => $price,
            ]);

            // Request QRIS Dinamis ke Payment Gateway BorderPay
            $paymentResult = BorderPayService::createQrisPayment(
                amount: $price,
                referenceId: $invoiceNumber
            );

            if (!$paymentResult['success']) {
                DB::rollBack();
                $msg = $paymentResult['message'] ?? 'Gagal membuat QRIS BorderPay';
                $errStr = is_array($msg) ? ($msg['message'] ?? json_encode($msg)) : (string) $msg;
                $this->dispatch('topup-alert', type: 'error', message: $errStr);
                return;
            }

            // Simpan detail QRIS ke Order
            $order->update([
                'qris_reference_id' => $paymentResult['reference_id'],
                'qris_qr_string' => $paymentResult['qr_string'],
                'qris_expires_at' => $paymentResult['expires_at'],
            ]);

            DB::commit();

            $expiresAt = !empty($paymentResult['expires_at']) 
                ? Carbon::parse($paymentResult['expires_at']) 
                : now()->addMinutes(15);

            $this->orderId = $order->id;
            $this->qrisInvoice = $paymentResult['reference_id'];
            $this->qrisQrString = $paymentResult['qr_string'];
            $this->qrisExpiresAt = $expiresAt->toISOString();
            $this->qrisTimeLeft = 900; // 15 menit = 900 detik
            $this->paymentStatus = 'pending';
            $this->topupStatus = 'waiting_payment';
            $this->showQrisModal = true;

            // Simpan ke session agar jika browser di-refresh atau dibuka lagi, modal langsung pulih
            session()->put('pending_topup_order_id', $order->id);

            // Dispatch render QR code ke canvas di browser
            $this->dispatch('render-qris-topup', qrString: $this->qrisQrString);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('initiatePayment topup error: ' . $e->getMessage());
            $this->dispatch('topup-alert', type: 'error', message: 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Bayar Pesanan Top Up Game Menggunakan Saldo Akun Member (Instan).
     */
    public function payWithBalance(string $gameSlug, string $sku, string $target, string $zone = '', string $phone = ''): void
    {
        if (!Auth::check()) {
            $this->dispatch('topup-alert', type: 'error', message: 'Silakan masuk ke akun member untuk membayar dengan Saldo.');
            return;
        }

        if (!empty($gameSlug)) {
            $this->selectedGame = $gameSlug;
        }

        if (!empty($sku)) {
            $this->selectProduct($sku);
        }

        $this->target = trim($target);
        $this->zone = trim($zone);
        if (!empty($phone)) {
            $this->customerPhone = trim($phone);
        } elseif (empty($this->customerPhone) && !empty(Auth::user()->phone)) {
            $this->customerPhone = Auth::user()->phone;
        }

        if (empty($this->target)) {
            $this->dispatch('topup-alert', type: 'error', message: 'Harap isi User ID game Anda.');
            return;
        }

        if (!$this->selectedProduct) {
            $this->dispatch('topup-alert', type: 'error', message: 'Silakan pilih nominal produk top up.');
            return;
        }

        $price = (float) $this->selectedProduct['price'];
        $user = Auth::user();

        if (!$user->hasSufficientBalance($price)) {
            $this->dispatch('topup-alert', type: 'warning', message: 'Saldo akun Anda tidak mencukupi (Tersedia: Rp ' . number_format($user->balance, 0, ',', '.') . '). Silakan isi saldo atau bayar via QRIS.');
            return;
        }

        try {
            DB::beginTransaction();

            $invoiceNumber = BorderPayService::generateInvoiceNumber();
            $gameName = $this->gameDetails()['brand']['name'] ?? ucwords(str_replace('-', ' ', $this->selectedGame));
            $productName = $this->selectedProduct['name'];

            $orderNotes = json_encode([
                'type' => 'ppob_game_topup',
                'provider' => 'melostore_h2h',
                'game' => $gameName,
                'game_slug' => $this->selectedGame,
                'target' => $this->target,
                'zone' => $this->zone,
                'nickname' => $this->nickname,
                'sku_code' => $this->selectedSku,
                'customer_phone' => $this->customerPhone,
                'buyer_user_id' => $user->id,
                'payment_via' => 'wallet_balance',
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'customer_id' => null,
                'channel' => 'ppob',
                'invoice_number' => $invoiceNumber,
                'subtotal' => $price,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => $price,
                'payment_method' => 'balance',
                'amount_paid' => $price,
                'change' => 0,
                'payment_status' => 'paid',
                'notes' => $orderNotes,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => null,
                'product_name' => "Top Up {$gameName} - {$productName}",
                'quantity' => 1,
                'unit_price' => $price,
                'total_price' => $price,
            ]);

            // Potong saldo via WalletService dengan Pessimistic Locking
            $payResult = WalletService::payOrderWithBalance($user, $order, $price);

            if (!$payResult['success']) {
                DB::rollBack();
                $this->dispatch('topup-alert', type: 'error', message: $payResult['message']);
                return;
            }

            DB::commit();

            // Refresh user saldo terbaru & dispatch event sinkronisasi saldo ke seluruh halaman
            $freshUser = $user->fresh();
            $newBalance = $freshUser ? (float) $freshUser->balance : 0;
            $this->dispatch('balance-updated', 
                balance: $newBalance, 
                formattedBalance: 'Rp ' . number_format($newBalance, 0, ',', '.')
            );

            $this->orderId = $order->id;
            $this->qrisInvoice = $invoiceNumber;
            $this->paymentStatus = 'paid';
            $this->topupStatus = 'processing';
            $this->showQrisModal = true;

            // Eksekusi pemesanan langsung ke Melostore H2H
            $this->fulfillMelostoreOrder($order);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('payWithBalance error: ' . $e->getMessage());
            $this->dispatch('topup-alert', type: 'error', message: 'Gagal memproses pembayaran saldo: ' . $e->getMessage());
        }
    }

    /**
     * Pengecekan Polling Status Pembayaran QRIS BorderPay.
     */
    public function checkPaymentStatus(): void
    {
        if (!$this->showQrisModal || empty($this->qrisInvoice) || $this->paymentStatus === 'paid') {
            return;
        }

        // 1. Cek langsung dari database lokal terlebih dahulu (< 1ms)
        $order = Order::find($this->orderId);
        if ($order && $order->payment_status === 'paid') {
            $this->paymentStatus = 'paid';
            $this->topupStatus = 'processing';
            $this->fulfillMelostoreOrder($order);
            return;
        }

        // 2. Jika API BorderPay belum dikonfigurasi, lewati request jaringan eksternal
        if (!BorderPayService::isConfigured()) {
            return;
        }

        // 3. Pengecekan ke API BorderPay
        $statusResult = BorderPayService::checkPaymentStatus($this->qrisInvoice);

        if ($statusResult['success'] && $statusResult['status'] === 'paid') {
            $this->paymentStatus = 'paid';
            $this->topupStatus = 'processing';

            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $order->total_amount,
                ]);

                // Eksekusi Pemesanan ke Melostore H2H API
                $this->fulfillMelostoreOrder($order);
            }
        } elseif ($statusResult['status'] === 'expired' || ($this->qrisExpiresAt && now()->greaterThan(Carbon::parse($this->qrisExpiresAt)))) {
            // Panggil cancel endpoint ke Payment Gateway BorderPay
            BorderPayService::cancelPayment($this->qrisInvoice);

            $this->paymentStatus = 'expired';
            $this->topupStatus = 'failed';
            $this->topupMessage = 'Waktu pembayaran QRIS telah habis (15 menit).';

            if ($order && $order->payment_status === 'pending') {
                $order->update(['payment_status' => 'expired']);
            }
        }
    }

    /**
     * Kirim Order Top Up Game ke Melostore H2H.
     */
    protected function fulfillMelostoreOrder(Order $order): void
    {
        try {
            $notes = json_decode($order->notes ?? '{}', true) ?: [];

            $sku = $notes['sku_code'] ?? $this->selectedSku;
            $target = $notes['target'] ?? $this->target;
            $zone = $notes['zone'] ?? $this->zone;

            $meloResult = MeloStoreService::createTransaction(
                skuCode: $sku,
                target: $target,
                zone: !empty($zone) ? $zone : null,
                buyerTrxId: $order->invoice_number,
                additionalData: ['email' => 'buyer@topup.com']
            );

            if ($meloResult['success']) {
                $this->topupStatus = 'success';
                $this->topupSn = $meloResult['sn'] ?? 'PROCESSING';
                $this->topupMessage = 'Top up berhasil diproses ke akun game!';

                // Update catatan pesanan
                $notes['melostore_trx_id'] = $meloResult['trx_id'] ?? null;
                $notes['sn'] = $this->topupSn;
                $notes['melo_status'] = $meloResult['status'] ?? 'success';
                $order->update(['notes' => json_encode($notes)]);

                $this->dispatch('topup-alert', type: 'success', message: 'Pembayaran sukses! Top up game sedang dikirim.');
            } else {
                $this->topupStatus = 'failed';
                $this->topupMessage = $meloResult['message'] ?? 'Provider gagal memproses order.';
                
                $notes['melo_error'] = $this->topupMessage;
                $order->update(['notes' => json_encode($notes)]);

                $this->dispatch('topup-alert', type: 'warning', message: 'Pembayaran diterima, namun provider game sedang sibuk.');
            }
        } catch (\Throwable $e) {
            Log::error('fulfillMelostoreOrder error: ' . $e->getMessage());
            $this->topupStatus = 'failed';
            $this->topupMessage = 'Terjadi kendala saat menghubungi provider game.';
        }
    }

    public function closeQrisModal(): void
    {
        // Jika modal ditutup saat status masih pending, batalkan ke payment gateway
        if ($this->paymentStatus === 'pending' && !empty($this->qrisInvoice)) {
            BorderPayService::cancelPayment($this->qrisInvoice);
            if ($this->orderId) {
                Order::where('id', $this->orderId)
                    ->where('payment_status', 'pending')
                    ->update(['payment_status' => 'cancelled']);
            }
        }
        
        session()->forget('pending_topup_order_id');
        $this->showQrisModal = false;
    }

    /**
     * Memulihkan transaksi QRIS jika user membuka kembali web saat pembayaran masih pending.
     */
    public function resumePendingOrderIfAny(): void
    {
        $pendingOrderId = session('pending_topup_order_id') ?? request()->query('order_id');
        if (empty($pendingOrderId)) {
            return;
        }

        $order = Order::find($pendingOrderId);
        if (!$order) {
            session()->forget('pending_topup_order_id');
            return;
        }

        // Jika order sudah dibayar
        if ($order->payment_status === 'paid') {
            session()->forget('pending_topup_order_id');
            return;
        }

        // Jika order masih pending
        if ($order->payment_status === 'pending' && !empty($order->qris_reference_id)) {
            $notes = json_decode($order->notes ?? '{}', true) ?: [];
            $expiresAt = $order->qris_expires_at 
                ? Carbon::parse($order->qris_expires_at) 
                : $order->created_at->addMinutes(15);

            // Jika sudah lewat 15 menit, cancel transaksi
            if (now()->greaterThan($expiresAt)) {
                BorderPayService::cancelPayment($order->qris_reference_id);
                $order->update(['payment_status' => 'expired']);
                session()->forget('pending_topup_order_id');
                return;
            }

            // Pulihkan state ke modal agar user bisa langsung bayar dan poll otomatis jalan!
            $this->orderId = $order->id;
            $this->qrisInvoice = $order->qris_reference_id;
            $this->qrisQrString = $order->qris_qr_string ?? '';
            $this->qrisExpiresAt = $expiresAt->toISOString();
            $this->qrisTimeLeft = max(0, $expiresAt->diffInSeconds(now()));
            $this->paymentStatus = 'pending';
            $this->topupStatus = 'waiting_payment';
            $this->showQrisModal = true;

            // Muat produk dan game terkait
            if (!empty($notes['game_slug'])) {
                $this->selectedGame = $notes['game_slug'];
                $this->target = $notes['target'] ?? '';
                $this->zone = $notes['zone'] ?? '';
                $this->selectedSku = $notes['sku_code'] ?? '';
            }

            // Seketika render modal dan langsung picu cek status ke BorderPay
            $this->dispatch('render-qris-topup', qrString: $this->qrisQrString);
            $this->checkPaymentStatus();
        }
    }

    public function render()
    {
        $categories = [
            'All' => 'Semua',
            'Game' => 'Game',
            'Pulsa & Data' => 'Pulsa & Data',
            'Gift Card' => 'Gift Card / Voucher',
            'E-Wallet' => 'E-Wallet',
            'PLN' => 'PLN & Listrik',
            'Social' => 'Social & Live',
            'Aplikasi' => 'Aplikasi & Streaming',
        ];

        $catalog = [];
        if (!empty($this->selectedGame)) {
            $catalog[$this->selectedGame] = $this->gameDetails();
        }

        return view('livewire.topup-game', [
            'allGames' => $this->gamesList,
            'catalog' => $catalog,
            'categories' => $categories,
        ]);
    }
}
