<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Services\BorderPayService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.pos')]
class PosTerminal extends Component
{
    // Search and filter
    public string $search = '';
    public ?int $selectedCategory = null;
    public int $perPage = 12;

    // Cart
    public array $cart = [];
    public float $subtotal = 0;
    public float $discount = 0;
    public float $discountType = 0; // 0 = nominal, 1 = percentage
    public $discountValue = '';
    public float $tax = 0;
    public float $defaultTax = 0;
    public float $total = 0;

    // Checkout modal
    public bool $showCheckoutModal = false;
    public string $paymentMethod = 'cash';
    public $amountPaid = '';
    public $change = 0;

    // BorderPay QRIS State
    public bool $showQrisModal = false;
    public ?string $qrisReferenceId = null;
    public ?string $qrisQrString = null;
    public ?string $qrisExpiresAt = null;
    public float $qrisAmount = 0;
    public float $qrisFee = 0;
    public bool $isCheckingQris = false;
    public bool $qrisPaid = false;
    public string $qrisErrorMessage = '';

    // Discount modal
    public bool $showDiscountModal = false;

    // Profile modal
    public bool $showProfileModal = false;
    public string $profileName = '';
    public string $profileEmail = '';
    public string $profilePassword = '';
    public string $profilePasswordConfirmation = '';

    // Customer
    public bool $showCustomerModal = false;
    public string $customerSearch = '';
    public ?int $selectedCustomerId = null;
    public ?Customer $selectedCustomer = null;

    // Create Customer
    public bool $showCreateCustomerModal = false;
    public string $newCustomerName = '';
    public string $newCustomerPhone = '';
    public string $newCustomerEmail = '';
    public string $newCustomerAddress = '';

    // PERF: Cached data — loaded once in mount(), not on every render
    public Collection $cachedCategories;
    public string $storeName = '';

    /**
     * Inisialisasi komponen: load setting pajak, kategori, dan nama toko.
     */
    public function mount(): void
    {
        // Load tax percentage from settings
        $this->defaultTax = (float) StoreSetting::get(StoreSetting::TAX_PERCENTAGE, 0);
        $this->tax = $this->defaultTax;

        // PERF: Cache categories and store name — these rarely change during a POS session
        $this->cachedCategories = Category::active()->withCount('products')->get();
        $this->storeName = StoreSetting::get(StoreSetting::STORE_NAME, 'POS Store');
    }

    /**
     * Membuka modal profil dan mengisi data user saat ini.
     */
    public function openProfileModal(): void
    {
        $user = Auth::user();
        $this->profileName = $user->name;
        $this->profileEmail = $user->email;
        $this->profilePassword = '';
        $this->profilePasswordConfirmation = '';
        $this->showProfileModal = true;
    }

    /**
     * Memperbarui data profil user (nama, email, password).
     */
    public function updateProfile(): void
    {
        $this->validate([
            'profileName' => 'required|string|max:255',
            'profileEmail' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'profilePassword' => 'nullable|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->name = $this->profileName;
        $user->email = $this->profileEmail;

        if (!empty($this->profilePassword)) {
            $user->password = bcrypt($this->profilePassword);
        }

        $user->save();

        $this->showProfileModal = false;
        $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Profil berhasil diperbarui');
    }

    /**
     * Membuka modal untuk memilih pelanggan.
     */
    public function openCustomerModal(): void
    {
        $this->showCustomerModal = true;
        $this->customerSearch = ''; // Reset search
    }

    /**
     * Membuka modal untuk membuat data pelanggan baru.
     */
    public function openCreateCustomerModal(): void
    {
        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
        $this->newCustomerEmail = '';
        $this->newCustomerAddress = '';
        $this->showCreateCustomerModal = true;
        $this->showCustomerModal = false;
    }

    /**
     * Menyimpan data pelanggan baru ke database dan langsung memilihnya.
     */
    public function createCustomer(): void
    {
        $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'required|string|max:20',
            'newCustomerEmail' => 'nullable|email|max:255',
            'newCustomerAddress' => 'nullable|string',
        ]);

        $customer = Customer::create([
            'name' => $this->newCustomerName,
            'phone' => $this->newCustomerPhone,
            'email' => $this->newCustomerEmail ?: null,
            'address' => $this->newCustomerAddress ?: null,
        ]);

        $this->selectCustomer($customer->id);
        $this->showCreateCustomerModal = false;
        $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Pelanggan baru berhasil ditambahkan');
    }

