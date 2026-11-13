@if(!empty($meta['meta_title']))
    <meta name="title" content="{{ $meta['meta_title'] }}">
@endif

@if(!empty($meta['meta_description']))
    <meta name="description" content="{{ $meta['meta_description'] }}">
@endif

@if(!empty($meta['meta_keywords']))
    <meta name="keywords" content="{{ implode(', ', $meta['meta_keywords']) }}">
@endif

@if(!empty($meta['robots']))
    <meta name="robots" content="{{ $meta['robots']->value }}">
@endif

@if(!empty($meta['author']))
    <meta name="author" content="{{ $meta['author'] }}">
@endif

@if(!empty($meta['og_title']))
    <meta property="og:title" content="{{ $meta['og_title'] }}">
@endif

@if(!empty($meta['og_description']))
    <meta property="og:description" content="{{ $meta['og_description'] }}">
@endif

@if(!empty($meta['og_image']))
    <meta property="og:image" content="{{ $meta['og_image'] }}">
@endif

@if(!empty($meta['og_url']))
    <meta property="og:url" content="{{ $meta['og_url'] }}">
@endif

@if(!empty($meta['og_type']))
    <meta property="og:type" content="{{ $meta['og_type'] }}">
@endif

@if(!empty($meta['canonical_url']))
    <link rel="canonical" href="{{ $meta['canonical_url'] }}">
@endif
