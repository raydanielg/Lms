@extends(getTemplate().'.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')

    <div class="container">
        @if(!empty(session()->has('msg')))
            <div class="alert alert-info alert-dismissible fade show mt-30" role="alert">
                {{ session()->get('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row login-container">

            <div class="col-12 col-md-6 pl-0">
                <img src="{{ getPageBackgroundSettings('login') }}" class="img-cover" alt="Login">
            </div>
            <div class="col-12 col-md-6">
                <div class="login-card">
                    <h1 class="font-20 font-weight-bold">{{ trans('auth.login_h1') }}</h1>

                    <form method="Post" action="/login" class="mt-35">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        @include('web.default.auth.includes.register_methods')


                        <div class="form-group">
                            <label class="input-label" for="password">{{ trans('auth.password') }}:</label>
                            <input name="password" type="password" class="form-control @error('password')  is-invalid @enderror" id="password" aria-describedby="passwordHelp">

                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        @if(!empty(getGeneralSecuritySettings('captcha_for_login')))
                            @include('web.default.includes.captcha_input')
                        @endif

                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.login') }}</button>
                    </form>

                    @if(session()->has('login_failed_active_session'))
                        <div class="d-flex align-items-center mt-20 p-15 danger-transparent-alert ">
                            <div class="danger-transparent-alert__icon d-flex align-items-center justify-content-center">
                                <i data-feather="alert-octagon" width="18" height="18" class=""></i>
                            </div>
                            <div class="ml-10">
                                <div class="font-14 font-weight-bold ">{{ session()->get('login_failed_active_session')['title'] }}</div>
                                <div class="font-12 ">{{ session()->get('login_failed_active_session')['msg'] }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="text-center mt-20">
                        <span class="badge badge-circle-gray300 text-secondary d-inline-flex align-items-center justify-content-center">{{ trans('auth.or') }}</span>
                    </div>

                    @if(!empty(getFeaturesSettings('show_google_login_button')))
                        <a href="/google" target="_blank" class="social-login mt-20 p-10 text-center d-flex align-items-center justify-content-center">
                            <img src="/assets/default/img/auth/google.svg" class="mr-auto" alt=" google svg"/>
                            <span class="flex-grow-1">{{ trans('auth.google_login') }}</span>
                        </a>
                    @endif

                    @if(!empty(getFeaturesSettings('show_facebook_login_button')))
                        <a href="{{url('/facebook/redirect')}}" target="_blank" class="social-login mt-20 p-10 text-center d-flex align-items-center justify-content-center ">
                            <img src="/assets/default/img/auth/facebook.svg" class="mr-auto" alt="facebook svg"/>
                            <span class="flex-grow-1">{{ trans('auth.facebook_login') }}</span>
                        </a>
                    @endif

                    <div class="mt-30 text-center">
                        <a href="/forget-password" target="_blank">{{ trans('auth.forget_your_password') }}</a>
                    </div>

                    <div class="mt-20 text-center">
                        <span>{{ trans('auth.dont_have_account') }}</span>
                        <a href="/register" class="text-secondary font-weight-bold">{{ trans('auth.signup') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/js/parts/forgot_password.min.js"></script>
@endpush
