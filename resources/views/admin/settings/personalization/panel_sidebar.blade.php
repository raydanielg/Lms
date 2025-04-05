<div class="mt-3">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="name" value="panel_sidebar">
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
                    <label>{{ trans('admin/main.link') }}</label>
                    <input type="text" name="value[link]" value="{{ (!empty($itemValue) and !empty($itemValue['link'])) ? $itemValue['link'] : old('link') }}" class="form-control "/>
                </div>

                <div class="form-group">
                    <label class="input-label">{{ trans('admin/main.background') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="sidebarBackground" data-preview="holder">
                                <i class="fa fa-chevron-up"></i>
                            </button>
                        </div>
                        <input type="text" name="value[background]" id="sidebarBackground" value="{{ (!empty($itemValue) and !empty($itemValue['background'])) ? $itemValue['background'] : old('background') }}" class="form-control"/>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>
