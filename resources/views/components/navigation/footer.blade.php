<div class="w-full bg-primary/12">
    <div class="content-width py-5 lg:py-6">
        <div class="flex flex-col items-center gap-5">

            <div class="w-full flex flex-col items-center gap-5 sm:grid sm:grid-cols-3 sm:gap-0">

                <div class="shrink-0 sm:justify-self-start">
                    @if($siteLogo || $siteLogoDark)
                        <a href="/" wire:navigate aria-label="{{ $siteName }}">
                            @if($siteLogo)
                                <img src="{{ $siteLogo->url }}" alt="{{ $siteName }}" height="40" class="h-10 w-auto dark:hidden">
                            @endif
                            @if($siteLogoDark)
                                <img src="{{ $siteLogoDark->url }}" alt="{{ $siteName }}" height="40" class="h-10 w-auto hidden dark:block">
                            @elseif($siteLogo)
                                <img src="{{ $siteLogo->url }}" alt="{{ $siteName }}" height="40" class="h-10 w-auto hidden dark:block">
                            @endif
                        </a>
                    @else
                        <a href="/" wire:navigate class="font-bold text-base text-primary">
                            {{ $siteName }}
                        </a>
                    @endif
                </div>

                @if(!empty($footerMenuItems))
                <nav aria-label="{{ __('Footer navigation') }}" class="sm:justify-self-center">
                <ul class="flex flex-wrap gap-x-6 gap-y-2 justify-center">
                    @foreach($footerMenuItems as $item)
                        <li>
                            <a href="{{ $item->model?->path?->url ? url($item->model->path->url) : '#' }}"
                               wire:navigate
                               class="text-sm text-text-muted hover:text-primary transition-colors duration-200">
                                {{ $item->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            @endif

            <div class="flex gap-4 items-center shrink-0 sm:justify-self-end">
                @if($socialInstagram)
                    <a href="{{ $socialInstagram }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                       class="text-text-muted hover:text-primary transition-colors duration-200">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16">
                            <path fill="currentColor" d="M8 5.67C6.71 5.67 5.67 6.72 5.67 8S6.72 10.33 8 10.33S10.33 9.28 10.33 8S9.28 5.67 8 5.67ZM15 8c0-.97 0-1.92-.05-2.89c-.05-1.12-.31-2.12-1.13-2.93c-.82-.82-1.81-1.08-2.93-1.13C9.92 1 8.97 1 8 1s-1.92 0-2.89.05c-1.12.05-2.12.31-2.93 1.13C1.36 3 1.1 3.99 1.05 5.11C1 6.08 1 7.03 1 8s0 1.92.05 2.89c.05 1.12.31 2.12 1.13 2.93c.82.82 1.81 1.08 2.93 1.13C6.08 15 7.03 15 8 15s1.92 0 2.89-.05c1.12-.05 2.12-.31 2.93-1.13c.82-.82 1.08-1.81 1.13-2.93c.06-.96.05-1.92.05-2.89Zm-7 3.59c-1.99 0-3.59-1.6-3.59-3.59S6.01 4.41 8 4.41s3.59 1.6 3.59 3.59s-1.6 3.59-3.59 3.59Zm3.74-6.49c-.46 0-.84-.37-.84-.84s.37-.84.84-.84s.84.37.84.84a.8.8 0 0 1-.24.59a.8.8 0 0 1-.59.24Z"/>
                        </svg>
                    </a>
                @endif
                @if($socialYoutube)
                    <a href="{{ $socialYoutube }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube"
                       class="text-text-muted hover:text-primary transition-colors duration-200">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 1024 768">
                            <path fill="currentColor" d="M928 736q-222 32-416 32q-86 0-190-8t-165-16l-61-8q-27-5-47.5-37.5t-30-78.5t-14-86T0 461V307Q0 52 96 32Q318 0 512 0q86 0 190 8t165 16l61 8q29 4 49.5 36.5T1007 148t13 86t4 73v154q0 36-3 73t-12 85t-30 80t-51 37zM693 359L431 199q-11-10-29-5.5T384 208v352q0 11 18 15t29-6l262-160q11-10 11-25t-11-25z"/>
                        </svg>
                    </a>
                @endif
                @if($socialPinterest)
                    <a href="{{ $socialPinterest }}" target="_blank" rel="noopener noreferrer" aria-label="Pinterest"
                       class="text-text-muted hover:text-primary transition-colors duration-200">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                            <path fill="currentColor" d="M10.015 0C4.484 0 0 4.473 0 9.99c0 4.232 2.638 7.847 6.364 9.3c-.088-.79-.166-2.002.034-2.865c.183-.78 1.175-4.964 1.175-4.964s-.3-.6-.3-1.484c0-1.386.808-2.426 1.811-2.426c.855 0 1.268.64 1.268 1.406c0 .858-.545 2.14-.829 3.327c-.238.994.502 1.804 1.483 1.804c1.778 0 3.148-1.87 3.148-4.572c0-2.384-1.723-4.058-4.184-4.058c-2.848 0-4.518 2.135-4.518 4.333c0 .86.329 1.786.742 2.284c.083.1.094.188.071.288c-.075.312-.244.999-.279 1.135c-.044.188-.143.226-.335.138c-1.249-.575-2.032-2.398-2.032-3.872c0-3.146 2.296-6.043 6.616-6.043c3.474 0 6.175 2.472 6.175 5.769c0 3.446-2.178 6.218-5.207 6.218c-1.014 0-1.966-.524-2.304-1.149l-.625 2.374c-.225.87-.84 1.96-1.252 2.621A10.07 10.07 0 0 0 9.988 20C15.508 20 20 15.53 20 10.01C20 4.493 15.507.023 9.988.023L10.015 0Z"/>
                        </svg>
                    </a>
                @endif
                <a href="{{ route('feed.info') }}" wire:navigate aria-label="{{ __('RSS feed') }}"
                   class="text-text-muted hover:text-primary transition-colors duration-200">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27zm0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93z"/>
                    </svg>
                </a>
            </div>

            </div>

        </div>
    </div>

    <span class="fade-rule"></span>
    <div>
        <div class="content-width py-3 flex flex-col gap-1 items-center sm:flex-row sm:justify-between">
            <p class="text-xs text-text-subtle mb-0">&copy; {{ $siteName }} {{ date('Y') }}</p>
            @if($footerSlogan)
                <p class="text-xs text-text-subtle italic mb-0">{{ $footerSlogan }}</p>
            @endif
        </div>
    </div>

</div>
