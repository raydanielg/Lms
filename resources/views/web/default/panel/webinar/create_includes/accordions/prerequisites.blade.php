<li data-id="{{ !empty($prerequisite) ? $prerequisite->id :'' }}" class="accordion-row bg-white rounded-sm panel-shadow mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="prerequisite_{{ !empty($prerequisite) ? $prerequisite->id :'record' }}">
        <div class="font-weight-bold text-dark-blue" href="#collapsePrerequisite{{ !empty($prerequisite) ? $prerequisite->id :'record' }}" aria-controls="collapsePrerequisite{{ !empty($prerequisite) ? $prerequisite->id :'record' }}" data-parent="#prerequisitesAccordion" role="button" data-toggle="collapse" aria-expanded="true">
            <span>{{ (!empty($prerequisite) and !empty($prerequisite->prerequisiteWebinar)) ? $prerequisite->prerequisiteWebinar->title .' - '. $prerequisite->prerequisiteWebinar->teacher->full_name : trans('public.add_new_prerequisites') }}</span>
        </div>

        <div class="d-flex align-items-center">
            <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

            @if(!empty($prerequisite))
                <div class="btn-group dropdown table-actions mr-15">
                    <button type="button" class="btn-transparent dropdown-toggle d-flex align-items-center justify-content-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i data-feather="more-vertical" height="20"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="/panel/prerequisites/{{ $prerequisite->id }}/delete" class="delete-action btn btn-sm btn-transparent">{{ trans('public.delete') }}</a>
                    </div>
                </div>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapsePrerequisite{{ !empty($prerequisite) ? $prerequisite->id :'record' }}" aria-controls="collapsePrerequisite{{ !empty($prerequisite) ? $prerequisite->id :'record' }}" data-parent="#prerequisitesAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapsePrerequisite{{ !empty($prerequisite) ? $prerequisite->id :'record' }}" aria-labelledby="prerequisite_{{ !empty($prerequisite) ? $prerequisite->id :'record' }}" class=" collapse @if(empty($prerequisite)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="prerequisite-form" data-action="/panel/prerequisites/{{ !empty($prerequisite) ? $prerequisite->id . '/update' : 'store' }}">
                <input type="hidden" name="ajax[{{ !empty($prerequisite) ? $prerequisite->id : 'new' }}][webinar_id]" value="{{ !empty($webinar) ? $webinar->id :'' }}">

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="form-group mt-15">
                            <label class="input-label d-block">{{ trans('public.select_prerequisites') }}</label>
                            <select name="ajax[{{ !empty($prerequisite) ? $prerequisite->id : 'new' }}][prerequisite_id]" class="js-ajax-prerequisite_id @if(empty($prerequisite)) form-control @endif prerequisites-select2" data-webinar-id="{{  !empty($webinar) ? $webinar->id : '' }}" data-placeholder="{{ trans('public.search_prerequisites') }}">
                                @if(!empty($prerequisite) and !empty($prerequisite->prerequisiteWebinar))
                                    <option selected value="{{ $prerequisite->prerequisiteWebinar->id }}">{{ $prerequisite->prerequisiteWebinar->title .' - '. $prerequisite->prerequisiteWebinar->teacher->full_name }}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mt-30 d-flex align-items-center justify-content-between mb-0">
                            <label class="cursor-pointer input-label" for="requiredPrerequisitesSwitch{{ !empty($prerequisite) ? $prerequisite->id : 'record' }}">{{ trans('public.required') }}</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" id="requiredPrerequisitesSwitch{{ !empty($prerequisite) ? $prerequisite->id : 'record' }}" name="ajax[{{ !empty($prerequisite) ? $prerequisite->id : 'new' }}][required]" class="custom-control-input" @if(!empty($prerequisite) and $prerequisite->required) checked="checked" @endif>
                                <label class="custom-control-label" for="requiredPrerequisitesSwitch{{ !empty($prerequisite) ? $prerequisite->id : 'record' }}"></label>
                            </div>
                        </div>

                        <div class="mt-5">
                            <p class="font-12 text-gray">- {{ trans('webinars.required_hint') }}</p>
                        </div>

                    </div>
                </div>

                <div class="mt-30 d-flex align-items-center">
                    <button type="button" class="js-save-prerequisite btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(empty($prerequisite))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>
