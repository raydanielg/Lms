<!-- Modal -->
<div class="d-none" id="webinarTicketModal">
    <h3 class="section-title after-line font-20 text-dark-blue mb-25">{{ trans('public.add_ticket') }}</h3>
    <div class="js-form" data-action="{{ getAdminPanelUrl() }}/tickets/store">
        <input type="hidden" name="bundle_id" value="{{ !empty($bundle) ? $bundle->id :'' }}">

        @if(!empty(getGeneralSettings('content_translate')))
            <div class="form-group">
                <label class="input-label">{{ trans('auth.language') }}</label>
                <select name="locale" class="form-control ">
                    @foreach($userLanguages as $lang => $language)
                        <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
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
            <label class="input-label">{{ trans('public.title') }}</label>
            <input type="text" name="title" class="js-ajax-title form-control" placeholder="{{ trans('forms.maximum_64_characters') }}"/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.date') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="dateRangeLabel">
                        <i class="fa fa-calendar text-white"></i>
                    </span>
                </div>
                <input type="text" name="date" class="js-ajax-date form-control date-range-picker" aria-describedby="dateRangeLabel"/>
                <div class="invalid-feedback"></div>
            </div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.discount') }} <span class="braces">(%)</span></label>
            <input type="text" name="discount" class="js-ajax-discount form-control" placeholder="10"/>
            <div class="invalid-feedback"></div>
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('public.capacity') }}</label>
            <input type="text" name="capacity" class="js-ajax-capacity form-control" placeholder="{{ trans('forms.empty_means_unlimited') }}"/>
            <div class="invalid-feedback"></div>
            <div class="text-muted text-small mt-1">{{ trans('admin/main.price_plan_modal_capacity_hint') }}</div>
        </div>

        <div class="mt-30 d-flex align-items-center justify-content-end">
            <button type="button" id="saveTicket" class="btn btn-primary">{{ trans('public.save') }}</button>
            <button type="button" class="btn btn-danger ml-2 close-swl">{{ trans('public.close') }}</button>
        </div>
    </div>
</div>
