@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp


<div class="mt-3" id="mobile_app">

    <form action="{{ getAdminPanelUrl() }}/settings/restriction_settings" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="page" value="personalization">
        <input type="hidden" name="restriction_settings" value="restriction_settings">

        <div class="row">
            <div class="col-12 col-md-6">

                @if(!empty(getGeneralSettings('content_translate')))
                    <div class="form-group">
                        <label class="input-label">{{ trans('auth.language') }}</label>
                        <select name="locale" class="form-control js-edit-content-locale">
                            @foreach($userLanguages as $lang => $language)
                                <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', (!empty($itemValue) and !empty($itemValue['locale'])) ? $itemValue['locale'] : app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                            @endforeach
                        </select>
                        @error('locale')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                @else
                    <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
                @endif

                <div class="form-group">
                    <label class="input-label mb-0">{{ trans('admin/main.title') }}</label>

                    <input type="text" name="value[title]" required value="{{ (!empty($itemValue) and !empty($itemValue['title'])) ? $itemValue['title'] : '' }}" class="form-control"/>
                </div>

                <div class="form-group">
                    <label class="input-label mb-0">{{ trans('admin/main.image') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="image" data-preview="holder">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <input type="text" name="value[image]" required id="image" value="{{ (!empty($itemValue) and !empty($itemValue['image'])) ? $itemValue['image'] : '' }}" class="form-control" placeholder="{{ trans('update.restriction_settings_image_placeholder') }}"/>
                    </div>
                </div>

                <div class="form-group ">
                    <label class="control-label">{{ trans('admin/main.description') }}</label>
                    <textarea name="value[description]" required rows="5" class="form-control text-left">{{ (!empty($itemValue) and !empty($itemValue['description'])) ? $itemValue['description'] : '' }}</textarea>
                </div>

            </div>
        </div>


        <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
    </form>

</div>

