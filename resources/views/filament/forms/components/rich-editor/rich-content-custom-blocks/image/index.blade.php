@if($image)
    <figure class="my-12 flex flex-col items-center">
        <div class="relative w-full md:w-[calc((100%-3rem)/2)] aspect-3/2">
            <div class="absolute inset-0 rounded-lg bg-secondary rotate-[4deg]"></div>
            <div class="absolute inset-0 rounded-lg bg-primary -rotate-[3deg]"></div>
            <img
                src="{{ $image->url }}"
                alt="{{ $image->alt ?? '' }}"
                @if($image->caption) title="{{ $image->caption }}" @endif
                class="relative w-full h-full object-cover rounded-lg"
                loading="lazy"
            >
        </div>
        @if($image->caption)
            <figcaption class="text-sm text-text-muted text-center mt-6 md:mt-8">{{ $image->caption }}</figcaption>
        @endif
    </figure>
@endif
