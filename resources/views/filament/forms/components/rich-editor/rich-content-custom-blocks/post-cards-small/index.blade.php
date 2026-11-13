<section class="full-width post-cards-section pt-8 pb-16 lg:pt-10 lg:pb-24" aria-label="{{ $heading }}">
    <div class="content-width">
        <div class="text-center mb-12">
            <h2 class="font-sans inline-block text-xs font-semibold tracking-[0.2em] uppercase text-primary border border-primary/30 rounded-full px-4 py-1.5 mb-5">{{ $heading }}</h2>
            @if($intro)
                <p class="text-text-muted mx-auto max-w-[80ch]">{{ $intro }}</p>
            @endif
        </div>
        @if($posts->isNotEmpty())
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" role="list">
                @foreach($posts as $index => $post)
                    <li data-reveal>
                        <x-content-card-small :content="$post" :index="$index" />
                    </li>
                @endforeach
            </ul>
        @endif
        @if(!empty($categoryUrl) && !empty($categoryName))
            <div class="text-center mt-10">
                <a href="{{ $categoryUrl }}" class="internal-link internal-link--with-arrow">{{ __('All posts in :name', ['name' => $categoryName]) }}</a>
            </div>
        @endif
    </div>
</section>
