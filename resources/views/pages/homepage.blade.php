@php
    $__siteUrl   = config('app.url');
    $__siteName  = setting('site.name');
    $__logoUrl   = \App\Models\Media::find(setting('site.logo'))?->url;
    $__sameAs    = array_values(array_filter([
        setting('social.instagram'),
        setting('social.youtube'),
        setting('social.pinterest'),
    ]));

    $__websiteJsonLd = json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => $__siteName,
        'url'      => $__siteUrl,
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT);

    $__orgData = array_filter([
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => $__siteName,
        'url'      => $__siteUrl,
        'logo'     => $__logoUrl ?: null,
        'sameAs'   => $__sameAs ?: null,
    ], fn($v) => $v !== null);
    $__orgJsonLd = json_encode($__orgData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT);

    $__webPageData = array_filter([
        '@context'      => 'https://schema.org',
        '@type'         => 'WebPage',
        'url'           => $__siteUrl,
        'name'          => $__siteName,
        'datePublished' => $content->published_at?->toIso8601String(),
        'dateModified'  => ($content->revised_at ?? $content->updated_at)?->toIso8601String(),
        'author'        => $content->author?->name ? [
            '@type' => 'Person',
            'name'  => $content->author->name,
        ] : null,
    ], fn($v) => $v !== null);
    $__webPageJsonLd = json_encode($__webPageData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PRETTY_PRINT);
@endphp
@push('jsonld')
    <script type="application/ld+json">{!! $__websiteJsonLd !!}</script>
    <script type="application/ld+json">{!! $__orgJsonLd !!}</script>
    <script type="application/ld+json">{!! $__webPageJsonLd !!}</script>
@endpush

<x-layouts.app>
    @include('homepage._hero', ['content' => $content])

    @if($content->content)
        <div class="rich-content mt-8 mb-8">
            @if($content->hasAffiliateLinks())
                <x-affiliate-disclosure />
            @endif
            {!! $content->renderSafeRichContent('content') !!}
        </div>
    @endif
</x-layouts.app>