    /**
     * Memilih pelanggan untuk transaksi ini.
     */
    public function selectCustomer(int $customerId): void
    {
        $this->selectedCustomerId = $customerId;
        $this->selectedCustomer = Customer::find($customerId);
        $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Pelanggan dipilih: ' . $this->selectedCustomer->name);
        $this->showCustomerModal = false;
    }

    /**
     * Menghapus pelanggan yang terpilih dari transaksi.
     */
    public function removeCustomer(): void
    {
        $this->selectedCustomerId = null;
        $this->selectedCustomer = null;
        $this->dispatch('toastMagic', status: 'info', title: 'Informasi', message: 'Pelanggan dihapus');
    }

    /**
     * Me-render view POS dengan data produk dan pelanggan yang difilter.
     */
    public function render()
    {
        // PERF: Products query is reactive (depends on search/category/perPage)
        // This is the only necessary DB query in render()
        $products = Product::query()
            ->active()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->orderBy('name')
            ->take($this->perPage)
            ->get();

        // PERF: O(1) lookup map instead of O(n) scan per product
        $cartByProductId = collect($this->cart)->keyBy('product_id');
        $products->transform(function ($product) use ($cartByProductId) {
            $cartItem = $cartByProductId->get($product->id);
            $product->available_stock = $product->stock - ($cartItem['quantity'] ?? 0);
            return $product;
        });

        // Only query customers when the modal is actually open
        $customers = [];
        if ($this->showCustomerModal) {
            $customers = Customer::query()
                ->when($this->customerSearch, fn($q) => $q->where('name', 'like', '%' . $this->customerSearch . '%')
                    ->orWhere('phone', 'like', '%' . $this->customerSearch . '%')
                    ->orWhere('email', 'like', '%' . $this->customerSearch . '%'))
                ->take(10)
                ->get();
        }

        return view('livewire.pos-terminal', [
            'categories' => $this->cachedCategories,
            'products' => $products,
            'customers' => $customers,
            'storeName' => $this->storeName,
        ]);
    }

    /**
     * Memfilter produk berdasarkan kategori yang dipilih.
     */
    public function selectCategory(?int $categoryId): void
    {
        $this->selectedCategory = $categoryId === $this->selectedCategory ? null : $categoryId;
        $this->perPage = 12;
    }

    /**
     * Reset pagination saat query pencarian berubah.
     */
    public function updatedSearch(): void
    {
        $this->perPage = 12;
    }

    /**
     * Menambah jumlah produk yang ditampilkan (Lazy Loading/Load More).
     */
    public function loadMore(): void
    {
        $this->perPage += 12;
    }

