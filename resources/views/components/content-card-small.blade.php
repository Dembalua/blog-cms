@props(['content', 'index' => 0, 'category' => null, 'headingLevel' => 3])

@php
    $date = $content->revised_at ?? $content->published_at;
    $isRevised = (bool) $content->revised_at;
    $url = isset($content->path->url) && $content->path->url !== '#' ? url($content->path->url) : ($content->path->url ?? '#');
    $resolvedCategory = $category ?? $content->category;
@endphp

<article class="group relative h-full flex flex-col rounded-xl overflow-hidden border border-primary/30 transition-all duration-300">
    <a href="{{ $url }}"
       wire:navigate
       class="flex flex-col h-full focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:rounded-xl">

        <div class="shrink-0">
            @if($content->featureImage)
                <div class="h-44 overflow-hidden">
                    <img src="{{ $content->featureImage->card_url }}"
                         alt="{{ $content->featureImage->alt ?? '' }}"
                         loading="{{ $index < 6 ? 'eager' : 'lazy' }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                </div>
            @else
                <div class="h-44 overflow-hidden">
                    <x-placeholder-image class="h-44 w-full transform-gpu transition-transform duration-500 group-hover:scale-105" />
                </div>
            @endif
        </div>

        <div class="px-5 pt-5 pb-6 flex flex-col justify-between gap-4 flex-1 {{ $content->is_featured ? 'bg-primary/8' : '' }}">
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between text-primary">
                    @if($resolvedCategory)
                        <span class="text-xs uppercase tracking-widest text-primary">{{ $resolvedCategory->name }}</span>
                    @endif
                    @if($content->is_featured)
                        <svg class="w-3.5 h-3.5 text-primary shrink-0 ml-auto" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 1 L13.2 9.5 L19.8 4.2 L14.5 10.8 L23 12 L14.5 13.2 L19.8 19.8 L13.2 14.5 L12 23 L10.8 14.5 L4.2 19.8 L9.5 13.2 L1 12 L9.5 10.8 L4.2 4.2 L10.8 9.5 Z"/>
                        </svg>
                    @endif
                </div>
                <h{{ $headingLevel }} class="h6 font-semibold leading-snug text-text">{{ $content->title }}</h{{ $headingLevel }}>
            </div>
            <time class="text-xs text-text-subtle block" datetime="{{ $date->toDateString() }}">
                {{ $isRevised ? __('Updated') . ' ' : '' }}{{ $date->translatedFormat('j. F Y') }}
            </time>
        </div>

    </a>
</article>
