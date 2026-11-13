
<div class="w-full bg-background fixed z-50 transition-all duration-300"
     x-data="{ mobileOpen: false, activeItem: null, scrolled: false }"
     x-on:close-nav.window="mobileOpen = false; activeItem = null"
     @scroll.window="scrolled = (window.scrollY > 4)"
     @mouseleave="activeItem = null"
     :class="scrolled ? 'nav-shadow' : ''">

    <div class="w-full transition-colors duration-300"
         :class="activeItem !== null || mobileOpen ? 'border-b border-b-primary/50' : ''">
        <div class="w-full lg:content-width">
            <nav aria-label="{{ __('Main navigation') }}">
                <div class="py-3 px-6 lg:px-0 flex items-center justify-between">

                    <div class="shrink-0">
                        @if($siteLogo || $siteLogoDark)
                            <a href="/" wire:navigate aria-label="{{ $siteName }} – {{ __('Home') }}">
                                @if($siteLogo)
                                    <img src="{{ $siteLogo->url }}" alt="{{ $siteName }}"
                                         height="44"
                                         class="h-11 lg:h-14 w-auto dark:hidden">
                                @endif
                                @if($siteLogoDark)
                                    <img src="{{ $siteLogoDark->url }}" alt="{{ $siteName }}"
                                         height="44"
                                         class="h-11 lg:h-14 w-auto hidden dark:block">
                                @elseif($siteLogo)
                                    <img src="{{ $siteLogo->url }}" alt="{{ $siteName }}"
                                         height="44"
                                         class="h-11 lg:h-14 w-auto hidden dark:block">
                                @endif
                            </a>
                        @else
                            <a href="/" wire:navigate
                               class="font-serif text-xl font-medium text-text hover:text-primary transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary rounded-sm">
                                {{ $siteName }}
                            </a>
                        @endif
                    </div>

                    <div class="hidden lg:flex font-medium flex-1 justify-center space-x-10" role="list">
                        @foreach($headerMenuItems as $item)
                            @php
                                $itemPath = $item->model?->path?->url ? trim($item->model->path->url, '/') : null;
                                $isActive = $itemPath && (request()->is($itemPath) || request()->is($itemPath . '/*'));
                            @endphp
                            <div role="listitem">
                            @if($item->children->isNotEmpty())
                                <a href="{{ $itemPath ? url($itemPath) : '#' }}"
                                   wire:navigate
                                   @mouseenter="activeItem = {{ $item->id }}"
                                   :aria-expanded="(activeItem === {{ $item->id }}).toString()"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg relative transition-all duration-200
                                          focus:outline-none focus:ring-2 focus:ring-primary
                                          {{ $isActive
                                              ? 'text-primary bg-primary/15'
                                              : 'text-text-muted hover:text-text hover:bg-primary/10' }}">
                                    {{ $item->title }}
                                    <svg class="w-3 h-3 shrink-0 transition-transform duration-200 ease-out"
                                         :class="activeItem === {{ $item->id }} ? 'rotate-180' : ''"
                                         aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ $itemPath ? url($itemPath) : '#' }}"
                                   wire:navigate
                                   @mouseenter="activeItem = null"
                                   class="inline-flex items-center px-3 py-1.5 rounded-lg relative transition-all duration-200
                                          focus:outline-none focus:ring-2 focus:ring-primary
                                          {{ $isActive
                                              ? 'text-primary bg-primary/15'
                                              : 'text-text-muted hover:text-text hover:bg-primary/10' }}">
                                    {{ $item->title }}
                                </a>
                            @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3">

                        <button @click="$store.theme.toggle()"
                                role="switch"
                                :aria-checked="$store.theme.isDark.toString()"
                                :aria-label="$store.theme.isDark ? 'Activate light mode' : 'Activate dark mode'"
                                class="hidden lg:flex cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-full
                                       active:scale-95 transition-transform duration-100">
                            <span class="relative flex h-10 w-20 rounded-full toggle-track shrink-0">
                                <span class="absolute top-1 left-1 h-8 w-8 rounded-full z-10 bg-primary/30
                                             shadow-[0_2px_12px_color-mix(in_srgb,var(--color-primary)_30%,transparent)]
                                             transition-transform duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                                      :class="$store.theme.isDark ? 'translate-x-10' : 'translate-x-0'">
                                </span>
                                <span class="absolute inset-0 flex items-center z-20 pointer-events-none text-text">
                                    <span class="flex-1 flex items-center justify-center">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25">
                                            <path fill="currentColor" d="M12.023 2.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 1 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75"/>
                                            <path fill="currentColor" fill-rule="evenodd" d="M6.523 12.5a5.5 5.5 0 1 1 11 0a5.5 5.5 0 0 1-11 0m5.5-4a4 4 0 1 0 0 8a4 4 0 0 0 0-8" clip-rule="evenodd"/>
                                            <path fill="currentColor" d="M19.095 6.491a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06zm2.928 6.009a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5a.75.75 0 0 1 .75.75m-3.989 7.07a.75.75 0 0 0 1.06-1.061l-1.06-1.06a.75.75 0 1 0-1.06 1.06zm-6.011-.07a.75.75 0 0 1 .75.75v1.5a.75.75 0 1 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75m-4.95-.985a.75.75 0 0 0-1.06-1.061l-1.061 1.06a.75.75 0 0 0 1.06 1.061zM5.023 12.5a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5a.75.75 0 0 1 .75.75m.99-4.954a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 0 0-1.061 1.06z"/>
                                        </svg>
                                    </span>
                                    <span class="flex-1 flex items-center justify-center">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <g fill="currentColor" fill-opacity="0">
                                                <path d="M15.22 6.03L17.75 4.09L14.56 4L13.5 1L12.44 4L9.25 4.09L11.78 6.03L10.87 9.09L13.5 7.28L16.13 9.09L15.22 6.03Z">
                                                    <animate fill="freeze" attributeName="fill-opacity" begin="0.7s" dur="0.4s" values="0;1"/>
                                                </path>
                                                <path d="M19.61 12.25L21.25 11L19.19 10.95L18.5 9L17.81 10.95L15.75 11L17.39 12.25L16.8 14.23L18.5 13.06L20.2 14.23L19.61 12.25Z">
                                                    <animate fill="freeze" attributeName="fill-opacity" begin="1.1s" dur="0.4s" values="0;1"/>
                                                </path>
                                            </g>
                                            <path fill="none" stroke="currentColor" stroke-dasharray="56" stroke-dashoffset="56" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6 C7 12.08 11.92 17 18 17 C18.53 17 19.05 16.96 19.56 16.89 C17.95 19.36 15.17 21 12 21 C7.03 21 3 16.97 3 12 C3 8.83 4.64 6.05 7.11 4.44 C7.04 4.95 7 5.47 7 6 Z">
                                                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="56;0"/>
                                            </path>
                                        </svg>
                                    </span>
                                </span>
                            </span>
                        </button>

                        <div class="flex lg:hidden">
                            <button @click="mobileOpen = !mobileOpen"
                                    class="relative w-8 h-8 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    aria-label="{{ __('Open menu') }}"
                                    :aria-expanded="mobileOpen.toString()">
                                <span aria-hidden="true"
                                      class="block absolute h-0.5 w-8 bg-text transition-all duration-300 ease-in-out"
                                      :class="mobileOpen ? 'rotate-45 top-4' : 'top-2'"></span>
                                <span aria-hidden="true"
                                      class="block absolute h-0.5 w-8 bg-text transition-all duration-300 ease-in-out"
                                      :class="mobileOpen ? 'opacity-0 top-4' : 'top-4'"></span>
                                <span aria-hidden="true"
                                      class="block absolute h-0.5 w-8 bg-text transition-all duration-300 ease-in-out"
                                      :class="mobileOpen ? '-rotate-45 top-4' : 'top-6'"></span>
                            </button>
                        </div>

                    </div>
                </div>

                <div x-show="mobileOpen"
                     @click.outside="mobileOpen = false"
                     x-transition:enter="transition ease-out duration-300 motion-reduce:duration-0"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200 motion-reduce:duration-0"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     x-cloak
                     class="absolute w-full lg:hidden bg-background border-b border-border">
                    <div class="font-medium w-full flex flex-col items-center">
                        @foreach($headerMenuItems as $item)
                            <div class="w-10/12 flex flex-col py-3 border-b border-border last:border-b-0">
                                @if($item->children->isNotEmpty())
                                    <div x-data="{ expanded: false }">
                                        <div class="flex items-center justify-between gap-2">
                                            <a href="{{ $item->model?->path?->url ? url($item->model->path->url) : '#' }}"
                                               wire:navigate
                                               @click="$dispatch('close-nav')"
                                               class="text-text-muted hover:text-text transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary rounded-sm">
                                                {{ $item->title }}
                                            </a>
                                            <button @click="expanded = !expanded"
                                                    :aria-expanded="expanded.toString()"
                                                    aria-label="{{ __('Toggle submenu :title', ['title' => $item->title]) }}"
                                                    class="p-1.5 rounded-full hover:bg-primary/10 hover:text-primary transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary cursor-pointer">
                                                <svg class="w-4 h-4 transition-transform duration-200 ease-out" :class="expanded ? 'rotate-180' : ''"
                                                     aria-hidden="true"
                                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div x-show="expanded"
                                             x-transition:enter="transition ease-out duration-200 motion-reduce:duration-0"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-150 motion-reduce:duration-0"
                                             x-transition:leave-start="opacity-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 -translate-y-1"
                                             class="mt-2 pl-4 flex flex-col gap-2 items-start w-full">
                                            @foreach($item->children as $child)
                                                @include('components.navigation._mobile-item', ['item' => $child])
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $item->model?->path?->url ? url($item->model->path->url) : '#' }}"
                                       wire:navigate
                                       @click="mobileOpen = false"
                                       class="text-text-muted hover:text-text transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary rounded-sm">
                                        {{ $item->title }}
                                    </a>
                                @endif
                            </div>
                        @endforeach

                        <div class="flex justify-center py-3">
                            <button @click="$store.theme.toggle()"
                                    role="switch"
                                    :aria-checked="$store.theme.isDark.toString()"
                                    :aria-label="$store.theme.isDark ? 'Activate light mode' : 'Activate dark mode'"
                                    class="flex cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-full
                                           active:scale-95 transition-transform duration-100">
                                <span class="relative flex h-9 w-18 rounded-full toggle-track shrink-0">
                                    <span class="absolute top-1 left-1 h-7 w-7 rounded-full z-10 bg-primary/30
                                                 shadow-[0_2px_10px_color-mix(in_srgb,var(--color-primary)_30%,transparent)]
                                                 transition-transform duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                                          :class="$store.theme.isDark ? 'translate-x-9' : 'translate-x-0'">
                                    </span>
                                    <span class="absolute inset-0 flex items-center z-20 pointer-events-none text-text">
                                        <span class="flex-1 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25">
                                                <path fill="currentColor" d="M12.023 2.5a.75.75 0 0 1 .75.75v1.5a.75.75 0 1 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75"/>
                                                <path fill="currentColor" fill-rule="evenodd" d="M6.523 12.5a5.5 5.5 0 1 1 11 0a5.5 5.5 0 0 1-11 0m5.5-4a4 4 0 1 0 0 8a4 4 0 0 0 0-8" clip-rule="evenodd"/>
                                                <path fill="currentColor" d="M19.095 6.491a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06zm2.928 6.009a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5a.75.75 0 0 1 .75.75m-3.989 7.07a.75.75 0 0 0 1.06-1.061l-1.06-1.06a.75.75 0 1 0-1.06 1.06zm-6.011-.07a.75.75 0 0 1 .75.75v1.5a.75.75 0 1 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75m-4.95-.985a.75.75 0 0 0-1.06-1.061l-1.061 1.06a.75.75 0 0 0 1.06 1.061zM5.023 12.5a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5a.75.75 0 0 1 .75.75m.99-4.954a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 0 0-1.061 1.06z"/>
                                            </svg>
                                        </span>
                                        <span class="flex-1 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <g fill="currentColor" fill-opacity="0">
                                                    <path d="M15.22 6.03L17.75 4.09L14.56 4L13.5 1L12.44 4L9.25 4.09L11.78 6.03L10.87 9.09L13.5 7.28L16.13 9.09L15.22 6.03Z">
                                                        <animate fill="freeze" attributeName="fill-opacity" begin="0.7s" dur="0.4s" values="0;1"/>
                                                    </path>
                                                    <path d="M19.61 12.25L21.25 11L19.19 10.95L18.5 9L17.81 10.95L15.75 11L17.39 12.25L16.8 14.23L18.5 13.06L20.2 14.23L19.61 12.25Z">
                                                        <animate fill="freeze" attributeName="fill-opacity" begin="1.1s" dur="0.4s" values="0;1"/>
                                                    </path>
                                                </g>
                                                <path fill="none" stroke="currentColor" stroke-dasharray="56" stroke-dashoffset="56" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6 C7 12.08 11.92 17 18 17 C18.53 17 19.05 16.96 19.56 16.89 C17.95 19.36 15.17 21 12 21 C7.03 21 3 16.97 3 12 C3 8.83 4.64 6.05 7.11 4.44 C7.04 4.95 7 5.47 7 6 Z">
                                                    <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="56;0"/>
                                                </path>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                            </button>
                        </div>

                    </div>
                </div>
            </nav>
        </div>
    </div>

    <div class="hidden lg:block absolute left-0 right-0 top-full">
        @foreach($headerMenuItems as $item)
            @if($item->children->isNotEmpty())
                <div x-show="activeItem === {{ $item->id }}"
                     x-cloak
                     x-transition:enter="transition ease-out duration-250 motion-reduce:duration-0"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200 motion-reduce:duration-0"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="w-full bg-background border-b border-border py-8 nav-shadow">
                    <div class="content-width flex flex-wrap gap-6">
                        @foreach($item->children as $child)
                            @include('components.navigation._mega-menu-column', ['item' => $child])
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
