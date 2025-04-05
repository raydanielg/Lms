@php
    $registerMethod = getGeneralSettings('register_method') ?? 'mobile';
    $showOtherRegisterMethod = getFeaturesSettings('show_other_register_method') ?? false;
@endphp

@if($showOtherRegisterMethod)
    <div class="d-flex align-items-center wizard-custom-radio mb-20">
        <div class="wizard-custom-radio-item flex-grow-1">
            <input type="radio" name="type" value="email" id="emailType" class="" {{ (($registerMethod == 'email' and empty(old('type'))) or old('type') == "email") ? 'checked' : '' }}>
            <label class="font-12 cursor-pointer px-15 py-10" for="emailType">{{ trans('public.email') }}</label>
        </div>

        <div class="wizard-custom-radio-item flex-grow-1">
            <input type="radio" name="type" value="mobile" id="mobileType" class="" {{ (($registerMethod == 'mobile' and empty(old('type'))) or old('type') == "mobile") ? 'checked' : '' }}>
            <label class="font-12 cursor-pointer px-15 py-10" for="mobileType">{{ trans('public.mobile') }}</label>
        </div>
    </div>

    <div class="js-email-fields form-group {{ (($registerMethod == 'email' and empty(old('type'))) or old('type') == "email") ? '' : 'd-none' }}">
        <label class="input-label" for="email">{{ trans('public.email') }}:</label>
        <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" id="email"
               value="{{ old('email') }}" aria-describedby="emailHelp">
        @error('email')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>


    <div class="js-mobile-fields {{ (($registerMethod == 'mobile' and empty(old('type'))) or old('type') == "mobile") ? '' : 'd-none' }}">
        @include('web.default.auth.register_includes.mobile_field')
    </div>

@else
    @if($registerMethod == 'mobile')
        <input type="hidden" name="type" value="mobile">
        <div class="">
            @include('web.default.auth.register_includes.mobile_field')
        </div>

    @else
        <input type="hidden" name="type" value="email">

        <div class=" form-group">
            <label class="input-label" for="email">{{ trans('public.email') }}:</label>
            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                   value="{{ old('email') }}" aria-describedby="emailHelp">
            @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    @endif
@endif
