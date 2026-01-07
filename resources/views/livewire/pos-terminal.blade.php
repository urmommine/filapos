<div class="h-full flex flex-col">
<!-- Top Navigation Bar -->
<header class="flex shrink-0 items-center justify-between whitespace-nowrap border-b border-solid border-border-dark px-6 py-3 bg-background-dark z-50">
    <div class="flex items-center gap-4 text-white">
        <div class="size-10 flex items-center justify-center bg-primary/10 rounded-lg text-primary">
            <span class="material-symbols-outlined text-2xl">point_of_sale</span>
        </div>
        <div>
            <h2 class="text-white text-lg font-bold leading-tight tracking-tight">{{ $storeName }}</h2>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="block size-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                <span class="text-xs text-[#b89d9f] font-medium">Online • {{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-end gap-6" x-data="{ open: false }">
        <!-- Shortcut Hint -->
        <div class="hidden lg:flex gap-4 text-[#b89d9f] text-xs font-medium bg-surface-dark px-4 py-2 rounded-lg border border-border-dark/50">
            <span class="flex items-center gap-1"><kbd class="bg-[#382929] px-1.5 py-0.5 rounded text-white border border-[#533c3d]">F2</kbd> Search</span>
            <span class="flex items-center gap-1"><kbd class="bg-[#382929] px-1.5 py-0.5 rounded text-white border border-[#533c3d]">F9</kbd> Pay</span>
            <span class="flex items-center gap-1"><kbd class="bg-[#382929] px-1.5 py-0.5 rounded text-white border border-[#533c3d]">F10</kbd> Tax</span>
        </div>
        <div class="flex gap-2">
            <button class="flex size-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-surface-dark text-white hover:bg-[#382929] transition-colors border border-border-dark/50">
                <span class="material-symbols-outlined">fullscreen</span>
            </button>
            <button class="flex size-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-surface-dark text-white hover:bg-[#382929] transition-colors relative border border-border-dark/50">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-2.5 right-2.5 size-2 bg-primary rounded-full border-2 border-surface-dark"></span>
            </button>
            <button class="flex size-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-surface-dark text-white hover:bg-[#382929] transition-colors border border-border-dark/50" @click="open = !open">
                <span class="material-symbols-outlined">settings</span>
            </button>
        </div>
        
        <!-- User Menu Dropdown -->
        <div class="relative">
             <div class="flex items-center gap-3 pl-4 border-l border-border-dark cursor-pointer" @click="open = !open">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-white leading-none">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[#b89d9f] mt-1">{{ auth()->user()->email }}</p>
                </div>
                <!-- Initial Avatar -->
                <div class="bg-primary/20 flex items-center justify-center text-primary font-bold rounded-lg size-10 ring-2 ring-border-dark">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>

            <div x-show="open" @click.outside="open = false" x-cloak
                 class="absolute right-0 top-full mt-2 w-48 bg-surface-dark border border-border-dark rounded-xl shadow-lg z-50 overflow-hidden">
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="block px-4 py-3 text-sm text-gray-300 hover:bg-[#382929] hover:text-white transition-colors">
                    Dashboard Admin
                </a>
                <button wire:click="openProfileModal(); open = false" class="w-full text-left block px-4 py-3 text-sm text-gray-300 hover:bg-[#382929] hover:text-white transition-colors">
                    Profil Saya
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-3 text-sm text-red-400 hover:bg-[#382929] hover:text-red-300 transition-colors border-t border-border-dark">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
<!-- Main Layout -->
<main class="flex flex-1 overflow-hidden relative" x-data="{ mobileCartOpen: false }">
    <!-- Left Panel: Inventory (70%) -->
    <section class="w-full lg:w-[70%] flex flex-col bg-background-dark relative z-0">
        <!-- Sticky Filter Header -->
        <div class="sticky top-0 z-10 bg-background-dark/95 backdrop-blur-sm border-b border-border-dark px-6 pt-5 pb-0 shadow-sm">
            <!-- Search Bar -->
            <div class="mb-4">
                <label class="flex flex-col w-full h-14">
                    <div class="flex w-full flex-1 items-stretch rounded-xl h-full shadow-sm">
                        <div class="text-[#b89d9f] flex border border-r-0 border-border-dark bg-surface-dark items-center justify-center pl-5 rounded-l-xl">
                            <span class="material-symbols-outlined text-[28px]">search</span>
                        </div>
                        <input 
                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-r-xl text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 focus:border-primary border border-l-0 border-border-dark bg-surface-dark h-full placeholder:text-[#b89d9f] px-4 text-lg font-medium leading-normal transition-all" 
                            placeholder="Cari produk atau scan barcode (F2)" 
                            wire:model.live.debounce.300ms="search"
                            id="search-input"
                        />
                        <!-- <div class="absolute right-3 top-3 h-8 w-10 flex items-center justify-center text-[#b89d9f] bg-[#382929] rounded border border-[#533c3d]">
                            <span class="material-symbols-outlined text-xl">barcode_scanner</span>
                        </div> -->
                    </div>
                </label>
            </div>
            <!-- Tabs -->
            <div class="flex overflow-x-auto no-scrollbar gap-8 pb-0">
                <button 
                    class="flex flex-col items-center justify-center border-b-[3px] {{ !$selectedCategory ? 'border-b-primary text-white' : 'border-b-transparent text-[#b89d9f] hover:text-white hover:border-b-white/20' }} pb-3 px-1 min-w-[60px] transition-all" 
                    wire:click="selectCategory(null)"
                >
                    <p class="text-sm font-bold leading-normal tracking-[0.015em]">Semua</p>
                </button>
                @foreach($categories as $category)
                <button 
                    class="flex flex-col items-center justify-center border-b-[3px] {{ $selectedCategory === $category->id ? 'border-b-primary text-white' : 'border-b-transparent text-[#b89d9f] hover:text-white hover:border-b-white/20' }} pb-3 px-1 min-w-[60px] transition-all" 
                    wire:click="selectCategory({{ $category->id }})"
                >
                    <p class="text-sm font-bold leading-normal tracking-[0.015em]">{{ $category->name }}</p>
                </button>
                @endforeach
            </div>
        </div>
        <!-- Scrollable Grid Area -->
        <div class="flex-1 overflow-y-auto p-6 scroll-smooth">
            <div class="grid grid-cols-[repeat(auto-fill,minmax(180px,1fr))] gap-4">
                @forelse($products as $product)
                <!-- Product Card -->
                <div 
                    class="group cursor-pointer flex flex-col gap-3 p-3 rounded-xl bg-surface-dark border border-transparent hover:border-primary/50 hover:bg-[#2a1f1f] transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-primary/5 {{ $product->stock <= 0 ? 'opacity-50 grayscale' : '' }}"
                    wire:click="addToCart({{ $product->id }})"
                >
                    <div class="relative w-full aspect-square bg-[#382929] rounded-lg overflow-hidden flex items-center justify-center">
                        @if($product->image)
                            <div class="absolute inset-0 bg-center bg-cover bg-no-repeat" style='background-image: url("{{ Storage::url($product->image) }}");'></div>
                        @else
                           <x-heroicon-o-archive-box class="w-12 h-12 text-[#b89d9f] opacity-50" />
                        @endif
                        <div class="absolute top-2 right-2 bg-primary text-white text-xs font-bold px-2 py-1 rounded shadow-sm">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </div>
                        @if($product->stock <= 0)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">HABIS</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-white text-base font-bold leading-tight mb-1 group-hover:text-primary transition-colors line-clamp-2">{{ $product->name }}</p>
                        <p class="text-[#b89d9f] text-xs font-normal leading-normal">Stok: {{ $product->stock }}</p>
                    </div>
                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center text-[#b89d9f] py-10">
                    <span class="material-symbols-outlined text-6xl mb-4 opacity-50">search_off</span>
                    <p>Tidak ada produk ditemukan</p>
                </div>
                @endforelse
                
                @if($products->count() >= $perPage)
                <div class="col-span-full pt-4 flex justify-center">
                    <button 
                        wire:click="loadMore" 
                        class="bg-[#382929] hover:bg-[#4a3636] border border-[#533c3d] text-white font-bold py-3 px-8 rounded-xl flex items-center gap-2 transition-all shadow-sm"
                    >
                        <span wire:loading.remove wire:target="loadMore" class="flex items-center gap-2"><x-heroicon-o-archive-box-arrow-down class="w-5 h-5" /> Muat Lebih Banyak</span>
                        <span wire:loading wire:target="loadMore" class="animate-spin material-symbols-outlined text-sm">progress_activity</span>
                        <span wire:loading wire:target="loadMore">Memuat...</span>
                    </button>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Mobile Cart Toggle FAB -->
         <button @click="mobileCartOpen = !mobileCartOpen" class="lg:hidden absolute bottom-6 right-6 size-14 bg-primary text-white rounded-full shadow-lg flex items-center justify-center z-30">
            <span class="material-symbols-outlined">shopping_cart</span>
            @if(count($cart) > 0)
                <span class="absolute -top-1 -right-1 bg-white text-primary text-xs font-bold size-5 flex items-center justify-center rounded-full border border-primary">{{ count($cart) }}</span>
            @endif
        </button>
    </section>
    
    <!-- Right Panel: Cart / Ticket (30%) -->
    <aside 
        class="w-full lg:w-[30%] flex flex-col bg-surface-dark border-l border-border-dark shadow-xl z-20 absolute lg:relative inset-y-0 right-0 transition-transform duration-300 lg:translate-x-0"
        :class="mobileCartOpen ? 'translate-x-0' : 'translate-x-full'"
    >
        <!-- Cart Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-border-dark bg-surface-dark">
            <div>
                <h3 class="text-white text-xl font-bold">Keranjang</h3>
                <p class="text-[#b89d9f] text-sm">#{{ rand(1000,9999) }} • Walk-in Customer</p>
            </div>
            <button class="lg:hidden text-[#b89d9f]" @click="mobileCartOpen = false">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <!-- Cart Items List (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse($cart as $index => $item)
            <!-- Cart Item -->
            <div class="flex items-center gap-4 bg-[#1e1515] p-3 rounded-lg border border-transparent hover:border-border-dark transition-colors group">
                <div class="bg-[#382929] rounded-md shrink-0 size-14 overflow-hidden relative flex items-center justify-center">
                    @if($item['image'])
                        <div class="absolute inset-0 bg-center bg-cover bg-no-repeat" style='background-image: url("{{ Storage::url($item['image']) }}");'></div>
                    @else
                        <x-heroicon-o-archive-box class="w-6 h-6 text-[#b89d9f] opacity-50" />
                    @endif
                </div>
                <div class="flex flex-col flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <p class="text-white text-sm font-medium leading-tight line-clamp-1">{{ $item['name'] }}</p>
                        <p class="text-white text-sm font-bold">Rp {{ number_format($item['total'], 0, ',', '.') }}</p>
                    </div>
                    <p class="text-[#b89d9f] text-xs font-normal mt-0.5">Rp {{ number_format($item['price'], 0, ',', '.') }} / unit</p>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-2">
                            <button class="size-6 flex items-center justify-center rounded bg-[#382929] text-white hover:bg-primary hover:text-white transition-colors" wire:click="decrementQuantity({{ $index }})">
                                <span class="material-symbols-outlined text-sm">remove</span>
                            </button>
                            <span class="text-white text-sm font-medium w-6 text-center">{{ $item['quantity'] }}</span>
                            <button class="size-6 flex items-center justify-center rounded bg-[#382929] text-white hover:bg-primary hover:text-white transition-colors" wire:click="incrementQuantity({{ $index }})">
                                <span class="material-symbols-outlined text-sm">add</span>
                            </button>
                        </div>
                        <button class="text-[#b89d9f] hover:text-red-500 transition-opacity" wire:click="removeFromCart({{ $index }})">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center h-full text-[#b89d9f] opacity-50">
                <span class="material-symbols-outlined text-6xl mb-2">shopping_basket</span>
                <p>Keranjang Kosong</p>
            </div>
            @endforelse
        </div>
        <!-- Cart Footer / Totals -->
        <div class="bg-[#1e1515] border-t border-border-dark p-6 space-y-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
            <div class="space-y-2">
                <div class="flex justify-between text-[#b89d9f] text-sm">
                    <span>Subtotal</span>
                    <span class="text-white font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                @if($tax > 0)
                <div class="flex justify-between text-[#b89d9f] text-sm">
                    <span>Pajak ({{ $tax }}%)</span>
                    <span class="text-white font-medium">Rp {{ number_format(($subtotal - $discount) * ($tax / 100), 0, ',', '.') }}</span>
                </div>
                @endif
                @if($discount > 0)
                <div class="flex justify-between text-[#b89d9f] text-sm">
                    <span>Diskon</span>
                    <span class="text-green-500 font-medium">-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
            <div class="flex justify-between items-end pt-2 border-t border-[#382929] border-dashed">
                <span class="text-white font-medium text-lg">Total</span>
                <span class="text-white font-bold text-3xl">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            @if(!empty($cart))
                <div class="grid grid-cols-[1fr_2fr] gap-3 pt-2">
                    <button class="flex items-center justify-center rounded-xl bg-[#382929] text-white hover:bg-[#4a3636] transition-colors py-4 text-base font-bold" wire:click="clearCart" title="Alt+Delete">
                        <span class="material-symbols-outlined mr-2">delete</span>
                        Clear (Alt+Del)
                    </button>
                    <button class="flex items-center justify-center rounded-xl bg-primary text-white hover:bg-red-600 transition-colors py-4 text-lg font-bold shadow-[0_0_15px_rgba(234,42,51,0.4)] hover:shadow-[0_0_20px_rgba(234,42,51,0.6)]" wire:click="openCheckout">
                        Bayar
                        <span class="material-symbols-outlined ml-2">arrow_forward</span>
                    </button>
                    
                    <button class="col-span-1 flex items-center justify-center rounded-xl bg-[#382929] text-white hover:bg-[#4a3636] transition-colors py-2 text-sm font-bold" wire:click="openDiscountModal">
                         <x-heroicon-o-tag class="w-5 h-5 mr-1 text-yellow-500" /> Diskon (F4)
                    </button>
                    <button class="col-span-1 flex items-center justify-center rounded-xl {{ $tax > 0 ? 'bg-[#382929] text-white hover:bg-[#4a3636]' : 'bg-surface-dark border border-dashed border-[#b89d9f] text-[#b89d9f] hover:text-white' }} transition-all py-2 text-sm font-bold" wire:click="toggleTax">
                         @if($tax > 0)
                            <span class="flex items-center gap-1"><x-heroicon-o-check-circle class="w-4 h-4 text-green-500" /> Pajak On (F10)</span>
                         @else
                            <span class="flex items-center gap-1"><x-heroicon-o-x-circle class="w-4 h-4 text-red-500" /> Pajak Off (F10)</span>
                         @endif
                    </button>
                </div>
            @endif
        </div>
    </aside>
</main>

<!-- Unified Modal Backdrop & Style -->
<style>
    .custom-modal-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(4px); z-index: 50; display: flex; align-items: center; justify-content: center;
    }
    .custom-modal {
        background: #241a1a; border: 1px solid #382929; border-radius: 1rem; width: 100%; max-width: 500px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
</style>

<!-- Checkout Modal -->
@if($showCheckoutModal)
    <div class="custom-modal-backdrop" wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()" @keydown.window.f1.prevent="$wire.setExactAmount()">
        <div class="custom-modal" wire:click.stop>
            <div class="p-5 border-b border-border-dark bg-background-dark/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-2"><x-heroicon-o-credit-card class="w-6 h-6 text-blue-400" /> Pembayaran</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="text-center p-4 bg-primary/10 rounded-xl border border-primary/20">
                    <p class="text-[#b89d9f] text-sm mb-1">Total Tagihan</p>
                    <p class="text-4xl font-bold text-white">Rp {{ number_format($total, 0, ',', '.') }}</p>
                </div>

                <div class="flex gap-3">
                    @foreach(['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer'] as $key => $label)
                    <button 
                        class="flex-1 p-3 rounded-lg border-2 {{ $paymentMethod === $key ? 'border-primary bg-primary/10 text-white' : 'border-border-dark bg-surface-dark text-[#b89d9f] hover:border-gray-600' }} transition-all font-bold"
                        wire:click="setPaymentMethod('{{ $key }}')"
                    >
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                @if($paymentMethod === 'cash')
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[#b89d9f] text-xs uppercase font-bold tracking-wider">Uang Diterima</label>
                            <input 
                                type="number" 
                                wire:model.live="amountPaid" 
                                class="w-full bg-background-dark border border-border-dark rounded-lg p-3 text-white text-xl font-bold focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="0"

                                x-init="$nextTick(() => $el.focus())"
                                wire:keydown.enter="processPayment"
                            >
                        </div>
                         
                        <div class="grid grid-cols-3 gap-2">
                             @php $suggestions = [50000, 100000, 20000]; @endphp
                             @foreach($suggestions as $amt)
                                <button class="bg-[#382929] text-white py-2 rounded text-sm hover:bg-[#4a3636]" wire:click="setQuickAmount({{ $amt }})">
                                    {{ number_format($amt/1000) }}k
                                </button>
                             @endforeach
                             <button class="bg-[#382929] text-white py-2 rounded text-sm hover:bg-[#4a3636] col-span-3" wire:click="setExactAmount">Uang Pas (F1)</button>
                        </div>

                        @if($amountPaid >= $total)
                        <div class="flex justify-between items-center bg-green-900/20 p-3 rounded-lg border border-green-900/50">
                            <span class="text-green-500 font-bold">Kembalian</span>
                            <span class="text-white text-xl font-bold">Rp {{ number_format($change, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="p-5 border-t border-border-dark flex gap-3 bg-background-dark/50">
                <button class="flex-1 py-3 px-4 rounded-xl border border-border-dark text-white hover:bg-[#382929] transition-colors font-bold" wire:click="closeModal">Batal</button>
                <button 
                    class="flex-[2] py-3 px-4 rounded-xl bg-primary text-white hover:bg-red-600 transition-colors font-bold disabled:opacity-50 disabled:cursor-not-allowed shadow-[0_4px_14px_rgba(234,42,51,0.4)]" 
                    wire:click="processPayment"
                    @if($paymentMethod === 'cash' && $amountPaid < $total) disabled @endif
                >
                    Proses Pembayaran (Enter)
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Discount Modal -->
@if($showDiscountModal)
    <div class="custom-modal-backdrop" wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()" @keydown.window.f1.prevent="$wire.set('discountType', 0)" @keydown.window.f2.prevent="$wire.set('discountType', 1)">
        <div class="custom-modal" wire:click.stop style="max-width: 400px;">
            <div class="p-5 border-b border-border-dark bg-background-dark/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-2"><x-heroicon-o-tag class="w-6 h-6 text-yellow-500" /> Tambah Diskon</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="flex bg-[#382929] p-1 rounded-lg">
                    <button class="flex-1 py-2 rounded-md text-sm font-bold transition-all {{ $discountType == 0 ? 'bg-primary text-white shadow' : 'text-[#b89d9f]' }}" wire:click="$set('discountType', 0)">Nominal (Rp) (F1)</button>
                    <button class="flex-1 py-2 rounded-md text-sm font-bold transition-all {{ $discountType == 1 ? 'bg-primary text-white shadow' : 'text-[#b89d9f]' }}" wire:click="$set('discountType', 1)">Persen (%) (F2)</button>
                </div>
                
                <input 
                    type="number" 
                    wire:model="discountValue"
                    class="w-full bg-background-dark border border-border-dark rounded-lg p-3 text-white text-xl font-bold focus:ring-2 focus:ring-primary focus:border-transparent text-center"
                    placeholder="0"
                    x-init="$nextTick(() => $el.focus())"
                    wire:keydown.enter="applyDiscount"
                >
            </div>
            <div class="p-5 border-t border-border-dark flex gap-3 bg-background-dark/50">
                <button class="flex-1 py-3 px-4 rounded-xl border border-border-dark text-white hover:bg-[#382929] transition-colors font-bold" wire:click="closeModal">Batal</button>
                <button class="flex-1 py-3 px-4 rounded-xl bg-primary text-white hover:bg-red-600 transition-colors font-bold" wire:click="applyDiscount">Terapkan (Enter)</button>
            </div>
        </div>
    </div>
@endif

<!-- Profile Modal -->
@if($showProfileModal)
    <div class="custom-modal-backdrop" wire:click.self="closeModal">
        <div class="custom-modal" wire:click.stop>
             <div class="p-5 border-b border-border-dark bg-background-dark/50">
                <h3 class="text-xl font-bold text-white flex items-center gap-2"><x-heroicon-o-user class="w-6 h-6 text-primary" /> Pengaturan Profil</h3>
            </div>
             <div class="p-6 space-y-4">
                <div class="space-y-2">
                    <label class="text-[#b89d9f] text-sm font-bold">Nama Lengkap</label>
                    <input type="text" wire:model="profileName" class="w-full bg-background-dark border border-border-dark rounded-lg p-2.5 text-white focus:border-primary focus:ring-1 focus:ring-primary">
                    @error('profileName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[#b89d9f] text-sm font-bold">Email</label>
                    <input type="email" wire:model="profileEmail" class="w-full bg-background-dark border border-border-dark rounded-lg p-2.5 text-white focus:border-primary focus:ring-1 focus:ring-primary">
                    @error('profileEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="pt-4 mt-4 border-t border-border-dark">
                    <p class="text-[#b89d9f] text-xs mb-3 italic">Kosongkan jika tidak ingin mengubah password</p>
                    <div class="space-y-3">
                         <div class="space-y-2">
                            <label class="text-[#b89d9f] text-sm font-bold">Password Baru</label>
                            <input type="password" wire:model="profilePassword" class="w-full bg-background-dark border border-border-dark rounded-lg p-2.5 text-white focus:border-primary focus:ring-1 focus:ring-primary">
                            @error('profilePassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                         <div class="space-y-2">
                            <label class="text-[#b89d9f] text-sm font-bold">Konfirmasi Password</label>
                            <input type="password" wire:model="profilePasswordConfirmation" class="w-full bg-background-dark border border-border-dark rounded-lg p-2.5 text-white focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>
                    </div>
                </div>
             </div>
             <div class="p-5 border-t border-border-dark flex gap-3 bg-background-dark/50">
                <button class="flex-1 py-3 px-4 rounded-xl border border-border-dark text-white hover:bg-[#382929] transition-colors font-bold" wire:click="closeModal">Batal</button>
                <button class="flex-1 py-3 px-4 rounded-xl bg-primary text-white hover:bg-red-600 transition-colors font-bold" wire:click="updateProfile">Simpan</button>
            </div>
        </div>
    </div>
@endif

<!-- Notifications -->
<div x-data="{ notifications: [] }" 
     @notify.window="notifications.push($event.detail); setTimeout(() => { notifications.shift() }, 3000)"
     class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none">
    <template x-for="note in notifications">
        <div x-show="true" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-10"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-10"
             class="pointer-events-auto bg-surface-dark border border-border-dark text-white p-4 rounded-xl shadow-2xl flex items-center gap-3 min-w-[300px]"
             :class="{ 'border-l-4 border-l-primary': note.type === 'success', 'border-l-red-500': note.type === 'error' }">
             
             <span class="material-symbols-outlined text-green-500" x-show="note.type === 'success'">check_circle</span>
             <span class="material-symbols-outlined text-red-500" x-show="note.type === 'error'">error</span>
             
             <div>
                <p class="font-bold text-sm" x-text="note.type === 'success' ? 'Sukses' : 'Error'"></p>
                <p class="text-xs text-[#b89d9f]" x-text="note.message"></p>
             </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('printReceipt', (data) => {
            let iframe = document.getElementById('receipt-frame');
            if (!iframe) {
                iframe = document.createElement('iframe');
                iframe.id = 'receipt-frame';
                iframe.style.position = 'absolute';
                iframe.style.width = '0px';
                iframe.style.height = '0px';
                iframe.style.border = 'none';
                document.body.appendChild(iframe);
            }
            iframe.src = '/pos/receipt/' + data.orderId;
        });
        
        // Keyboard Shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F2') {
                e.preventDefault();
                document.getElementById('search-input').focus();
            }
            if (e.key === 'F9') {
                e.preventDefault();
                @this.call('openCheckout');
            }
            if (e.key === 'F4') {
                e.preventDefault();
                @this.call('openDiscountModal');
            }
            if (e.key === 'F10') {
                e.preventDefault();
                @this.call('toggleTax');
            }
            if (e.altKey && e.key === 'Delete') {
                e.preventDefault();
                @this.call('clearCart');
                // if(confirm('Yakin ingin mengosongkan keranjang?')) {
                    
                // }
            }
        });
    });
</script>
</div>
