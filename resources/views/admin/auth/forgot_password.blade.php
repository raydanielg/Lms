@extends('admin.auth.auth_layout')

@section('content')
    @php
        $siteGeneralSettings = getGeneralSettings();
    @endphp

    <div class="p-4 m-3">
        <img src="{{ $siteGeneralSettings['logo'] ?? '' }}" alt="logo" width="40%" class="mb-5 mt-2">

        <h4>{{ trans('auth.forget_password') }}</h4>

        <p class="text-muted">{{ trans('update.we_will_send_a_link_to_reset_your_password') }}</p>

        <form method="POST" action="{{ getAdminPanelUrl() }}/forget-password">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="email">{{ trans('auth.email') }}</label>
                <input id="email" type="email" value="{{ old('email') }}" class="form-control  @error('email')  is-invalid @enderror"
                       name="email" tabindex="1"
                       required autofocus>
                @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            @if(!empty(getGeneralSecuritySettings('captcha_for_admin_forgot_pass')))
                @include('admin.includes.captcha_input')
            @endif

            <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.reset_password') }}</button>
        </form>

        <div class="text-center mt-3">
            <span class=" d-inline-flex align-items-center justify-content-center">or</span>
        </div>

        <div class="text-center mt-20">
            <span class="text-secondary">
                <a href="{{ getAdminPanelUrl() }}/login" class="font-weight-bold">{{ trans('auth.login') }}</a>
            </span>
        </div>
    </div>
@endsection
