@php
    $time=time();
    $productBadges = \App\Models\ProductBadgeContent::query()
                    ->where('targetable_id', $itemTarget->id)
                    ->where('targetable_type', $itemTarget->getMorphClass())
                    ->whereHas('badge', function ($query) use ($time) {
                        $query->where('enable', true);

                        $query->where(function ($query) use ($time) {
                            $query->whereNull('start_at');
                            $query->orWhere('start_at', '<', $time);
                        });

                        $query->where(function ($query) use ($time) {
                            $query->whereNull('end_at');
                            $query->orWhere('end_at', '>', $time);
                        });
                    })
                    ->with(['badge'])
                    ->get();
@endphp


@if($productBadges->isNotEmpty())
    @foreach($productBadges as $productBadge)
        <div class="badge d-flex align-items-center" style="color: {{ $productBadge->badge->color }}; background-color: {{ $productBadge->badge->background }}">
            @if(!empty($productBadge->badge->icon))
                <div class="size-32 mr-5">
                    <img src="{{ $productBadge->badge->icon }}" alt="{{ $productBadge->badge->title }}" class="img-cover">
                </div>
            @endif

            <span class="">{{ $productBadge->badge->title }}</span>
        </div>
    @endforeach
@endif
