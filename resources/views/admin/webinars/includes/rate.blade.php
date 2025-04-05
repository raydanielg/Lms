<div class="stars-card d-flex align-items-center {{ $className ?? ' mt-15' }}">
    @php
        $i = 5;
    @endphp

    @if((!empty($rate) and $rate > 0) or !empty($showRateStars))
        @while(--$i >= 5 - $rate)
            <i class="fa fa-star active"></i>
        @endwhile
        @while($i-- >= 0)
            <i class="fa fa-star"></i>
        @endwhile

        @if(empty($dontShowRate) or !$dontShowRate)
            <span class="badge badge-primary ml-10">{{ $rate }}</span>
        @endif
    @endif
</div>
