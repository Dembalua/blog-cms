<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function () {
            var stored = localStorage.getItem('frontend-theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <title>{{ __('Maintenance') }} – {{ $siteName }}</title>
    @if($siteFavicon)
        <link rel="icon" href="{{ $siteFavicon->url }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}?v={{ @filemtime(public_path('css/variables.css')) }}">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-background flex flex-col min-h-screen">
    <div class="maintenance-wrap flex-1">
        <div class="maintenance-inner">

            <div class="relative flex items-center justify-center shrink-0 w-56 h-56 lg:w-64 lg:h-64 mx-auto" aria-hidden="true">
                <div class="absolute inset-4 rounded-full border-8 border-primary/10 animate-ping [animation-duration:5s] motion-reduce:animate-none"></div>
                <div class="absolute w-44 h-44 lg:w-52 lg:h-52 rounded-full border-4 border-primary/15 animate-pulse [animation-duration:4s] motion-reduce:animate-none"></div>
                <div class="absolute w-32 h-32 lg:w-40 lg:h-40 rounded-full border-2 border-primary/20"></div>
                <div class="absolute w-20 h-20 lg:w-24 lg:h-24 rounded-full bg-primary/15 border border-primary/40 flex items-center justify-center">
                    <svg class="w-10 h-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.213-1.281Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </div>
            </div>

            <p class="maintenance-site-name">{{ \App\Models\Setting::get('site.name') }}</p>

            <h1 class="maintenance-title">{{ __('We are working on something') }}</h1>

            <div class="maintenance-divider" aria-hidden="true"></div>

            <p class="maintenance-text">{{ __('The site is currently unavailable.') }}<br>{{ __('Please check back soon.') }}</p>

        </div>
    </div>

    <footer>
        <x-navigation.footer />
    </footer>
</body>
</html>
