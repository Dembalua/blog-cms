<x-layouts.app pageTitle="{{ __('410 – This page is no longer here') }}">
    <div class="max-w-4xl mx-auto w-full px-6 py-16 overflow-x-hidden lg:overflow-x-visible">
        <section class="flex flex-col lg:flex-row lg:items-center gap-14 lg:gap-24">

            <div class="relative flex items-center justify-center shrink-0 w-56 h-56 lg:w-64 lg:h-64 mx-auto lg:mx-0" aria-hidden="true">
                <div class="absolute inset-4 rounded-full border-8 border-primary/10 animate-ping [animation-duration:5s] motion-reduce:animate-none"></div>
                <div class="absolute w-44 h-44 lg:w-52 lg:h-52 rounded-full border-4 border-primary/15 animate-pulse [animation-duration:4s] motion-reduce:animate-none"></div>
                <div class="absolute w-32 h-32 lg:w-40 lg:h-40 rounded-full border-2 border-primary/20"></div>
                <div class="absolute w-20 h-20 lg:w-24 lg:h-24 rounded-full bg-primary/15 border border-primary/40 flex items-center justify-center">
                    <span class="h3 font-semibold text-primary mb-0">410</span>
                </div>
            </div>

            <div class="flex flex-col items-center text-center lg:items-start lg:text-left">
                <h1 class="font-serif text-4xl sm:text-5xl font-medium text-text leading-snug mb-8">
                    {{ __('Some things get to go') }}
                </h1>

                <p class="text-text-subtle text-md leading-relaxed mb-5">
                    {{ __('This page was intentionally removed.') }}
                </p>
                <div class="w-8 h-px bg-primary/30 mb-5 mx-auto lg:mx-0"></div>
                <p class="font-serif italic text-text-muted text-xl leading-relaxed mb-10">
                    {{ __('Letting go is also a practice.') }}<br>{{ __('The path continues.') }}
                </p>

                <a href="/"
                   class="inline-flex items-center px-5 py-2 bg-primary/15 text-primary border border-primary/30 rounded-lg text-sm font-medium hover:bg-primary/25 transition-colors">
                    {{ __('Back to home') }}
                </a>
            </div>

        </section>
    </div>
</x-layouts.app>