    /**
     * Menambahkan produk ke keranjang belanja.
     */
    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_active) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Produk tidak ditemukan');
            return;
        }

        if ($product->stock <= 0) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Stok produk ' . $product->name . ' habis');
            return;
        }

        // Check if already in cart
        $cartKey = $this->findCartIndex($productId);

        if ($cartKey !== false) {
            // PERF: Use cached stock from cart — zero extra DB queries
            if ($this->cart[$cartKey]['quantity'] >= $product->stock) {
                $this->dispatch('toastMagic', status: 'warning', title: 'Peringatan', message: 'Stok tidak mencukupi');
                return;
            }
            $this->cart[$cartKey]['quantity']++;
            $this->recalculateCartItemTotal($cartKey);
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->selling_price,
                'quantity' => 1,
                'total' => (float) $product->selling_price,
                'image' => $product->image,
                'stock' => $product->stock,
            ];
        }

        $this->calculateTotals();
        $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: $product->name . ' ditambahkan');
    }

    /**
     * Menangani input barcode untuk pencarian produk cepat.
     */
    public function handleBarcodeScan($scannedBarcode = null): void
    {
        $barcode = $scannedBarcode ?? $this->search;

        if (empty($barcode)) {
            return;
        }

        $product = Product::byCode($barcode)->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->clearSearch();
        } else {
            $this->notifyProductNotFound($barcode, false);
        }
    }

    // #[On('barcodeScanned')]
    // public function handleBarcode(string $barcode): void
    // {
    //     $product = Product::byCode($barcode)->first();

    //     if ($product) {
    //         $this->addToCart($product->id);
    //     } else {
    //         $this->notifyProductNotFound($barcode, true);
    //     }
    // }

    /**
     * Membersihkan input pencarian.
     */
    private function clearSearch(): void
    {
        $this->search = '';
        $this->dispatch('clear-search-input');
    }

    /**
     * Menampilkan notifikasi jika produk/barcode tidak ditemukan.
     */
    private function notifyProductNotFound(string $barcode, bool $forceNotify = false): void
    {
        // Only show error if forced or it looks like a barcode/SKU (alphanumeric and >= 3 digits)
        $isProbableCode = preg_match('/^[a-zA-Z0-9]+$/', $barcode) && strlen($barcode) >= 3;

        if ($forceNotify || $isProbableCode) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Produk dengan kode ' . $barcode . ' tidak ditemukan');
        }
    }

    /**
     * Menambah jumlah qty item di keranjang.
     */
    public function incrementQuantity(int $index): void
    {
        if (isset($this->cart[$index])) {
            // PERF: Use cached stock from cart array — no Product::find() query needed
            if ($this->cart[$index]['quantity'] < $this->cart[$index]['stock']) {
                $this->cart[$index]['quantity']++;
                $this->recalculateCartItemTotal($index);
                $this->calculateTotals();
            } else {
                $this->dispatch('toastMagic', status: 'warning', title: 'Peringatan', message: 'Stok tidak mencukupi');
            }
        }
    }

    /**
     * Mengurangi jumlah qty item di keranjang atau menghapusnya jika qty = 1.
     */
    public function decrementQuantity(int $index): void
    {
        if (isset($this->cart[$index])) {
            if ($this->cart[$index]['quantity'] > 1) {
                $this->cart[$index]['quantity']--;
                $this->recalculateCartItemTotal($index);
            } else {
                $this->removeFromCart($index);
            }
            $this->calculateTotals();
        }
    }

    /**
     * Menghapus item tertentu dari keranjang.
     */
    public function removeFromCart(int $index): void
    {
        if (isset($this->cart[$index])) {
            $name = $this->cart[$index]['name'];
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Re-index array
            $this->calculateTotals();
            $this->dispatch('toastMagic', status: 'info', title: $name . ' dihapus', message: '');
        }
    }

    /**
     * Mengosongkan seluruh isi keranjang.
     */
    public function clearCart(): void
    {
        $this->resetCartState();
        $this->dispatch('toastMagic', status: 'info', title: 'Informasi', message: 'Keranjang dikosongkan');
    }

    /**
     * Reset state keranjang dan diskon ke awal.
     */
    private function resetCartState(): void
    {
        $this->cart = [];
        $this->discount = 0;
        $this->discountValue = '';
        $this->selectedCustomerId = null;
        $this->selectedCustomer = null;
        $this->calculateTotals();
    }

    /**
     * Menghitung ulang subtotal, diskon, pajak, dan total akhir.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = array_sum(array_column($this->cart, 'total')); //ambil all nilai di kolom total yang ada di $cart[]

        // Calculate discount
        if ($this->discountType == 1 && (float) $this->discountValue > 0) {
            // Percentage discount
            $this->discount = $this->subtotal * ((float) $this->discountValue / 100);
        } else {
            $this->discount = (float) $this->discountValue;
        }

        // Calculate total (with tax if applicable)
        $afterDiscount = $this->subtotal - $this->discount;
        $taxAmount = $this->tax > 0 ? $afterDiscount * ($this->tax / 100) : 0;
        $this->total = $afterDiscount + $taxAmount;

        // Update change if checkout modal is open
        if ($this->showCheckoutModal) {
            $this->calculateChange();
        }
    }

    #[On('openCheckout')]
    /**
     * Membuka modal checkout/pembayaran.
     */
    public function openCheckout(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toastMagic', status: 'warning', title: 'Peringatan', message: 'Keranjang masih kosong');
            return;
        }

        $this->amountPaid = '';
        $this->change = 0;
        $this->paymentMethod = 'cash';
        $this->showCheckoutModal = true;
    }

   // #[On('closeModal')]
    /**
     * Menutup semua modal yang sedang terbuka.
     */
    public function closeModal(): void
    {
        $this->showCheckoutModal = false;
        $this->showDiscountModal = false;
        $this->showProfileModal = false;
        $this->showCustomerModal = false;
        $this->showCreateCustomerModal = false;
        $this->showQrisModal = false;
    }

    /**
     * Mengatur metode pembayaran (Cash, qris, tf ).
     */
    public function setPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;

        // For non-cash, set amount paid to exact total
        if ($method !== 'cash') {
            $this->amountPaid = $this->total;
            $this->change = 0;
        }
    }

    /**
     * Mengatur nominal template pembayaran cepat 10, 20, 100k (Quick Amount).
     */
    public function setQuickAmount(float $amount): void
    {
        $this->amountPaid = $amount;
        $this->calculateChange();
    }

    /**
     * Mengatur nominal pembayaran sesuai dengan total tagihan (Uang Pas).
     */
    public function setExactAmount(): void
    {
        $this->amountPaid = $this->total;
        $this->change = 0;
    }

    /**
     * Event hook saat input nominal bayar berubah.
     */
    public function updatedAmountPaid(): void
    {
        $this->calculateChange();
    }

    /**
     * Menghitung jumlah uang kembalian.
     */
    public function calculateChange(): void
    {
        $this->change = max(0, (float) $this->amountPaid - $this->total);
    }

    /**
     * Membuka modal input diskon.
     */
    public function openDiscountModal(): void
    {
        $this->showDiscountModal = true;
    }

    /**
     * Menerapkan diskon yang telah diinput ke keranjang.
     */
    public function applyDiscount(): void
    {
        $this->calculateTotals();
        $this->showDiscountModal = false;
        $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Diskon diterapkan');
    }

    /**
     * Mengaktifkan/menonaktifkan penerapan pajak pada transaksi.
     */
    public function toggleTax(): void
    {
        if ($this->tax > 0) {
            $this->tax = 0;
            $this->dispatch('toastMagic', status: 'info', title: 'Informasi', message: 'Pajak dinonaktifkan');
        } else {
            $this->tax = $this->defaultTax;
            $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Pajak diaktifkan (' . $this->tax . '%)');
        }
        $this->calculateTotals();
    }

    /**
     * Memproses pembayaran, menyimpan order ke DB, dan mengurangi stok produk.
     */
    public function processPayment(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Keranjang kosong');
            return;
        }

        // Jika metode pembayaran QRIS, inisialisasi QRIS BorderPay
        if ($this->paymentMethod === 'qris') {
            $this->initiateQrisPayment();
            return;
        }

        if ($this->paymentMethod === 'cash' && (float) $this->amountPaid < $this->total) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Jumlah bayar kurang dari total');
            return;
        }

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_id' => $this->selectedCustomerId,
                'channel' => 'pos',
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax > 0 ? ($this->subtotal - $this->discount) * ($this->tax / 100) : 0,
                'total_amount' => $this->total,
                'payment_method' => $this->paymentMethod,
                'amount_paid' => (float) $this->amountPaid,
                'change' => $this->change,
                'payment_status' => 'paid',
            ]);

            // PERF: Single query loads all cart products — prevents N+1 inside the loop
            $productIds = array_column($this->cart, 'product_id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Create order items and reduce stock
            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['total'],
                ]);

                // Reduce stock using pre-loaded product
                $product = $products->get($item['product_id']);
                $product?->reduceStock($item['quantity']);
            }

            DB::commit();

            // Success cleanup
            $transactionInvoice = $order->invoice_number;
            $transactionId = $order->id;

            $this->resetCartState();
            $this->showCheckoutModal = false;

            // Dispatch event to print receipt
            $this->dispatch('printReceipt', orderId: $transactionId);
            $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Transaksi berhasil! Invoice: ' . $transactionInvoice);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Inisialisasi pembayaran QRIS melalui BorderPay API.
     */
    public function initiateQrisPayment(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('toastMagic', status: 'warning', title: 'Peringatan', message: 'Keranjang masih kosong');
            return;
        }

        if ($this->total < 1000) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Nominal pembayaran QRIS minimal Rp1.000');
            return;
        }

        $borderPay = app(BorderPayService::class);
        $invoiceNumber = BorderPayService::generateInvoiceNumber();

        $result = $borderPay->createQrisPayment($this->total, $invoiceNumber);

        if (!$result['success']) {
            $msg = $result['message'] ?? 'Terjadi kendala pada server BorderPay';
            $errStr = is_array($msg) ? ($msg['message'] ?? json_encode($msg)) : (string) $msg;
            $this->dispatch('toastMagic', status: 'error', title: 'Gagal Membuat QRIS', message: $errStr);
            return;
        }

        $this->qrisReferenceId = $result['reference_id'];
        $this->qrisQrString = $result['qr_string'];
        $this->qrisExpiresAt = $result['expires_at'];
        $this->qrisAmount = $result['amount'];
        $this->qrisFee = $result['fee'] ?? 0;
        $this->isCheckingQris = false;
        $this->qrisPaid = false;
        $this->qrisErrorMessage = '';

        // Buka modal QRIS dan sembunyikan modal checkout tunai
        $this->showCheckoutModal = false;
        $this->showQrisModal = true;

        // Berikan sinyal ke JavaScript untuk merender QR Code
        $this->dispatch('render-qris', qrString: $this->qrisQrString);
        $this->dispatch('toastMagic', status: 'info', title: 'QRIS Siap', message: 'Silakan minta pelanggan memindai QR Code');
    }

    /**
     * Polling untuk mengecek status pelunasan QRIS BorderPay.
     * Dipanggil secara otomatis oleh timer frontend setiap ~3 detik.
     */
    public function checkQrisStatus(): void
    {
        if (!$this->showQrisModal || empty($this->qrisReferenceId) || $this->qrisPaid) {
            return;
        }

        $this->isCheckingQris = true;
        $borderPay = app(BorderPayService::class);
        $statusCheck = $borderPay->checkPaymentStatus($this->qrisReferenceId);
        $this->isCheckingQris = false;

        if ($statusCheck['success'] && $statusCheck['is_paid']) {
            $this->finalizeQrisOrder();
        }
    }

    /**
     * Memproses order setelah verifikasi status BorderPay menyatakan 'paid'.
     */
    private function finalizeQrisOrder(): void
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_id' => $this->selectedCustomerId,
                'channel' => 'pos',
                'invoice_number' => $this->qrisReferenceId,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax > 0 ? ($this->subtotal - $this->discount) * ($this->tax / 100) : 0,
                'total_amount' => $this->total,
                'payment_method' => 'qris',
                'amount_paid' => $this->total,
                'change' => 0,
                'payment_status' => 'paid',
                'qris_reference_id' => $this->qrisReferenceId,
                'qris_qr_string' => $this->qrisQrString,
                'qris_expires_at' => $this->qrisExpiresAt,
                'notes' => 'Lunas via BorderPay QRIS (' . $this->qrisReferenceId . ')',
            ]);

            $productIds = array_column($this->cart, 'product_id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['total'],
                ]);

                $product = $products->get($item['product_id']);
                $product?->reduceStock($item['quantity']);
            }

            DB::commit();

            $transactionInvoice = $order->invoice_number;
            $transactionId = $order->id;

            $this->qrisPaid = true;
            $this->showQrisModal = false;
            $this->resetCartState();

            // Cetak struk otomatis dan tampilkan notifikasi sukses
            $this->dispatch('printReceipt', orderId: $transactionId);
            $this->dispatch('toastMagic', status: 'success', title: 'Pembayaran QRIS Berhasil!', message: 'Invoice: ' . $transactionInvoice);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Gagal menyelesaikan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Membatalkan transaksi QRIS yang sedang menunggu pembayaran.
     */
    public function cancelQrisPayment(): void
    {
        if (!empty($this->qrisReferenceId)) {
            $borderPay = app(BorderPayService::class);
            $borderPay->cancelPayment($this->qrisReferenceId);
        }

        $this->showQrisModal = false;
        $this->qrisReferenceId = null;
        $this->qrisQrString = null;
        $this->qrisPaid = false;

        $this->dispatch('toastMagic', status: 'info', title: 'Dibatalkan', message: 'Transaksi QRIS berhasil dibatalkan');
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    /**
     * Mencari index item di keranjang berdasarkan ID produk.
     */
    private function findCartIndex(int $productId): int|false
    {
        return array_search($productId, array_column($this->cart, 'product_id'));
    }

    /**
     * Menghitung ulang total harga untuk satu baris item di keranjang.
     */
    private function recalculateCartItemTotal(int $index): void
    {
        $this->cart[$index]['total'] = $this->cart[$index]['quantity'] * $this->cart[$index]['price'];
    }
}
