<div class="d-none" id="sendMessageModal">
    <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('site.send_message') }}</h3>

    <form action="/users/{{ $user->id }}/send-message" method="post">
        {{ csrf_field() }}

        <div class="form-group">
            <label class="input-label">{{ trans('public.title') }}</label>
            <input type="text" name="title" class="form-control"/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.email') }}</label>
            <input type="text" name="email" class="form-control"/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.description') }}</label>
            <textarea name="description" class="form-control" rows="6"></textarea>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label font-weight-500">{{ trans('site.captcha') }}</label>
            <div class="row align-items-center">
                <div class="col">
                    <input type="text" name="captcha" class="form-control">

                    <div class="invalid-feedback"></div>
                </div>
                <div class="col d-flex align-items-center">
                    <img id="captchaImageComment" class="captcha-image" src="">

                    <button type="button" class="js-refresh-captcha btn-transparent ml-15">
                        <i data-feather="refresh-ccw" width="24" height="24" class=""></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-30 d-flex align-items-center justify-content-end">
            <button type="button" class="js-send-message-submit btn btn-primary">{{ trans('site.send_message') }}</button>
            <button type="button" class="btn btn-danger ml-10 close-swl">{{ trans('public.close') }}</button>
        </div>
    </form>
</div>
