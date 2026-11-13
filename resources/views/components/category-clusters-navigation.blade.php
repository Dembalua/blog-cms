<section class="category-clusters-nav content-width my-14"
         aria-label="{{ __('More topics') }}">

    <h2 class="font-serif text-center italic text-text-muted text-base mb-6">
        {{ __('More topics in this category') }}
    </h2>

    <ul class="list-none p-0 m-0 flex flex-col gap-3" role="list">
        @foreach($pillars as $pillar)
            <li>
                <a href="{{ $pillar->path?->url ? url($pillar->path->url) : '#' }}"
                   class="category-clusters-nav-item group flex items-center gap-4 px-5 py-4 rounded-2xl no-underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                    @if($pillar->featureImage)
                        <img src="{{ $pillar->featureImage->medium_url }}"
                             alt="{{ $pillar->featureImage->alt ?? $pillar->title }}"
                             class="w-12 h-12 rounded-xl object-cover shrink-0"
                             loading="lazy">
                    @else
                        <span class="category-clusters-nav-placeholder w-12 h-12 rounded-xl shrink-0"
                              aria-hidden="true"></span>
                    @endif
                    <div class="flex flex-col gap-0.5 flex-1 min-w-0">
                        <span class="text-xs text-primary uppercase tracking-wider">{{ $pillar->cluster->title }}</span>
                        <span class="text-sm font-semibold text-text group-hover:text-primary transition-colors duration-200 leading-snug">{{ $pillar->title }}</span>
                    </div>
                    <span class="category-clusters-nav-icon shrink-0" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" aria-hidden="true">
                            <path fill="currentColor" fill-rule="evenodd" d="M15 8A7 7 0 1 0 1 8a7 7 0 0 0 14 0M4.75 7.25a.75.75 0 0 0 0 1.5h4.69L8.22 9.97a.75.75 0 1 0 1.06 1.06l2.5-2.5a.75.75 0 0 0 0-1.06l-2.5-2.5a.75.75 0 0 0-1.06 1.06l1.22 1.22z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

</section>
