<section class="hero relative flex items-center overflow-hidden" aria-label="{{ __('Introduction') }}">

    <div class="hero-image absolute inset-0 lg:inset-y-0 lg:left-auto lg:right-0 lg:w-[55%] z-0">
        <img
            src="{{ asset('images/hero.webp') }}"
            alt="{{ __('Two monitors with code editors in purple ambient lighting') }}"
            fetchpriority="high"
            class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-black/70 lg:hidden"></div>
    </div>
    <div class="relative z-10 w-full lg:w-[48%] px-6 lg:pl-[8%] lg:pr-16 py-16">
        <p class="font-sans text-sm font-semibold tracking-[0.2em] uppercase text-primary mb-10">{{ __('Articles & Tutorials') }}</p>
        <h1 class="font-serif leading-[1.15] text-white lg:text-text mb-7 text-[clamp(2rem,3.5vw,3.2rem)]">
            {{ __('Build things for the web.') }}<br>
            <em class="text-primary">{{ __('Make them count.') }}</em>
        </h1>
        <p class="text-white/80 lg:text-text-muted leading-[1.85] mb-5 text-[clamp(1rem,1.25vw,1.125rem)] max-w-[44ch]">
            {{ __('Practical articles on Laravel, Filament, Tailwind and the tools that shape modern web development.') }}
        </p>
        <p class="text-white/80 lg:text-text-muted leading-[1.85] text-[clamp(1rem,1.25vw,1.125rem)] max-w-[44ch]">
            {{ __('From the first request to accessibility audits — this blog gets concrete.') }}
        </p>
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
