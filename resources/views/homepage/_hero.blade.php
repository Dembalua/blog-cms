<section class="hero relative flex items-center overflow-hidden" aria-label="{{ __('Introduction') }}">

    <div class="hero-image absolute inset-0 lg:inset-y-0 lg:left-auto lg:right-0 lg:w-[55%] z-0">
        <img
            src="{{ $content->heroImage?->homepage_hero_url }}"
            alt="{{ $content->heroImage?->alt ?? $content->hero_headline }}"
            fetchpriority="high"
            class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-black/70 lg:hidden"></div>
    </div>
    <div class="relative z-10 w-full lg:w-[48%] px-6 lg:pl-[8%] lg:pr-16 py-16">
        <p class="font-sans text-sm font-semibold tracking-[0.2em] uppercase text-primary mb-10">{{ $content->hero_kicker }}</p>
        <h1 class="font-serif leading-[1.15] text-white lg:text-text mb-7 text-[clamp(2rem,3.5vw,3.2rem)]">
            {{ $content->hero_headline }}<br>
            <em class="text-primary">{{ $content->hero_headline_accent }}</em>
        </h1>
        @foreach(preg_split('/\r\n|\r|\n/', trim($content->hero_text)) as $paragraph)
            @if(trim($paragraph) !== '')
                <p class="text-white/80 lg:text-text-muted leading-[1.85] mb-5 last:mb-0 text-[clamp(1rem,1.25vw,1.125rem)] max-w-[44ch]">
                    {{ $paragraph }}
                </p>
            @endif
        @endforeach
    </div>

    <div class="absolute bottom-0 left-0 right-0 z-20 leading-0">
        <svg viewBox="0 0 1440 72" xmlns="http://www.w3.org/2000/svg"
             preserveAspectRatio="none" class="w-full block" height="72" aria-hidden="true">
            <path d="M0,36 Q240,72 480,36 Q720,0 960,36 Q1200,72 1440,36 L1440,72 L0,72 Z"
                  class="fill-background"/>
            <path d="M0,36 Q240,72 480,36 Q720,0 960,36 Q1200,72 1440,36"
                  fill="none" stroke="var(--color-primary)" stroke-width="2"/>
        </svg>
    </div>
</section>
