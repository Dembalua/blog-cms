@props(['items', 'current' => null])

@if($items->isNotEmpty() || $current)
    <div class="w-full bg-primary/8 border-b border-border">
    <nav aria-label="{{ __('Breadcrumb') }}" class="content-width py-2">
        <ol
            class="flex items-center flex-wrap gap-y-1 text-xs tracking-wide md:tracking-widest uppercase"
            itemscope
            itemtype="https://schema.org/BreadcrumbList"
        >
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a
                    href="{{ url('/') }}"
                    wire:navigate
                    itemprop="item"
                    class="text-text-muted hover:text-primary transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary rounded-sm"
                >
                    <span itemprop="name">{{ __('Home') }}</span>
                </a>
                <meta itemprop="position" content="1">
            </li>

            @foreach($items as $index => $item)
                <li
                    class="flex items-center"
                    itemprop="itemListElement"
                    itemscope
                    itemtype="https://schema.org/ListItem"
                >
                    <svg class="mx-1.5 md:mx-2 w-3 h-3 shrink-0 text-primary/50 normal-case not-italic" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                    <a
                        href="{{ $item['url'] }}"
                        wire:navigate
                        itemprop="item"
                        class="text-text-muted hover:text-primary transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary rounded-sm"
                    >
                        <span itemprop="name">{{ $item['label'] }}</span>
                    </a>
                    <meta itemprop="position" content="{{ $index + 2 }}">
                </li>
            @endforeach

            @if($current)
                <li
                    class="flex items-center"
                    itemprop="itemListElement"
                    itemscope
                    itemtype="https://schema.org/ListItem"
                >
                    <svg class="mx-1.5 md:mx-2 w-3 h-3 shrink-0 text-primary/50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                    <span
                        class="text-text font-bold truncate max-w-[24ch]"
                        itemprop="name"
                        aria-current="page"
                    >{{ $current }}</span>
                    <meta itemprop="position" content="{{ $items->count() + 2 }}">
                </li>
            @endif
        </ol>
    </nav>
    </div>
@endif
