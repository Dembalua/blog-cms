<x-layouts.app>
    @php view()->share('pageTitle', __('RSS Feed')) @endphp

    <section class="feeds-page" aria-label="{{ __('RSS Feed') }}">
        <div class="content-width feeds-content">

            <div class="feeds-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                    <path fill="currentColor" d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27zm0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93z"/>
                </svg>
            </div>

            <h1 class="feeds-heading">{{ __('Never miss a post – follow us via RSS') }}</h1>

            <p class="feeds-description">{{ __('Just add our URL to your feed reader. Add the blog address once, and every new article will appear there automatically.') }}</p>

            <div class="feeds-url-block card-neumorphic" x-data="{ copied: false }">
                <p class="feeds-url-label">{{ __('Feed URL') }}</p>
                <div class="feeds-url-row">
                    <code class="feeds-url-text">{{ $feedUrl }}</code>
                    <button
                        type="button"
                        class="feeds-copy-btn"
                        @click="navigator.clipboard.writeText('{{ $feedUrl }}'); copied = true; setTimeout(() => copied = false, 2500)"
                        :aria-label="copied ? '{{ __('Copied') }}' : '{{ __('Copy URL') }}'"
                    >
                        <span x-show="!copied">{{ __('Copy') }}</span>
                        <span x-show="copied" x-cloak>{{ __('Copied') }}</span>
                    </button>
                </div>
            </div>

        </div>
    </section>
</x-layouts.app>
