<section class="my-10">

    <div class="flex flex-col gap-3 lg:w-10/12 lg:mx-auto">

    @if($heading)
        <h2>{{ $heading }}</h2>
    @endif
        @foreach($items as $item)
            <details class="group card-neumorphic rounded-xl border border-primary/20">
                <summary class="flex items-center justify-between gap-6 px-6 py-3.5 cursor-pointer select-none [&::-webkit-details-marker]:hidden marker:hidden">
                    <h3 class="h5 mb-0">{{ $item['question'] }}</h3>
                    <span class="shrink-0 text-primary motion-safe:transition-transform motion-safe:duration-300 group-open:rotate-45" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/>
                        </svg>
                    </span>
                </summary>
                <p class="pl-6 pr-16 text-text-muted">
                    {!! nl2br(e($item['answer'])) !!}
                </p>
            </details>
        @endforeach
    </div>

</section>
