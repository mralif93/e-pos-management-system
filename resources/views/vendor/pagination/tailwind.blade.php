@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">

        {{-- Results Summary --}}
        <div class="text-sm text-gray-500">
            @if ($paginator->firstItem())
                Showing
                <span class="font-semibold text-gray-700">{{ $paginator->firstItem() }}</span>
                –
                <span class="font-semibold text-gray-700">{{ $paginator->lastItem() }}</span>
                of
                <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span>
                results
            @else
                Showing <span class="font-semibold text-gray-700">{{ $paginator->count() }}</span> results
            @endif
        </div>

        {{-- Page Navigation --}}
        <div class="flex items-center gap-1">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed bg-white">
                    <i class="hgi-stroke hgi-arrow-left-01 text-[16px]"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all"
                    aria-label="{{ __('pagination.previous') }}">
                    <i class="hgi-stroke hgi-arrow-left-01 text-[16px]"></i>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-gray-400 cursor-default select-none">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-semibold bg-indigo-600 text-white border border-indigo-600 shadow-sm shadow-indigo-200 cursor-default">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 bg-white hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all"
                                aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 transition-all"
                    aria-label="{{ __('pagination.next') }}">
                    <i class="hgi-stroke hgi-arrow-right-01 text-[16px]"></i>
                </a>
            @else
                <span
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed bg-white">
                    <i class="hgi-stroke hgi-arrow-right-01 text-[16px]"></i>
                </span>
            @endif

        </div>
    </nav>
@endif