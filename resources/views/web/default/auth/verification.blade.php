@extends(getTemplate().'.layouts.app')

@section('content')
    <div class="container">
        <div class="row login-container">
            <div class="col-12 col-md-6 pl-0">
                <img src="{{ getPageBackgroundSettings('verification') }}" class="img-cover" alt="Login">
            </div>

            <div class="col-12 col-md-6">

                <div class="login-card">
                    <h1 class="font-20 font-weight-bold">{{ trans('auth.account_verification') }}</h1>

                    <p>{{ trans('auth.account_verification_hint',['username' => $username]) }}</p>
                    <form method="post" action="/verification" class="mt-35">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <input type="hidden" name="username" value="{{ $usernameValue }}">

                        <div class="form-group">
                            <label class="input-label" for="code">{{ trans('auth.code') }}:</label>
                            <input type="tel" name="code" class="form-control @error('code') is-invalid @enderror" id="code"
                                   aria-describedby="codeHelp">
                            @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block mt-20">{{ trans('auth.verification') }}</button>
                    </form>

                    <div class="text-center mt-20">
                        <span class="text-secondary">
                            <a href="/verification/resend" class="font-weight-bold">{{ trans('auth.resend_code') }}</a>
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
