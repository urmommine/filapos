<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\WalletTransaction;
use App\Services\BorderPayService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.topup')]
#[Title('Akun & Saldo Member - FilaPOS Games Hub')]
class TopupAccount extends Component
{
    use WithPagination;

    public string $activeTab = 'deposit'; // 'deposit', 'mutations', 'orders'

    // Deposit form state
    public int $depositAmount = 50000;
    public ?int $activeDepositId = null;
    public string $depositReferenceId = '';
    public string $depositQrString = '';
    public string $depositExpiresAt = '';
    public int $depositTimeLeft = 900;
    public bool $showDepositModal = false;
    public string $depositStatus = 'pending'; // 'pending', 'paid', 'expired', 'cancelled'
    public string $errorMessage = '';

    public array $presetAmounts = [
        10000,
        25000,
        50000,
        100000,
        200000,
        500000,
    ];

    public function mount(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('topups.login'));
            return;
        }

        // Cek jika ada deposit pending aktif di session atau database
        $this->checkExistingActiveDeposit();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function selectPreset(int $amount): void
    {
        $this->depositAmount = $amount;
    }

    /**
     * Cek jika ada transaksi deposit pending yang belum kadaluwarsa
     */
    protected function checkExistingActiveDeposit(): void
    {
        $pendingTx = WalletTransaction::where('user_id', Auth::id())
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(15))
            ->latest()
            ->first();

        if ($pendingTx && !empty($pendingTx->qris_qr_string)) {
            $expiresAt = $pendingTx->qris_expires_at 
                ? Carbon::parse($pendingTx->qris_expires_at) 
                : $pendingTx->created_at->addMinutes(15);

            if (now()->lessThan($expiresAt)) {
                $this->activeDepositId = $pendingTx->id;
                $this->depositReferenceId = $pendingTx->reference_id;
                $this->depositQrString = $pendingTx->qris_qr_string;
                $this->depositExpiresAt = $expiresAt->toISOString();
                $this->depositTimeLeft = max(0, $expiresAt->diffInSeconds(now()));
                $this->depositStatus = 'pending';
                $this->showDepositModal = true;

                $this->dispatch('render-deposit-qris', qrString: $this->depositQrString);
            }
        }
    }

    /**
     * Buat Tagihan Deposit Saldo QRIS
     */
    public function createDeposit(): void
    {
        $this->errorMessage = '';

        if ($this->depositAmount < 1000) {
            $this->errorMessage = 'Nominal pengisian saldo minimal Rp 1.000.';
            return;
        }

        if ($this->depositAmount > 5000000) {
            $this->errorMessage = 'Maksimal deposit saldo per transaksi adalah Rp 5.000.000.';
            return;
        }

        $res = WalletService::createDepositQris(Auth::user(), (float) $this->depositAmount);

        if (!$res['success']) {
            $msg = $res['message'] ?? 'Gagal membuat QRIS deposit.';
            $this->errorMessage = is_array($msg) ? ($msg['message'] ?? json_encode($msg)) : (string) $msg;
            return;
        }

        $this->activeDepositId = $res['transaction_id'];
        $this->depositReferenceId = $res['reference_id'];
        $this->depositQrString = $res['qr_string'];
        $this->depositExpiresAt = $res['expires_at'];
        $this->depositTimeLeft = $res['time_left'];
        $this->depositStatus = 'pending';
        $this->showDepositModal = true;

        $this->dispatch('render-deposit-qris', qrString: $this->depositQrString);
    }

    /**
     * Polling Status Pembayaran Deposit
     */
    public function checkDepositStatus(): void
    {
        if (!$this->showDepositModal || empty($this->activeDepositId) || $this->depositStatus === 'paid') {
            return;
        }

        $tx = WalletTransaction::find($this->activeDepositId);
        if (!$tx) {
            return;
        }

        if ($tx->status === 'completed') {
            $this->depositStatus = 'paid';
            $this->dispatch('deposit-success', message: 'Saldo berhasil ditambahkan ke akun Anda!');
            return;
        }

        // Cek API BorderPay
        if (!BorderPayService::isConfigured()) {
            return;
        }

        $statusResult = BorderPayService::checkPaymentStatus($this->depositReferenceId);

        if ($statusResult['success'] && $statusResult['status'] === 'paid') {
            WalletService::confirmDeposit($tx);
            $this->depositStatus = 'paid';
            $this->dispatch('deposit-success', message: 'Pembayaran diterima! Saldo bertambah.');
        } elseif ($statusResult['status'] === 'expired' || ($this->depositExpiresAt && now()->greaterThan(Carbon::parse($this->depositExpiresAt)))) {
            BorderPayService::cancelPayment($this->depositReferenceId);
            $tx->update(['status' => 'expired']);
            $this->depositStatus = 'expired';
        }
    }

    /**
     * Batalkan Permintaan Deposit Aktif
     */
    public function cancelDeposit(): void
    {
        if (!empty($this->depositReferenceId)) {
            BorderPayService::cancelPayment($this->depositReferenceId);
        }

        if ($this->activeDepositId) {
            WalletTransaction::where('id', $this->activeDepositId)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }

        $this->closeDepositModal();
    }

    public function closeDepositModal(): void
    {
        $this->showDepositModal = false;
        $this->activeDepositId = null;
        $this->depositReferenceId = '';
        $this->depositQrString = '';
    }

    public function render()
    {
        $user = Auth::user();

        // Mutasi Saldo
        $mutations = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'mutations_page');

        // Riwayat Pembelian Game
        $userPhone = $user->phone;
        $userId = $user->id;

        $orders = Order::where(function ($query) use ($userId, $userPhone) {
            $query->where('user_id', $userId)
                ->orWhere('notes', 'like', '%"buyer_user_id":' . $userId . '%')
                ->orWhere('notes', 'like', '%"buyer_user_id":"' . $userId . '"%');
            if (!empty($userPhone)) {
                $query->orWhere('notes', 'like', '%"customer_phone":"' . $userPhone . '"%');
            }
        })
            ->with(['items'])
            ->latest()
            ->paginate(10, ['*'], 'orders_page');

        return view('livewire.topup-account', [
            'user' => $user,
            'mutations' => $mutations,
            'orders' => $orders,
        ]);
    }
}
