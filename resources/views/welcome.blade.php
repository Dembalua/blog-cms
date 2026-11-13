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
@endphp
@push('jsonld')
    <script type="application/ld+json">{!! $__websiteJsonLd !!}</script>
    <script type="application/ld+json">{!! $__orgJsonLd !!}</script>
@endpush

<x-layouts.app>
    @include('welcome._hero')
    @include('welcome._latest-posts')

</x-layouts.app>
