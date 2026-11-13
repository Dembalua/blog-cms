@php
    $faqItems = \App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\FaqBlock::extractItems($content->content);
@endphp

@push('jsonld')
    @php
        $__articleJsonLd = array_filter([
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $content->title,
            'description'   => $meta['meta_description'] ?? null,
            'datePublished' => $content->published_at?->toIso8601String(),
            'dateModified'  => ($content->revised_at ?? $content->published_at)?->toIso8601String(),
            'url'           => $meta['canonical_url'],
            'image'         => $meta['og_image'] ?? null,
            'author'        => [
                '@type' => 'Person',
                'name'  => $content->author?->name ?? setting('site.name'),
            ],
        ], fn($v) => $v !== null);
    @endphp
    <script type="application/ld+json">{!! json_encode($__articleJsonLd, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT) !!}</script>
@endpush

@if(!empty($faqItems))
    @php
        $__faqJsonLd = json_encode([
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(fn($item) => [
                '@type'          => 'Question',
                'name'           => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $item['answer'],
                ],
            ], $faqItems),
        ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT);
    @endphp
    @push('jsonld')
        <script type="application/ld+json">{!! $__faqJsonLd !!}</script>
    @endpush
@endif

<x-layouts.app>
    <article>
        <header>
            @php
                $date      = $content->revised_at ?? $content->published_at;
                $isRevised = (bool) $content->revised_at;
            @endphp

            @if($content->featureImage)
                <div class="relative w-full h-80 md:h-80">
                    <div class="absolute inset-0 overflow-hidden">
                        <img
                            src="{{ $content->featureImage->hero_url }}"
                            alt="{{ $content->featureImage->alt ?? $content->title }}"
                            class="w-full h-full object-cover"
                            loading="eager"
                        >
                        <div class="absolute inset-0 bg-primary/40"></div>
                        <div class="absolute inset-0 bg-linear-to-b from-transparent via-background/50 to-background"></div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 pb-6">
                        <div class="content-width">
                                <span class="inline-block bg-background text-primary text-xs font-bold rounded-xl px-3 py-1.5 mb-4">
                                    {{ $isRevised ? __('Updated') : __('Created') }}
                                    &middot; {{ $date->translatedFormat('j. F Y') }}
                                </span>
                                <h1 class="font-semibold text-text leading-snug">{{ $content->title }}</h1>
                        </div>
                    </div>
                </div>
            @else
                <div class="relative w-full h-72 md:h-72">
                    <div class="absolute inset-0 overflow-hidden">
                        <x-placeholder-image class="w-full h-full" :wide="true" />
                        <div class="absolute inset-0 bg-linear-to-b from-transparent via-background/50 to-background"></div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 pb-6">
                        <div class="content-width">
                            <span class="inline-block bg-background text-primary text-xs font-bold rounded-xl px-3 py-1.5 mb-4">
                                {{ $isRevised ? __('Updated') : __('Created') }}
                                &middot; {{ $date->translatedFormat('j. F Y') }}
                            </span>
                            <h1 class="font-semibold text-text leading-snug">{{ $content->title }}</h1>
                        </div>
                    </div>
                </div>
            @endif
        </header>

        <div class="rich-content">
            @if($content->hasAffiliateLinks())
                <x-affiliate-disclosure />
            @endif
            {!! $content->renderSafeRichContent('content') !!}
        </div>

        <x-cluster-navigation :content="$content" />
        <x-category-clusters-navigation :content="$content" />
    </article>
</x-layouts.app>
