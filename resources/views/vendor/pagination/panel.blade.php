@if (!empty($paginator) and $paginator->hasPages())
    <nav class="d-flex justify-content-center">
        <ul class="custom-pagination d-flex align-items-center justify-content-center">
            @if ($paginator->onFirstPage())
                <li class="previous disabled">
                    <i data-feather="chevron-left" width="20" height="20" class=""></i>
                </li>
            @else
                <li class="previous">
                    <a href="{{ $paginator->previousPageUrl() }}">
                        <i data-feather="chevron-left" width="20" height="20" class=""></i>
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)

                @php
                    $separate = false;
                @endphp

                @if (is_array($element))
                    @foreach ($element as $page => $url)


                        @if(($page < 2) or ($page + 1 > $paginator->lastPage()) or (($page < $paginator->currentPage() + 2) and ($page > $paginator->currentPage() - 2)))
                            @php
                                $separate = true;
                            @endphp

                            @if ($page == $paginator->currentPage())
                                <li><span class="active">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif

                        @elseif($separate)
                            <li aria-disabled="true"><span>...</span></li>

                            @php
                                $separate = false;
                            @endphp
                        @endif
                    @endforeach
                @endif

            @endforeach

            @if ($paginator->hasMorePages())
                <li class="next">
                    <a href="{{ $paginator->nextPageUrl() }}">
                        <i data-feather="chevron-right" width="20" height="20" class=""></i>
                    </a>
                </li>
            @else
                <li class="next disabled">
                    <i data-feather="chevron-right" width="20" height="20" class=""></i>
                </li>
            @endif

            {{--<li><span class="d-flex align-items-center justify-content-center">...</span></li>--}}
        </ul>
    </nav>
@endif
