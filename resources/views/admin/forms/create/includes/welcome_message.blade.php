<div class="row">
    <div class="col-12 col-md-5">

        <div class="form-group d-flex align-items-center mt-20">
            <label class="" for="enable_welcome_messageSwitch">{{ trans('update.enable_welcome_message') }}</label>
            <div class="custom-control custom-switch ml-3">
                <input type="checkbox" name="enable_welcome_message" class="custom-control-input" id="enable_welcome_messageSwitch" {{ (!empty($form) and $form->enable_welcome_message) ? 'checked' : '' }}>
                <label class="custom-control-label" for="enable_welcome_messageSwitch"></label>
            </div>
        </div>

        <div class="js-enable-welcome-message-fields {{ ((!empty($form) and $form->enable_welcome_message) or !empty(old("enable_welcome_message"))) ? '' : 'd-none' }}">

            <div class="form-group ">
                <label class="input-label">{{ trans('update.welcome_message_title') }}</label>
                <input type="text" name="welcome_message_title" value="{{ !empty($form) ? $form->welcome_message_title : old('welcome_message_title') }}" class="form-control @error('welcome_message_title')  is-invalid @enderror" placeholder=""/>
                @error('welcome_message_title')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group mt-15">
                <label class="input-label">{{ trans('update.welcome_message_description') }}</label>
                <textarea name="welcome_message_description" rows="5" class="form-control @error('welcome_message_description')  is-invalid @enderror" >{!! !empty($form) ? $form->welcome_message_description : old('welcome_message_description')  !!}</textarea>
                @error('welcome_message_description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>


            <div class="form-group mt-15">
                <label class="input-label">{{ trans('update.welcome_message_image') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text admin-file-manager" data-input="welcome_message_image" data-preview="holder">
                            <i class="fa fa-upload"></i>
                        </button>
                    </div>
                    <input type="text" name="welcome_message_image" id="welcome_message_image" value="{{ !empty($form) ? $form->welcome_message_image : old('welcome_message_image') }}" class="form-control @error('welcome_message_image')  is-invalid @enderror"/>
                    <div class="input-group-append">
                        <button type="button" class="input-group-text admin-file-view" data-input="welcome_message_image">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('welcome_message_image')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

        </div>

    </div>
</div>
