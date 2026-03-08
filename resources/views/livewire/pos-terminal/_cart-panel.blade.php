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
                            <span class="text-sm font-medium text-slate-800 truncate">{{ $selectedCustomer->name }}</span>
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
                <!-- <button wire:click="openCustomerModal"
                    class="w-10 h-10 rounded-lg bg-secondary text-primary hover:bg-primary hover:text-white flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined">{{ $selectedCustomer ? 'swap_horiz' : 'add' }}</span>
                </button> -->
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
                        <h4 class="font-semibold text-slate-800 group-hover:text-primary transition-colors line-clamp-1">
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