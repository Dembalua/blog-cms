<section class="cluster-nav content-width mb-14 px-6 lg:px-10 py-8"
         aria-label="{{ __('Article series') }}"
         x-data="{
             isSlider: false,
             checkSlider() {
                 const s = this.$refs.slider;
                 this.isSlider = s.scrollWidth > s.clientWidth;
             },
             prev() { this.$refs.slider.scrollBy({ left: -this.$refs.slider.clientWidth * 0.8, behavior: 'smooth' }) },
             next() { this.$refs.slider.scrollBy({ left:  this.$refs.slider.clientWidth * 0.8, behavior: 'smooth' }) },
         }"
         x-init="$nextTick(() => checkSlider())">

    <h2 class="h3 font-serif text-center italic text-text mb-6">
        {{ __('More in') }} <span class="text-primary">{{ $clusterTitle }}</span>
    </h2>

    <div class="relative">

        <button @click="prev"
                x-show="isSlider"
                x-cloak
                class="absolute -left-10 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full flex items-center justify-center bg-background border border-primary text-primary cursor-pointer transition-opacity duration-200 hover:opacity-70"
                aria-label="{{ __('Previous') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" aria-hidden="true">
                <path fill="currentColor" d="M10.843 13.069L6.232 8.384a.546.546 0 0 1 0-.768l4.61-4.685a.552.552 0 0 0 0-.771a.53.53 0 0 0-.759 0l-4.61 4.684a1.65 1.65 0 0 0 0 2.312l4.61 4.684a.53.53 0 0 0 .76 0a.552.552 0 0 0 0-.771"/>
            </svg>
        </button>

        <div class="cluster-slider py-2" x-ref="slider">

            @php
                $cards = collect();
                if (! $isPillar && $pillar) $cards->push($pillar);
                foreach ($subArticles as $a) $cards->push($a);
            @endphp

            @foreach($cards as $card)
                <div class="cluster-slider-card">
                    <x-content-card-small :content="$card" :index="$loop->index" />
                </div>
            @endforeach

        </div>

        <button @click="next"
                x-show="isSlider"
                x-cloak
                class="absolute -right-10 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full flex items-center justify-center bg-background border border-primary text-primary cursor-pointer transition-opacity duration-200 hover:opacity-70"
                aria-label="{{ __('Next') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" aria-hidden="true">
                <path fill="currentColor" d="m5.157 13.069l4.611-4.685a.546.546 0 0 0 0-.768L5.158 2.93a.552.552 0 0 1 0-.771a.53.53 0 0 1 .759 0l4.61 4.684a1.65 1.65 0 0 1 0 2.312l-4.61 4.684a.53.53 0 0 1-.76 0a.552.552 0 0 1 0-.771"/>
            </svg>
        </button>

    </div>

</section>
