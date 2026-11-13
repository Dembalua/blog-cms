@if($item->children->isNotEmpty())
    <div x-data="{ expanded: false }">
        <div class="flex items-center justify-between gap-2 py-1.5">
            <a href="{{ $item->model?->path?->url ? url($item->model->path->url) : '#' }}"
               wire:navigate
               @click="$dispatch('close-nav')"
               class="hover:text-primary transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary">
                {{ $item->title }}
            </a>
            <button @click="expanded = !expanded"
                    :aria-expanded="expanded.toString()"
                    aria-label="{{ __('Toggle submenu :title', ['title' => $item->title]) }}"
                    class="p-0.5 hover:text-primary transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                <svg class="w-3.5 h-3.5 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                </svg>
            </button>
        </div>
        <div x-show="expanded"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mt-1 pl-3 flex flex-col gap-1.5">
            @foreach($item->children as $child)
                @include('components.navigation._mobile-item', ['item' => $child])
            @endforeach
        </div>
    </div>
@else
    <a href="{{ $item->model?->path?->url ? url($item->model->path->url) : '#' }}"
       wire:navigate
       @click="$dispatch('close-nav')"
       class="block py-1.5 hover:text-primary transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary">
        {{ $item->title }}
    </a>
@endif
