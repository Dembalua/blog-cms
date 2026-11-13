<x-layouts.app>

    @if($category->image)
        <div class="relative w-full h-112 md:h-104 overflow-hidden">
            <img src="{{ $category->image->hero_url }}"
                 alt="{{ $category->name }}"
                 class="w-full h-full object-cover"
                 loading="eager">
            <div class="absolute inset-0 bg-linear-to-b from-transparent from-40% to-background"></div>
            <div class="absolute inset-0 flex items-center pb-14 md:pb-24">
                <div class="content-width">
                    <div class="inline-block backdrop-blur-xl bg-background/50 rounded-2xl px-6 py-5 max-w-xl">
                        <h1 class="font-serif font-semibold text-text mb-0">{{ $category->name }}</h1>
                        @if($category->description)
                            <p class="text-text text-sm mt-3 leading-relaxed">{{ $category->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <x-placeholder-image class="w-full h-112 md:h-104" :wide="true">
            <div class="absolute inset-0 bg-linear-to-b from-transparent from-40% to-background z-10"></div>
            <div class="absolute inset-0 flex items-center pb-14 md:pb-24 z-20">
                <div class="content-width">
                    <div class="inline-block backdrop-blur-xl bg-background/50 rounded-2xl px-6 py-5 max-w-xl">
                        <h1 class="font-serif font-semibold text-text mb-0">{{ $category->name }}</h1>
                        @if($category->description)
                            <p class="text-text text-sm mt-3 leading-relaxed">{{ $category->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </x-placeholder-image>
    @endif

    <div class="relative z-10 -mt-14 md:-mt-24 pb-24">

        <div class="relative">
            <x-category.side-nav :navTree="$navTree" />

            <div class="content-width">
                <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" role="list">
                    @foreach($contents as $index => $content)
                        <li data-reveal>
                            <x-content-card :content="$content" :index="$index" :category="$categoryMap->get($content->category_id)" :heading-level="2" />
                        </li>
                    @endforeach
                </ul>

                <div class="relative z-10">
                    {{ $contents->links() }}
                </div>
            </div>
        </div>
    </div>


</x-layouts.app>
