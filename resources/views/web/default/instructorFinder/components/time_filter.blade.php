@php
    $days = ['saturday', 'sunday','monday','tuesday','wednesday','thursday','friday'];

    $requestDays = request()->get('day');
    if (!is_array($requestDays)) {
        $requestDays = [$requestDays];
    }
@endphp

<div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">
    <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('public.time') }}</h3>

    <div class="mt-35">
        @foreach($days as $day)
            <div class="custom-control custom-checkbox mb-20 full-checkbox w-100">
                <input type="checkbox" name="day[]" value="{{ $day }}" class="custom-control-input" id="day_{{ $day }}" {{ (in_array($day, $requestDays)) ? 'checked' : '' }}>
                <label class="custom-control-label font-14 w-100" for="day_{{ $day }}">{{ trans('panel.'.$day) }}</label>
            </div>
        @endforeach
    </div>

    <div class="form-group">
        <label class="form-label">{{ trans('update.time_range') }}</label>
        <div
            class="range wrunner-value-bottom"
            id="timeRangeInstructorPage"
            data-minLimit="0"
            data-maxLimit="23"
        >
            <input type="hidden" name="min_time" value="{{ request()->get('min_time') ?? null }}">
            <input type="hidden" name="max_time" value="{{ request()->get('max_time') ?? null }}">
        </div>
    </div>
</div>
