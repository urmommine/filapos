<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreSetting;
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

    public function mount()
    {
        // Load tax percentage from settings
        $this->defaultTax = (float) StoreSetting::get(StoreSetting::TAX_PERCENTAGE, 0);
        $this->tax = $this->defaultTax;
    }

    public function openProfileModal()
    {
        $user = Auth::user();
        $this->profileName = $user->name;
        $this->profileEmail = $user->email;
        $this->profilePassword = '';
        $this->profilePasswordConfirmation = '';
        $this->showProfileModal = true;
    }

    public function updateProfile()
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

    public function openCustomerModal()
    {
        $this->showCustomerModal = true;
        $this->customerSearch = ''; // Reset search
    }

    public function selectCustomer($customerId)
    {
        $this->selectedCustomerId = $customerId;
        $this->selectedCustomer = Customer::find($customerId);
        $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Pelanggan dipilih: ' . $this->selectedCustomer->name);
        $this->showCustomerModal = false;
    }

    public function removeCustomer()
    {
        $this->selectedCustomerId = null;
        $this->selectedCustomer = null;
        $this->dispatch('toastMagic', status: 'info', title: 'Informasi', message: 'Pelanggan dihapus');
    }

    public function render()
    {
        $categories = Category::active()->withCount('products')->get();

        $products = Product::query()
            ->active()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->orderBy('name')
            ->take($this->perPage)
            ->get();

        $products->transform(function ($product) {
            $cartItem = collect($this->cart)->where('product_id', $product->id)->first();
            $product->available_stock = $product->stock - ($cartItem['quantity'] ?? 0);
            return $product;
        });

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
            'categories' => $categories,
            'products' => $products,
            'customers' => $customers,
            'storeName' => StoreSetting::get(StoreSetting::STORE_NAME, 'POS Store'),
        ]);
    }

    public function selectCategory(?int $categoryId)
    {
        $this->selectedCategory = $categoryId === $this->selectedCategory ? null : $categoryId;
        $this->perPage = 12;
    }

    public function updatedSearch()
    {
        $this->perPage = 12;
    }

    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function addToCart(int $productId)
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
        $cartKey = array_search($productId, array_column($this->cart, 'product_id'));

        if ($cartKey !== false) {
            // Check stock before increasing
            if ($this->cart[$cartKey]['quantity'] >= $product->stock) {
                $this->dispatch('toastMagic', status: 'warning', title: 'Peringatan', message: 'Stok tidak mencukupi');
                return;
            }
            $this->cart[$cartKey]['quantity']++;
            $this->cart[$cartKey]['total'] = $this->cart[$cartKey]['quantity'] * $this->cart[$cartKey]['price'];
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

    public function handleBarcodeScan($scannedBarcode = null)
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

    #[On('barcodeScanned')]
    public function handleBarcode(string $barcode)
    {
        $product = Product::byCode($barcode)->first();

        if ($product) {
            $this->addToCart($product->id);
        } else {
            $this->notifyProductNotFound($barcode, true);
        }
    }

    private function clearSearch(): void
    {
        $this->search = '';
        $this->dispatch('clear-search-input');
    }

    private function notifyProductNotFound(string $barcode, bool $forceNotify = false): void
    {
        // Only show error if forced or it looks like a barcode/SKU (alphanumeric and >= 3 digits)
        $isProbableCode = preg_match('/^[a-zA-Z0-9]+$/', $barcode) && strlen($barcode) >= 3;

        if ($forceNotify || $isProbableCode) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Produk dengan kode ' . $barcode . ' tidak ditemukan');
        }
    }

    public function incrementQuantity(int $index)
    {
        if (isset($this->cart[$index])) {
            $product = Product::find($this->cart[$index]['product_id']);

            if ($product && $this->cart[$index]['quantity'] < $product->stock) {
                $this->cart[$index]['quantity']++;
                $this->cart[$index]['total'] = $this->cart[$index]['quantity'] * $this->cart[$index]['price'];
                $this->calculateTotals();
            } else {
                $this->dispatch('toastMagic', status: 'warning', title: 'Peringatan', message: 'Stok tidak mencukupi');
            }
        }
    }

    public function decrementQuantity(int $index)
    {
        if (isset($this->cart[$index])) {
            if ($this->cart[$index]['quantity'] > 1) {
                $this->cart[$index]['quantity']--;
                $this->cart[$index]['total'] = $this->cart[$index]['quantity'] * $this->cart[$index]['price'];
            } else {
                $this->removeFromCart($index);
            }
            $this->calculateTotals();
        }
    }

    public function removeFromCart(int $index)
    {
        if (isset($this->cart[$index])) {
            $name = $this->cart[$index]['name'];
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Re-index array
            $this->calculateTotals();
            $this->dispatch('toastMagic', status: 'info', title: $name . ' dihapus', message: '');
        }
    }

    public function clearCart()
    {
        $this->resetCartState();
        $this->dispatch('toastMagic', status: 'info', title: 'Informasi', message: 'Keranjang dikosongkan');
    }

    private function resetCartState(): void
    {
        $this->cart = [];
        $this->discount = 0;
        $this->discountValue = '';
        $this->selectedCustomerId = null;
        $this->selectedCustomer = null;
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = array_sum(array_column($this->cart, 'total'));

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
    public function openCheckout()
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

    #[On('closeModal')]
    public function closeModal()
    {
        $this->showCheckoutModal = false;
        $this->showDiscountModal = false;
        $this->showProfileModal = false;
        $this->showCustomerModal = false;
    }

    public function setPaymentMethod(string $method)
    {
        $this->paymentMethod = $method;

        // For non-cash, set amount paid to exact total
        if ($method !== 'cash') {
            $this->amountPaid = $this->total;
            $this->change = 0;
        }
    }

    public function setQuickAmount(float $amount)
    {
        $this->amountPaid = $amount;
        $this->calculateChange();
    }

    public function setExactAmount()
    {
        $this->amountPaid = $this->total;
        $this->change = 0;
    }

    public function updatedAmountPaid()
    {
        $this->calculateChange();
    }

    public function calculateChange()
    {
        $this->change = max(0, (float) $this->amountPaid - $this->total);
    }

    public function openDiscountModal()
    {
        $this->showDiscountModal = true;
    }

    public function applyDiscount()
    {
        $this->calculateTotals();
        $this->showDiscountModal = false;
        $this->dispatch('toastMagic', status: 'success', title: 'Sukses', message: 'Diskon diterapkan');
    }

    public function toggleTax()
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

    public function processPayment()
    {
        if (empty($this->cart)) {
            $this->dispatch('toastMagic', status: 'error', title: 'Error', message: 'Keranjang kosong');
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
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax > 0 ? ($this->subtotal - $this->discount) * ($this->tax / 100) : 0,
                'total_amount' => $this->total,
                'payment_method' => $this->paymentMethod,
                'amount_paid' => (float) $this->amountPaid,
                'change' => $this->change,
                'payment_status' => 'paid',
            ]);

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

                // Reduce stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->reduceStock($item['quantity']);
                }
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
}
