<section class="my-10">
    @if($heading)
        <h2>{{ $heading }}</h2>
    @endif

    @if($intro)
        <p>{!! nl2br(e($intro)) !!}</p>
    @endif

    <div class="flex flex-col gap-10">
        @foreach($items as $item)
            <div class="relative pl-6 md:pl-8 py-2">
                <span class="section-bar-wave absolute left-0 top-3 bottom-3 w-5 bg-gradient-to-b from-primary via-primary/60 to-transparent" aria-hidden="true"></span>
                <h3 class="text-primary text-[clamp(1.25rem,2vw,1.5rem)]">{{ $item['heading'] }}</h3>
                <p class="text-text-muted mb-0">{!! nl2br(e($item['body'])) !!}</p>
                @if(!empty($item['link_html']))
                    <p class="mt-2 mb-0">{!! $item['link_html'] !!}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>
