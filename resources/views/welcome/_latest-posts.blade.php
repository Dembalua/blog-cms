<section class="pt-8 pb-16 lg:pt-10 lg:pb-24" aria-label="{{ __('Latest posts') }}">
    <div class="content-width">
        <div class="text-center mb-12">
            <h2 class="font-sans inline-block text-xs font-semibold tracking-[0.2em] uppercase text-primary border border-primary/30 rounded-full px-4 py-1.5 mb-5">{{ __("What's new") }}</h2>
            <p class="text-text-muted mx-auto max-w-[52ch]">{{ __('A quick look at the latest posts on Laravel, Filament, UX and modern web development.') }}</p>
        </div>
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" role="list">
            @foreach($latestPosts as $index => $post)
                <li data-reveal>
                    <x-content-card :content="$post" :index="$index" />
                </li>
            @endforeach
        </ul>
    </div>
</section>
