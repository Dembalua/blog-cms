<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if(config('services.turnstile.site_key'))
            <meta name="turnstile-site-key" content="{{ config('services.turnstile.site_key') }}">
        @endif
        @if($sitePrivacyUrl)
            <meta name="privacy-url" content="{{ $sitePrivacyUrl }}">
        @endif
        <script>
            (function () {
                var stored = localStorage.getItem('frontend-theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (stored === 'dark' || (!stored && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
        @if(config('services.umami.website_id') && config('services.umami.script_url'))
            <script defer src="{{ config('services.umami.script_url') }}" data-website-id="{{ config('services.umami.website_id') }}"></script>
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    const startTime = Date.now();

                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'hidden') {
                            const duration = Math.round((Date.now() - startTime) / 1000);

                            if (duration > 3 && window.umami && typeof window.umami.track === 'function') {
                                window.umami.track('page-exit', { duration });
                            }
                        }
                    });
                });
            </script>
        @endif
        <link rel="stylesheet" href="{{ asset('css/variables.css') }}?v={{ @filemtime(public_path('css/variables.css')) }}">
        @if($siteFavicon)
            <link rel="icon" href="{{ $siteFavicon->url }}">
        @endif
        <title>
            @if(!empty($pageTitleFull))
                {{ $pageTitleFull }}
            @elseif(!empty($pageTitle))
                {{ $pageTitle }} | {{ $siteName }}
            @else
                {{ $siteName }}
            @endif
        </title>
        <link rel="alternate" type="application/rss+xml" title="{{ setting('rss.title') ?? $siteName }}" href="{{ url('/rss') }}">
        <x-meta-tags/>
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/theme.js'])
        @if(config('services.turnstile.site_key'))
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>
        @endif
    </head>
    <body class="bg-background flex flex-col min-h-screen">
        <a href="#main-content"
           class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-9999 focus:px-4 focus:py-2 focus:bg-primary focus:text-background focus:rounded-lg focus:text-sm focus:font-medium">
            {{ __('Skip to main content') }}
        </a>
        @unless($maintenanceModeActive ?? false)
        <header class="h-18 lg:h-20 shrink-0">
            <x-navigation.header />
        </header>
        @endunless

        <main id="main-content" class="flex-1">
            @if(isset($breadcrumbs))
                <x-breadcrumbs :items="$breadcrumbs" />
            @endif
            {{ $slot }}
        </main>

        <footer>
            <x-navigation.footer />
        </footer>
        @stack('jsonld')
        @livewireScripts
    </body>
</html>
