<div class="row">
    <div class="col-12 col-md-5">

        <div class="form-group d-flex align-items-center mt-20">
            <label class="" for="enable_tank_you_messageSwitch">{{ trans('update.enable_tank_you_message') }}</label>
            <div class="custom-control custom-switch ml-3">
                <input type="checkbox" name="enable_tank_you_message" class="custom-control-input" id="enable_tank_you_messageSwitch" {{ (!empty($form) and $form->enable_tank_you_message) ? 'checked' : '' }}>
                <label class="custom-control-label" for="enable_tank_you_messageSwitch"></label>
            </div>
        </div>

        <div class="js-enable-tank-you-message-fields {{ ((!empty($form) and $form->enable_tank_you_message) or !empty(old('enable_tank_you_message'))) ? '' : 'd-none' }}">

            <div class="form-group ">
                <label class="input-label">{{ trans('update.tank_you_message_title') }}</label>
                <input type="text" name="tank_you_message_title" value="{{ !empty($form) ? $form->tank_you_message_title : old('tank_you_message_title') }}" class="form-control @error('tank_you_message_title')  is-invalid @enderror" placeholder=""/>
                @error('tank_you_message_title')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group mt-15">
                <label class="input-label">{{ trans('update.tank_you_message_description') }}</label>
                <textarea name="tank_you_message_description" rows="5" class="form-control @error('tank_you_message_description')  is-invalid @enderror" >{!! !empty($form) ? $form->tank_you_message_description : old('tank_you_message_description')  !!}</textarea>
                @error('tank_you_message_description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>


            <div class="form-group mt-15">
                <label class="input-label">{{ trans('update.tank_you_message_image') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text admin-file-manager" data-input="tank_you_message_image" data-preview="holder">
                            <i class="fa fa-upload"></i>
                        </button>
                    </div>
                    <input type="text" name="tank_you_message_image" id="tank_you_message_image" value="{{ !empty($form) ? $form->tank_you_message_image : old('tank_you_message_image') }}" class="form-control @error('tank_you_message_image')  is-invalid @enderror"/>
                    <div class="input-group-append">
                        <button type="button" class="input-group-text admin-file-view" data-input="tank_you_message_image">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('tank_you_message_image')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

        </div>

    </div>
</div>
