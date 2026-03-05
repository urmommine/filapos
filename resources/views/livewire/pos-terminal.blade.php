<div class="h-full flex flex-row" x-data="{ mobileCartOpen: false }">
    {{-- Sidebar Navigation --}}
    @include('livewire.pos-terminal._sidebar')

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col h-full overflow-hidden bg-background-light">
        @include('livewire.pos-terminal._header')
        @include('livewire.pos-terminal._category-bar')

        <div class="pos-content-area flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 pb-6 sm:pb-8">
            @include('livewire.pos-terminal._product-grid')
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
    @include('livewire.pos-terminal._cart-panel')

    {{-- ==================== MODALS ==================== --}}
    @include('livewire.pos-terminal._modal-checkout')
    @include('livewire.pos-terminal._modal-discount')
    @include('livewire.pos-terminal._modal-profile')
    @include('livewire.pos-terminal._modal-customer')

    {{-- Scripts --}}
    @include('livewire.pos-terminal._scripts')
</div>