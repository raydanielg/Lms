@php
    $productBadges = \App\Models\ProductBadge::query()->get();
@endphp

@if($productBadges->isNotEmpty())
    <div class="form-group mt-15">
        <label class="input-label d-block">{{ trans('update.select_badges') }}</label>

        <select name="product_badges[]" multiple class="form-control select2"
                data-placeholder="{{ trans('update.select_badges') }}"
        >
            @foreach($productBadges as $productBadge)
                @php
                    $selected = $productBadge->contents()->where('targetable_id', $itemTarget->id)->where('targetable_type', $itemTarget->getMorphClass())->first();
                @endphp

                <option value="{{ $productBadge->id }}" {{ !empty($selected) ? 'selected' : '' }}>{{ $productBadge->title }}</option>
            @endforeach
        </select>

        <div class="text-muted text-small mt-1">{{ trans('update.select_badges_hint') }}</div>
    </div>
@endif
