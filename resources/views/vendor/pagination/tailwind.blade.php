@if ($paginator->hasPages())
    <nav aria-label="{{ __('Pagination') }}" class="flex items-center justify-center gap-1 mt-12">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span aria-hidden="true"
                  class="pagination-disabled inline-flex items-center justify-center w-9 h-9 rounded-full cursor-not-allowed opacity-40">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="m15 4l2 2l-6 6l6 6l-2 2l-8-8z"/>
                </svg>
            </span>
        @else
            <button onclick="window.location='{{ $paginator->previousPageUrl() }}'"
                    aria-hidden="true"
                    tabindex="-1"
                    class="pagination-link inline-flex items-center justify-center w-9 h-9 rounded-full">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="m15 4l2 2l-6 6l6 6l-2 2l-8-8z"/>
                </svg>
            </button>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span aria-hidden="true" class="pagination-dots inline-flex items-center justify-center w-9 h-9 text-sm select-none">
                    &hellip;
                </span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              class="pagination-current inline-flex items-center justify-center w-9 h-9 rounded-full text-base font-medium">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           wire:navigate
                           aria-label="{{ __('Page :page', ['page' => $page]) }}"
                           class="pagination-link inline-flex items-center justify-center w-9 h-9 rounded-full text-base">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button onclick="window.location='{{ $paginator->nextPageUrl() }}'"
                    aria-hidden="true"
                    tabindex="-1"
                    class="pagination-link inline-flex items-center justify-center w-9 h-9 rounded-full">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="m9.005 4l8 8l-8 8L7 18l6.005-6L7 6z"/>
                </svg>
            </button>
        @else
            <span aria-hidden="true"
                  class="pagination-disabled inline-flex items-center justify-center w-9 h-9 rounded-full cursor-not-allowed opacity-40">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="m9.005 4l8 8l-8 8L7 18l6.005-6L7 6z"/>
                </svg>
            </span>
        @endif

    </nav>
@endif
