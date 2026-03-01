<div class="h-full flex flex-row" x-data="{ mobileCartOpen: false }">
    {{-- Sidebar Navigation --}}
    <nav class="w-20 bg-white border-r border-slate-200 flex-col items-center py-6 shrink-0 z-20 hidden lg:flex">
        <div class="mb-8">
            <div
                class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined text-3xl">storefront</span>
            </div>
        </div>

        <div class="flex-1 flex flex-col gap-3 w-full px-2 overflow-y-auto no-scrollbar">
            <button wire:click="selectCategory(null)"
                class="w-full aspect-square rounded-xl flex items-center justify-center transition-colors group relative {{ !$selectedCategory ? 'bg-secondary text-primary-hover' : 'text-slate-400 hover:bg-secondary/50 hover:text-primary' }}">
                <span class="material-symbols-outlined text-2xl">grid_view</span>
                <span
                    class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 pointer-events-none">Semua</span>
            </button>
            {{-- @foreach($categories as $category)
            <button wire:click="selectCategory({{ $category->id }})"
                class="w-full aspect-square rounded-xl flex items-center justify-center transition-colors group relative {{ $selectedCategory === $category->id ? 'bg-secondary text-primary-hover' : 'text-slate-400 hover:bg-secondary/50 hover:text-primary' }}">
                <span class="material-symbols-outlined text-2xl">inventory_2</span>
                <span
                    class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50 pointer-events-none">{{
                    $category->name }}</span>
            </button>
            @endforeach --}}
        </div>

        <div class="mt-auto flex flex-col items-center gap-4 pt-4" x-data="{ userMenu: false }">
            <button
                class="w-10 h-10 rounded-full hover:bg-secondary/50 text-slate-400 hover:text-primary flex items-center justify-center transition-colors"
                wire:click="openProfileModal()">
                <span class="material-symbols-outlined">settings</span>
            </button>
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm cursor-pointer ring-2 ring-white shadow-sm"
                    @click="userMenu = !userMenu">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div x-show="userMenu" @click.outside="userMenu = false" x-cloak
                    class="absolute left-full bottom-0 ml-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('filament.admin.pages.dashboard') }}"
                        class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-secondary hover:text-primary transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">dashboard</span>
                        Admin Panel
                    </a>
                    <button wire:click="openProfileModal(); userMenu = false"
                        class="w-full text-left px-4 py-2.5 text-sm text-slate-600 hover:bg-secondary hover:text-primary transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">person</span>
                        Profil Saya
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2 border-t border-slate-100">
                            <span class="material-symbols-outlined text-lg">logout</span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-background-light">
        <header class="pos-header h-16 sm:h-20 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 z-10">
            <div class="flex flex-col min-w-0">
                <h1 class="pos-store-name text-lg sm:text-2xl font-bold text-slate-900 truncate">{{ $storeName }}</h1>
                <p class="pos-store-date text-xs sm:text-sm text-slate-500">{{ now()->translatedFormat('l, d M Y') }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden xl:flex gap-4 text-xs text-slate-500 font-medium items-center">
                    <span class="flex items-center gap-1"><kbd
                            class="bg-white px-1.5 py-0.5 rounded text-slate-700 border border-slate-200 text-[11px] font-bold">F2</kbd>
                        Cari</span>
                    <span class="flex items-center gap-1"><kbd
                            class="bg-white px-1.5 py-0.5 rounded text-slate-700 border border-slate-200 text-[11px] font-bold">F9</kbd>
                        Bayar</span>
                    <span class="flex items-center gap-1"><kbd
                            class="bg-white px-1.5 py-0.5 rounded text-slate-700 border border-slate-200 text-[11px] font-bold">F10</kbd>
                        Pajak</span>
                </div>
                <div class="relative w-48 sm:w-72 lg:w-96 group">
                    <input
                        class="w-full pl-10 sm:pl-12 pr-20 sm:pr-24 py-2.5 sm:py-3 bg-white border border-slate-200 rounded-xl placeholder:text-slate-300 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:shadow-lg focus:shadow-primary/5 text-slate-800 outline-none text-sm sm:text-base transition-all duration-200"
                        placeholder=" Cari / Scan produk..." wire:model.live.debounce.300ms="search"
                        wire:keydown.enter="handleBarcodeScan($event.target.value); $event.target.value = '';"
                        id="search-input" />
                    <div class="absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-slate-300 text-lg sm:text-xl">barcode_scanner</span>
                        <kbd
                            class="hidden sm:inline-flex bg-slate-100 text-slate-400 text-[10px] font-bold px-1.5 py-0.5 rounded border border-slate-200">F2</kbd>
                    </div>
                </div>
                {{-- Mobile user avatar --}}
                <div class="lg:hidden relative" x-data="{ mobileUser: false }">
                    <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm cursor-pointer"
                        @click="mobileUser = !mobileUser">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div x-show="mobileUser" @click.outside="mobileUser = false" x-cloak
                        class="absolute right-0 top-full mt-2 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('filament.admin.pages.dashboard') }}"
                            class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-secondary hover:text-primary transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">dashboard</span> Admin Panel
                        </a>
                        <button wire:click="openProfileModal()"
                            class="w-full text-left px-4 py-2.5 text-sm text-slate-600 hover:bg-secondary hover:text-primary transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">person</span> Profil Saya
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2 border-t border-slate-100">
                                <span class="material-symbols-outlined text-lg">logout</span> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Category Pills (swipeable, pinned below header) --}}
        <div
            class="pos-category-bar flex gap-2 px-4 sm:px-6 lg:px-8 py-2 sm:py-3 overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth shrink-0 bg-background-light">
            <button wire:click="selectCategory(null)"
                class="snap-start px-3 sm:px-5 py-2 sm:py-2.5 rounded-lg text-sm sm:text-base font-medium whitespace-nowrap transition-colors {{ !$selectedCategory ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-white text-slate-600 hover:bg-secondary hover:text-primary-hover border border-slate-200' }}">
                Semua
            </button>
            @foreach($categories as $category)
                <button wire:click="selectCategory({{ $category->id }})"
                    class="snap-start px-3 sm:px-5 py-2 sm:py-2.5 rounded-lg text-sm sm:text-base font-medium whitespace-nowrap transition-colors {{ $selectedCategory === $category->id ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-white text-slate-600 hover:bg-secondary hover:text-primary-hover border border-slate-200' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <div class="pos-content-area flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 pb-6 sm:pb-8">
            {{-- Product Grid --}}
            <div class="pos-product-grid grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
                @forelse($products as $product)
                    <div class="pos-product-card bg-white p-3 sm:p-4 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-primary/20 transition-all cursor-pointer group flex flex-col h-full {{ $product->available_stock <= 0 ? 'opacity-60 grayscale' : '' }}"
                        wire:click="addToCart({{ $product->id }})">
                        <div
                            class="pos-product-image relative aspect-[4/3] rounded-lg overflow-hidden mb-2 sm:mb-4 bg-slate-100">
                            <div class="w-full h-full bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-300"
                                style='background-image: url("{{ $product->image ? Storage::url($product->image) : asset('images/placeholder.png') }}");'>
                            </div>
                            @if($product->available_stock <= 0)
                                <div class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center">
                                    <span
                                        class="bg-red-500 text-white text-[10px] font-black px-3 py-1.5 rounded-full shadow-lg">HABIS</span>
                                </div>
                            @endif
                        </div>
                        <div class="pos-product-details mt-auto">
                            <h3
                                class="pos-product-name font-bold text-sm sm:text-base text-slate-800 mb-1 line-clamp-2 min-h-[36px] sm:min-h-[44px]">
                                {{ $product->name }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-400 mb-2 sm:mb-3">Stok: {{ $product->available_stock }}
                            </p>
                            <div
                                class="pos-product-btn w-full py-1.5 sm:py-2 bg-secondary text-primary-hover group-hover:bg-primary group-hover:text-white font-medium rounded-lg transition-colors text-xs sm:text-sm text-center">
                                Tambah ke Keranjang
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center text-slate-400 py-16">
                        <span class="material-symbols-outlined text-6xl mb-4 opacity-50">search_off</span>
                        <p class="text-lg font-medium">Tidak ada produk ditemukan</p>
                    </div>
                @endforelse

                @if($products->count() >= $perPage)
                    <div class="col-span-full pt-4 flex justify-center">
                        <button wire:click="loadMore"
                            class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold py-3 px-8 rounded-xl flex items-center gap-2 transition-all shadow-sm">
                            <span wire:loading.remove wire:target="loadMore" class="flex items-center gap-2">
                                <x-heroicon-o-archive-box-arrow-down class="w-5 h-5" /> Muat Lebih Banyak
                            </span>
                            <span wire:loading wire:target="loadMore"
                                class="animate-spin material-symbols-outlined text-sm">progress_activity</span>
                            <span wire:loading wire:target="loadMore">Memuat...</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Mobile Cart FAB --}}
        <button @click="mobileCartOpen = !mobileCartOpen"
            class="pos-cart-fab lg:hidden fixed bottom-6 right-6 size-14 bg-primary text-white rounded-full shadow-lg shadow-primary/30 flex items-center justify-center z-30">
            <span class="material-symbols-outlined">shopping_cart</span>
            @if(count($cart) > 0)
                <span
                    class="absolute -top-1 -right-1 bg-white text-primary text-xs font-bold size-5 flex items-center justify-center rounded-full border-2 border-primary">{{ count($cart) }}</span>
            @endif
        </button>
    </main>

    {{-- Cart Panel --}}
    <aside
        class="pos-cart-panel w-full md:w-[400px] xl:w-[450px] bg-white border-l border-slate-200 flex flex-col shrink-0 z-20 shadow-xl fixed lg:static inset-y-0 right-0 transition-transform duration-300 lg:translate-x-0"
        :class="mobileCartOpen ? 'translate-x-0' : 'translate-x-full'">
        {{-- Cart Header --}}
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Keranjang</h2>
                    <p class="text-sm text-slate-400">#{{ rand(1000, 9999) }} •
                        {{ $selectedCustomer ? $selectedCustomer->name : 'Walk-in Customer' }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if(!empty($cart))
                        <button wire:click="clearCart"
                            class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                            title="Alt+Delete">
                            <span class="material-symbols-outlined">delete_sweep</span>
                        </button>
                    @endif
                    <button class="lg:hidden p-2 text-slate-400" @click="mobileCartOpen = false">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>
            {{-- Customer Selection --}}
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-slate-400">person</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        @if($selectedCustomer)
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-sm font-medium text-slate-800 truncate">{{ $selectedCustomer->name }}</span>
                                <button wire:click="removeCustomer" class="text-red-400 hover:text-red-500 shrink-0">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                            </div>
                        @else
                            <button wire:click="openCustomerModal"
                                class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm text-left text-slate-400 hover:border-primary transition-all truncate">
                                Pilih Pelanggan...
                            </button>
                        @endif
                    </div>
                    <button wire:click="openCustomerModal"
                        class="w-10 h-10 rounded-lg bg-secondary text-primary hover:bg-primary hover:text-white flex items-center justify-center transition-colors shrink-0">
                        <span class="material-symbols-outlined">{{ $selectedCustomer ? 'swap_horiz' : 'add' }}</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-4">
            @forelse($cart as $index => $item)
                <div class="flex gap-4 items-start group">
                    <div class="w-16 h-16 rounded-lg bg-slate-100 bg-cover bg-center shrink-0 overflow-hidden flex items-center justify-center"
                        @if($item['image']) style='background-image: url("{{ Storage::url($item['image']) }}");' @endif>
                        @if(!$item['image'])
                            <x-heroicon-o-archive-box class="w-6 h-6 text-slate-300" />
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-1">
                            <h4
                                class="font-semibold text-slate-800 group-hover:text-primary transition-colors line-clamp-1">
                                {{ $item['name'] }}
                            </h4>
                            <span class="font-bold text-slate-800 shrink-0 ml-2">Rp
                                {{ number_format($item['total'], 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mb-3">Rp {{ number_format($item['price'], 0, ',', '.') }} / unit
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <button wire:click="decrementQuantity({{ $index }})"
                                    class="w-7 h-7 rounded bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors">
                                    <span class="material-symbols-outlined text-sm">remove</span>
                                </button>
                                <span class="font-medium text-slate-900 w-4 text-center">{{ $item['quantity'] }}</span>
                                <button wire:click="incrementQuantity({{ $index }})"
                                    class="w-7 h-7 rounded bg-primary text-white flex items-center justify-center hover:bg-primary-hover transition-colors shadow-sm">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-red-500 transition-colors"
                                wire:click="removeFromCart({{ $index }})">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex-1 flex flex-col items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-6xl mb-2 opacity-30">shopping_basket</span>
                    <p class="font-medium">Keranjang Kosong</p>
                </div>
            @endforelse
        </div>

        {{-- Cart Footer --}}
        <div class="bg-slate-50 p-6 border-t border-slate-200">
            @if(!empty($cart))
                <div class="flex gap-3 mb-6">
                    <button wire:click="openDiscountModal"
                        class="flex-1 py-2 px-3 border border-slate-200 bg-white text-slate-600 text-sm font-medium rounded-lg hover:bg-secondary hover:text-primary hover:border-primary/30 flex items-center justify-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">percent</span> Diskon (F4)
                    </button>
                    <button wire:click="toggleTax"
                        class="flex-1 py-2 px-3 border border-slate-200 bg-white text-slate-600 text-sm font-medium rounded-lg hover:bg-secondary hover:text-primary hover:border-primary/30 flex items-center justify-center gap-2 transition-colors">
                        @if($tax > 0)
                            <span class="material-symbols-outlined text-[18px] text-green-500">check_circle</span> Pajak On
                            (F10)
                        @else
                            <span class="material-symbols-outlined text-[18px] text-red-400">cancel</span> Pajak Off (F10)
                        @endif
                    </button>
                </div>
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($tax > 0)
                        <div class="flex justify-between text-sm text-slate-500">
                            <span>Pajak ({{ $tax }}%)</span>
                            <span>Rp {{ number_format(($subtotal - $discount) * ($tax / 100), 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($discount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon</span>
                            <span>-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-xl font-bold text-slate-900 pt-3 border-t border-slate-200 mt-2">
                        <span>Total</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
                <button wire:click="openCheckout"
                    class="w-full bg-primary hover:bg-primary-hover text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-primary/25 flex items-center justify-center gap-2 transition-all active:scale-[0.98]">
                    <span>Bayar (F9)</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            @else
                <button disabled
                    class="w-full bg-slate-200 text-slate-400 py-4 rounded-xl font-bold text-lg cursor-not-allowed flex items-center justify-center gap-2">
                    <span>Bayar (F9)</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            @endif
        </div>
    </aside>

    {{-- ==================== MODALS ==================== --}}

    {{-- Checkout Modal --}}
    @if($showCheckoutModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()"
            @keydown.window.f1.prevent="$wire.setExactAmount()">
            <div class="bg-white border border-slate-200 rounded-xl w-full max-w-md overflow-hidden shadow-2xl"
                wire:click.stop>
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

    {{-- Discount Modal --}}
    @if($showDiscountModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()"
            @keydown.window.f1.prevent="$wire.set('discountType', 0)"
            @keydown.window.f2.prevent="$wire.set('discountType', 1)">
            <div class="bg-white border border-slate-200 rounded-xl w-full max-w-sm overflow-hidden shadow-2xl"
                wire:click.stop>
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

    {{-- Profile Modal --}}
    @if($showProfileModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            wire:click.self="closeModal">
            <div class="bg-white border border-slate-200 rounded-xl w-full max-w-md overflow-hidden shadow-2xl"
                wire:click.stop>
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <x-heroicon-o-user class="w-6 h-6 text-primary" /> Pengaturan Profil
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        <label class="text-slate-600 text-sm font-bold">Nama Lengkap</label>
                        <input type="text" wire:model="profileName"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        @error('profileName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-slate-600 text-sm font-bold">Email</label>
                        <input type="email" wire:model="profileEmail"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                        @error('profileEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-200">
                        <p class="text-slate-400 text-xs mb-3 italic">Kosongkan jika tidak ingin mengubah password</p>
                        <div class="space-y-3">
                            <div class="space-y-2">
                                <label class="text-slate-600 text-sm font-bold">Password Baru</label>
                                <input type="password" wire:model="profilePassword"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                                @error('profilePassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-slate-600 text-sm font-bold">Konfirmasi Password</label>
                                <input type="password" wire:model="profilePasswordConfirmation"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:border-primary focus:ring-1 focus:ring-primary outline-none">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-5 border-t border-slate-100 flex gap-3 bg-slate-50">
                    <button
                        class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors font-bold"
                        wire:click="closeModal">Batal</button>
                    <button
                        class="flex-1 py-3 px-4 rounded-xl bg-primary text-white hover:bg-primary-hover transition-colors font-bold"
                        wire:click="updateProfile">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Customer Modal --}}
    @if($showCustomerModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            wire:click.self="closeModal" x-data @keydown.window.escape="$wire.closeModal()">
            <div class="bg-white border border-slate-200 rounded-xl w-full max-w-md overflow-hidden shadow-2xl"
                wire:click.stop>
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <x-heroicon-o-users class="w-6 h-6 text-primary" /> Pilih Pelanggan
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <input type="text" wire:model.live.debounce.300ms="customerSearch"
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

    {{-- Scripts --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('clear-search-input', () => {
                const input = document.getElementById('search-input');
                if (input) input.value = '';
            });

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

            document.addEventListener('keydown', (e) => {
                if (e.key === 'F2') { e.preventDefault(); document.getElementById('search-input').focus(); }
                if (e.key === 'F9') { e.preventDefault(); @this.call('openCheckout'); }
                if (e.key === 'F4') { e.preventDefault(); @this.call('openDiscountModal'); }
                if (e.key === 'F10') { e.preventDefault(); @this.call('toggleTax'); }
                if (e.altKey && e.key === 'Delete') { e.preventDefault(); @this.call('clearCart'); }
            });
        });
    </script>
</div>