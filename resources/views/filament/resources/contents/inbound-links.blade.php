@if($articles->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('resource.content_resource.inbound_links.empty') }}
    </p>
@else
    <div class="flex flex-col gap-5">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('resource.content_resource.inbound_links.count', ['count' => $articles->count()]) }}
    </p>

    <ul class="flex flex-col gap-2">
        @foreach($articles as $article)
            <li class="flex items-center gap-3 rounded-lg bg-gray-50 dark:bg-white/5 px-4 py-3">
                <span class="flex-1 text-sm font-medium text-gray-900 dark:text-white leading-snug">
                    {{ $article->title }}
                </span>
                <div class="flex items-center gap-2 shrink-0">
                    @if($article->category)
                        <x-filament::badge color="gray">
                            {{ $article->category->name }}
                        </x-filament::badge>
                    @endif
                    @if($article->is_pillar)
                        <x-filament::badge color="warning">
                            {{ __('resource.cluster_resource.contents_widget.is_pillar') }}
                        </x-filament::badge>
                    @endif
                    <x-filament::badge :color="$article->status->getColor()">
                        {{ $article->status->getLabel() }}
                    </x-filament::badge>
                </div>
            </li>
        @endforeach
    </ul>
    </div>
@endif
