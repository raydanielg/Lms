<div class="mt-20 p-20 rounded-sm shadow-lg border border-gray300 filters-container">
    <h3 class="category-filter-title font-20 font-weight-bold text-dark-blue">{{ trans('update.location') }}</h3>

    <div class="form-group mt-20">
        <label class="input-label font-weight-500">{{ trans('update.country') }}</label>

        <select name="country_id" class="form-control">
            <option value="">{{ trans('update.select_country') }}</option>

            @if(!empty($countries))
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ (request()->get('country_id') == $country->id) ? 'selected' : '' }}>{{ $country->title }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="form-group mt-10">
        <label class="input-label font-weight-500">{{ trans('update.province') }}</label>

        <select name="province_id" class="form-control" {{ empty($provinces) ? 'disabled' : '' }}>
            <option value="">{{ trans('update.select_province') }}</option>

            @if(!empty($provinces))
                @foreach($provinces as $province)
                    <option value="{{ $province->id }}" {{ (request()->get('province_id') == $province->id) ? 'selected' : '' }}>{{ $province->title }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="form-group mt-10">
        <label class="input-label font-weight-500">{{ trans('update.city') }}</label>

        <select name="city_id" class="form-control" {{ empty($cities) ? 'disabled' : '' }}>
            <option value="">{{ trans('update.select_city') }}</option>

            @if(!empty($cities))
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ (request()->get('city_id') == $city->id) ? 'selected' : '' }}>{{ $city->title }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="form-group mt-10">
        <label class="input-label font-weight-500">{{ trans('update.district') }}</label>

        <select name="district_id" class="form-control" {{ empty($districts) ? 'disabled' : '' }}>
            <option value="">{{ trans('update.select_district') }}</option>

            @if(!empty($districts))
                @foreach($districts as $district)
                    <option value="{{ $district->id }}" {{ (request()->get('district_id') == $district->id) ? 'selected' : '' }}>{{ $district->title }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
