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