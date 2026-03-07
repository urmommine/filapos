{{-- Product Grid --}}
<div class="pos-product-grid grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
    @forelse($products as $product)
        <div
            class="pos-product-card relative bg-white p-3 sm:p-4 rounded-xl shadow-sm border border-slate-100 hover:shadow-md hover:border-primary/20 transition-all cursor-pointer group flex flex-col h-full {{ $product->available_stock <= 0 ? 'opacity-60 grayscale' : '' }}"
            wire:click="addToCart({{ $product->id }})">

            {{-- Price Badge --}}
            <span
                class="absolute top-2 right-2 bg-primary text-white text-xs sm:text-sm font-bold px-2.5 py-1 rounded-lg shadow-md z-10">
                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
            </span>

            <div class="pos-product-image relative aspect-[4/3] rounded-lg overflow-hidden mb-2 sm:mb-4 bg-slate-100">
                <div
                    class="w-full h-full bg-center bg-cover bg-no-repeat group-hover:scale-105 transition-transform duration-300"
                    style='background-image: url("{{ $product->image ? Storage::url($product->image) : asset('images/placeholder.png') }}");'>
                </div>

                @if($product->available_stock <= 0)
                    <div class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center">
                        <span
                            class="bg-red-500 text-white text-[10px] font-black px-3 py-1.5 rounded-full shadow-lg">
                            HABIS
                        </span>
                    </div>
                @endif
            </div>

       <div class="pos-product-details mt-auto">
    <h3
        class="pos-product-name font-bold text-sm sm:text-base text-slate-800 mb-0 leading-tight line-clamp-2 min-h-[28px] sm:min-h-[32px]">
        {{ $product->name }}
    </h3>

    <p class="text-xs sm:text-sm text-slate-400 mb-1 leading-tight">
        Stok: {{ $product->available_stock }}
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
            <button
                wire:click="loadMore"
                class="bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold py-3 px-8 rounded-xl flex items-center gap-2 transition-all shadow-sm">

                <span wire:loading.remove wire:target="loadMore" class="flex items-center gap-2">
                    <x-heroicon-o-archive-box-arrow-down class="w-5 h-5" />
                    Muat Lebih Banyak
                </span>

                <span wire:loading wire:target="loadMore"
                    class="animate-spin material-symbols-outlined text-sm">
                    progress_activity
                </span>

                <span wire:loading wire:target="loadMore">
                    Memuat...
                </span>
            </button>
        </div>
    @endif
</div>