<div class=" mt-3">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="name" value="become_instructor_section">
                <input type="hidden" name="page" value="personalization">

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
                    <label class="input-label">{{ trans('admin/main.image') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="image" data-preview="holder">
                                <i class="fa fa-chevron-up"></i>
                            </button>
                        </div>
                        <input type="text" name="value[image]" id="image" value="{{ (!empty($itemValue) and !empty($itemValue['image'])) ? $itemValue['image'] : old('image') }}" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.title') }}</label>
                    <input type="text" name="value[title]" value="{{ (!empty($itemValue) and !empty($itemValue['title'])) ? $itemValue['title'] : old('title') }}" class="form-control "/>
                </div>

                <div class="form-group">
                    <label>{{ trans('public.description') }}</label>
                    <textarea type="text" name="value[description]" rows="5" class="form-control ">{{ (!empty($itemValue) and !empty($itemValue['description'])) ? $itemValue['description'] : old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ trans('update.button') }} 1</label>
                    <div class="row">
                        <div class="col-6">
                            <label>{{ trans('admin/main.title') }}</label>
                            <input type="text" name="value[button1][title]" value="{{ (!empty($itemValue) and !empty($itemValue['button1']) and !empty($itemValue['button1']['title'])) ? $itemValue['button1']['title'] : '' }}" class="form-control "/>
                        </div>
                        <div class="col-6">
                            <label>{{ trans('admin/main.link') }}</label>
                            <input type="text" name="value[button1][link]" value="{{ (!empty($itemValue) and !empty($itemValue['button1']) and !empty($itemValue['button1']['link'])) ? $itemValue['button1']['link'] : '' }}" class="form-control "/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ trans('update.button') }} 2</label>
                    <div class="row">
                        <div class="col-6">
                            <label>{{ trans('admin/main.title') }}</label>
                            <input type="text" name="value[button2][title]" value="{{ (!empty($itemValue) and !empty($itemValue['button2']) and !empty($itemValue['button2']['title'])) ? $itemValue['button2']['title'] : '' }}" class="form-control "/>
                        </div>
                        <div class="col-6">
                            <label>{{ trans('admin/main.link') }}</label>
                            <input type="text" name="value[button2][link]" value="{{ (!empty($itemValue) and !empty($itemValue['button2']) and !empty($itemValue['button2']['link'])) ? $itemValue['button2']['link'] : '' }}" class="form-control "/>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>
