<div class="row">
    <div class="col-5">
        <div class="form-group">
            <label class="input-label" for="mobile">{{ trans('auth.country') }}:</label>
            <select name="country_code" class="form-control select2">
                @foreach(getCountriesMobileCode() as $country => $code)
                    <option value="{{ $code }}" @if($code == old('country_code')) selected @endif>{{ $country }}</option>
                @endforeach
            </select>

            @error('mobile')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="col-7">
        <div class="form-group">
            <label class="input-label" for="mobile">{{ trans('auth.mobile') }} {{ !empty($optional) ? "(". trans('public.optional') .")" : '' }}:</label>
            <input name="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror"
                   value="{{ old('mobile') }}" id="mobile" aria-describedby="mobileHelp">

            @error('mobile')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
</div>
