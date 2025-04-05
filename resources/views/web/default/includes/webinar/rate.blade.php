<div class="stars-card d-flex align-items-center {{ $className ?? ' mt-15' }}">
    @php
        $i = 5;
    @endphp

    @if((!empty($rate) and $rate > 0) or !empty($showRateStars))
        @while(--$i >= 5 - $rate)
            <i data-feather="star" width="20" height="20" class="active"></i>
        @endwhile
        @while($i-- >= 0)
            <i data-feather="star" width="20" height="20" class=""></i>
        @endwhile

        @if(empty($dontShowRate) or !$dontShowRate)
            <span class="badge badge-primary ml-10">{{ $rate }}</span>
        @endif
    @endif
</div>
