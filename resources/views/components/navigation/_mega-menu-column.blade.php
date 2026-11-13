@if($item->children->isNotEmpty())
    <div class="flex flex-col gap-3 min-w-[9rem]">
        <a href="{{ $item->model?->path?->url ? url($item->model->path->url) : '#' }}"
           wire:navigate
           @click="activeItem = null"
           class="text-xs font-semibold uppercase tracking-widest text-primary hover:text-primary/70 transition-colors duration-200">
            {{ $item->title }}
        </a>
        <ul class="flex flex-col gap-0.5">
            @foreach($item->children as $child)
                <li>
                    <a href="{{ $child->model?->path?->url ? url($child->model->path->url) : '#' }}"
                       wire:navigate
                       @click="activeItem = null"
                       class="block text-sm text-text-muted hover:text-primary hover:bg-primary/10
                              rounded-md px-2 py-1 -mx-2 transition-all duration-200">
                        {{ $child->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <a href="{{ $item->model?->path?->url ? url($item->model->path->url) : '#' }}"
       wire:navigate
       @click="activeItem = null"
       class="self-start text-xs font-semibold uppercase tracking-widest text-primary hover:text-primary/70 transition-colors duration-200">
        {{ $item->title }}
    </a>
@endif
