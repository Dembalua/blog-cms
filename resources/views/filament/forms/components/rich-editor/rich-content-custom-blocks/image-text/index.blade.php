<section class="my-12">
    <div class="fade-rule"></div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start md:items-center py-12">

        <div class="{{ $imagePosition === 'left' ? 'md:order-last' : '' }} flex flex-col gap-4">
            @if($heading)
                <h2 class="text-primary pb-2">{{ $heading }}</h2>
            @endif
            <div class="text-text-muted leading-relaxed">{!! $body !!}</div>
        </div>

        <div class="{{ $imagePosition === 'left' ? 'md:order-first' : '' }}">
            @if($image)
                <img
                    src="{{ $image->url }}"
                    alt="{{ $image->alt ?? $heading ?? '' }}"
                    class="w-full aspect-3/2 object-cover rounded-lg"
                    loading="lazy"
                >
            @endif
        </div>

    </div>
    <div class="fade-rule"></div>
</section>
