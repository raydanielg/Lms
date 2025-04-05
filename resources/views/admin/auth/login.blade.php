@extends('admin.auth.auth_layout')

@section('content')
    @php
        $siteGeneralSettings = getGeneralSettings();
    @endphp

    <div class="p-4 m-3">
        <img src="{{ $siteGeneralSettings['logo'] ?? '' }}" alt="logo" width="40%" class="mb-5 mt-2">

        <h4 class="text-dark font-weight-normal">{{ trans('admin/main.welcome') }} <span class="font-weight-bold">{{ $siteGeneralSettings['site_name'] ?? '' }}</span></h4>

        <p class="text-muted">{{ trans('auth.admin_tagline') }}</p>

        <form method="POST" action="{{ getAdminPanelUrl() }}/login" class="needs-validation" novalidate="">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
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

            <div class="form-group">
                <div class="d-block">
                    <label for="password" class="control-label">{{ trans('auth.password') }}</label>
                </div>
                <input id="password" type="password" class="form-control  @error('password')  is-invalid @enderror"
                       name="password" tabindex="2" required>
                @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            @if(!empty(getGeneralSecuritySettings('captcha_for_admin_login')))
                @include('admin.includes.captcha_input')
            @endif

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="remember" class="custom-control-input" tabindex="3"
                           id="remember-me">
                    <label class="custom-control-label"
                           for="remember-me">{{ trans('auth.remember_me') }}</label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                    {{ trans('auth.login') }}
                </button>
            </div>
        </form>

        <a href="{{ getAdminPanelUrl() }}/forget-password" class="">{{ trans('auth.forget_your_password') }}</a>
    </div>
@endsection
