@props(['navTree'])

@if($navTree->isNotEmpty())
    <aside class="hidden xl:block absolute top-0 w-40 right-[calc(83.333%+2.5rem)]">
        <nav aria-label="{{ __('Subcategories') }}">
            <ul class="border-l-2 border-primary">
                @foreach($navTree as $item)
                    @php $isActive = request()->url() === url($item['category']->path?->url ?? ''); @endphp
                    <li>
                        <a href="{{ $item['category']->path ? url($item['category']->path->url) : '#' }}"
                           wire:navigate
                           class="block text-sm px-4 py-2 transition-colors duration-200
                                  {{ $isActive ? 'text-primary' : 'text-text hover:text-primary' }}">
                            {{ $item['category']->name }}
                        </a>
                        @if($item['children']->isNotEmpty())
                            <ul class="ml-3 mb-1">
                                @foreach($item['children'] as $child)
                                    @php $childActive = request()->url() === url($child->path?->url ?? ''); @endphp
                                    <li>
                                        <a href="{{ $child->path ? url($child->path->url) : '#' }}"
                                           wire:navigate
                                           class="block text-xs py-1.5 px-3 transition-colors duration-200
                                                  {{ $childActive ? 'text-primary font-medium' : 'text-text-muted hover:text-primary' }}">
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </aside>
@endif
