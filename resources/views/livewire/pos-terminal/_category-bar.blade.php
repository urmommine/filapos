{{-- Category Pills (swipeable, pinned below header) --}}
<div x-data="{
        isDown: false,
        isDragging: false,
        startX: 0,
        scrollLeft: 0,
        init() {
            this.$el.addEventListener('mousedown', (e) => {
                this.isDown = true;
                this.isDragging = false;
                this.$el.classList.add('cursor-grabbing');
                this.$el.classList.remove('snap-x', 'snap-mandatory', 'scroll-smooth');
                this.startX = e.pageX - this.$el.offsetLeft;
                this.scrollLeft = this.$el.scrollLeft;
            });
            this.$el.addEventListener('mouseleave', () => {
                this.isDown = false;
                this.$el.classList.remove('cursor-grabbing');
                this.$el.classList.add('snap-x', 'snap-mandatory', 'scroll-smooth');
            });
            this.$el.addEventListener('mouseup', () => {
                this.isDown = false;
                this.$el.classList.remove('cursor-grabbing');
                this.$el.classList.add('snap-x', 'snap-mandatory', 'scroll-smooth');
            });
            this.$el.addEventListener('mousemove', (e) => {
                if (!this.isDown) return;
                e.preventDefault();
                const x = e.pageX - this.$el.offsetLeft;
                const walk = (x - this.startX) * 1.5;
                if (Math.abs(walk) > 10) {
                    this.isDragging = true;
                }
                this.$el.scrollLeft = this.scrollLeft - walk;
            });
            this.$el.addEventListener('click', (e) => {
                if (this.isDragging) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);
        }
    }"
    class="pos-category-bar flex gap-2 px-4 sm:px-6 lg:px-8 py-2 sm:py-3 overflow-x-auto no-scrollbar snap-x snap-mandatory scroll-smooth shrink-0 bg-background-light cursor-grab">
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