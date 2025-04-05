<div id="addStatisticItemForm"
     data-action="{{ getAdminPanelUrl("/settings/personalization/statistics/". (!empty($editStatistic) ? $editStatistic->id."/updateItem" : 'storeItem')) }}">
    @if(!empty(getGeneralSettings('content_translate')))
        <div class="form-group">
            <label class="input-label">{{ trans('auth.language') }}</label>
            <select name="locale" class="form-control {{ !empty($editStatistic) ? 'js-statistic-locale' : '' }}">
                @foreach($userLanguages as $lang => $language)
                    <option value="{{ $lang }}" @if($locale == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
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
        <label>{{ trans('admin/main.title') }}</label>
        <input type="text" name="title" value="{{ (!empty($editStatistic)) ? $editStatistic->title : '' }}" class="js-ajax-title form-control "/>
        <div class="invalid-feedback"></div>
    </div>

    <div class="form-group">
        <label>{{ trans('admin/main.description') }}</label>
        <textarea rows="3" name="description" class="js-ajax-description form-control ">{{ (!empty($editStatistic)) ? $editStatistic->description : '' }}</textarea>
        <div class="invalid-feedback"></div>
    </div>

    <div class="form-group">
        <label>{{ trans('admin/main.background') }}</label>
        <div class="input-group colorpickerinput">
            <input type="text" name="color" class="js-ajax-color form-control" value="{{ (!empty($editStatistic)) ? $editStatistic->color : '' }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <i class="fas fa-fill-drip"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="input-label">{{ trans('admin/main.icon') }}</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <button type="button" class="input-group-text admin-file-manager" data-input="statisticIcon" data-preview="holder">
                    <i class="fa fa-chevron-up"></i>
                </button>
            </div>
            <input type="text" name="icon" id="statisticIcon" value="{{ (!empty($editStatistic)) ? $editStatistic->icon : '' }}" class="js-ajax-icon form-control"/>
        </div>
    </div>

    <div class="form-group">
        <label>{{ trans('admin/main.count') }}</label>
        <input type="number" name="count" value="{{ (!empty($editStatistic)) ? $editStatistic->count : '' }}" class="js-ajax-count form-control " min="0"/>
        <div class="invalid-feedback"></div>
    </div>


    <div class="d-flex align-items-center justify-content-end">
        <button type="button" class="js-save-statistic btn btn-primary">{{ trans('admin/main.save') }}</button>
        <button type="button" class="close-swl btn btn-danger ml-2">{{ trans('admin/main.cancel') }}</button>
    </div>
</div>
