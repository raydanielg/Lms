@php
    if (!empty($session->agora_settings)) {
        $session->agora_settings = json_decode($session->agora_settings);
    }
@endphp

<li data-id="{{ !empty($chapterItem) ? $chapterItem->id :'' }}" class="accordion-row bg-white rounded-sm border border-gray300 mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="session_{{ !empty($session) ? $session->id :'record' }}">
        <div class="d-flex align-items-center" href="#collapseSession{{ !empty($session) ? $session->id :'record' }}" aria-controls="collapseSession{{ !empty($session) ? $session->id :'record' }}" data-parent="#chapterContentAccordion{{ !empty($chapter) ? $chapter->id :'' }}" role="button" data-toggle="collapse" aria-expanded="true">
            <span class="chapter-icon chapter-content-icon mr-10">
                <i data-feather="file-text" class=""></i>
            </span>

            <div class="font-weight-bold text-dark-blue d-block">{{ !empty($session) ? $session->title : trans('public.add_new_sessions') }}</div>
        </div>

        <div class="d-flex align-items-center">

            @if(!empty($session) and $session->status != \App\Models\WebinarChapter::$chapterActive)
                <span class="disabled-content-badge mr-10">{{ trans('public.disabled') }}</span>
            @endif

            @if(!empty($session))
                <button type="button" data-item-id="{{ $session->id }}" data-item-type="{{ \App\Models\WebinarChapterItem::$chapterSession }}" data-chapter-id="{{ !empty($chapter) ? $chapter->id : '' }}" class="js-change-content-chapter btn btn-sm btn-transparent text-gray mr-10">
                    <i data-feather="grid" class="" height="20"></i>
                </button>
            @endif

            <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

            @if(!empty($session))
                <a href="/panel/sessions/{{ $session->id }}/delete" class="delete-action btn btn-sm btn-transparent text-gray">
                    <i data-feather="trash-2" class="mr-10 cursor-pointer" height="20"></i>
                </a>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseSession{{ !empty($session) ? $session->id :'record' }}" aria-controls="collapseSession{{ !empty($session) ? $session->id :'record' }}" data-parent="#chapterContentAccordion{{ !empty($chapter) ? $chapter->id :'' }}" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseSession{{ !empty($session) ? $session->id :'record' }}" aria-labelledby="session_{{ !empty($session) ? $session->id :'record' }}" class=" collapse @if(empty($session)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            <div class="js-content-form session-form" data-action="/panel/sessions/{{ !empty($session) ? $session->id . '/update' : 'store' }}">
                <input type="hidden" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][webinar_id]" value="{{ !empty($webinar) ? $webinar->id :'' }}">

                <div class="form-group">
                    <label class="input-label">{{ trans('webinars.select_session_api') }}</label>

                    <div class="js-session-api">
                        @foreach(getFeaturesSettings("available_session_apis") as $sessionApi)
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][session_api]" id="{{ $sessionApi }}_api_{{ !empty($session) ? $session->id : '' }}" value="{{ $sessionApi }}" @if((!empty($session) and $session->session_api == $sessionApi) or (empty($session) and $sessionApi == 'local')) checked @endif class="js-api-input custom-control-input" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}>
                                <label class="custom-control-label" for="{{ $sessionApi }}_api_{{ !empty($session) ? $session->id : '' }}">{{ trans('update.session_api_'.$sessionApi) }}</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="invalid-feedback"></div>

                    {{--<div class="js-zoom-not-complete-alert mt-10 text-danger d-none">
                        {{ trans('update.zoom_settings_are_not_complete_please_contact_with_admin') }}
                        --}}{{--<a href="/panel/setting/step/8" class="text-primary" target="_blank">{{ trans('public.go_to_settings') }}</a>--}}{{--
                    </div>--}}
                </div>


                <div class="row">
                    <div class="col-12 col-lg-6">

                        @if(!empty(getGeneralSettings('content_translate')))
                            <div class="form-group">
                                <label class="input-label">{{ trans('auth.language') }}</label>
                                <select name="ajax[{{ !empty($session) ? $session->id : 'new' }}][locale]"
                                        class="form-control {{ !empty($session) ? 'js-webinar-content-locale' : '' }}"
                                        data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}"
                                        data-id="{{ !empty($session) ? $session->id : '' }}"
                                        data-relation="sessions"
                                        data-fields="title,description"
                                >
                                    @foreach($userLanguages as $lang => $language)
                                        <option value="{{ $lang }}" {{ (!empty($session) and !empty($session->locale)) ? (mb_strtolower($session->locale) == mb_strtolower($lang) ? 'selected' : '') : ($locale == $lang ? 'selected' : '') }}>{{ $language }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][locale]" value="{{ $defaultLocale }}">
                        @endif

                        <div class="form-group js-api-secret {{ (!empty($session) and in_array($session->session_api, ['zoom', 'agora', 'jitsi'])) ? 'd-none' :'' }}">
                            <label class="input-label">{{ trans('auth.password') }}</label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][api_secret]" class="js-ajax-api_secret form-control" value="{{ !empty($session) ? $session->api_secret : '' }}" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group js-moderator-secret {{ (empty($session) or $session->session_api != 'big_blue_button') ? 'd-none' :'' }}">
                            <label class="input-label">{{ trans('public.moderator_password') }}</label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][moderator_secret]" class="js-ajax-moderator_secret form-control" value="{{ !empty($session) ? $session->moderator_secret : '' }}" {{ (!empty($session) and $session->session_api == 'big_blue_button') ? 'disabled' :'' }}/>
                            <div class="invalid-feedback"></div>
                        </div>

                        @if(!empty($session))
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.chapter') }}</label>
                                <select name="ajax[{{ !empty($session) ? $session->id : 'new' }}][chapter_id]" class="js-ajax-chapter_id form-control">
                                    @foreach($webinar->chapters as $ch)
                                        <option value="{{ $ch->id }}" {{ ($session->chapter_id == $ch->id) ? 'selected' : '' }}>{{ $ch->title }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        @else
                            <input type="hidden" name="ajax[new][chapter_id]" value="" class="chapter-input">
                        @endif

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.title') }}</label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][title]" class="js-ajax-title form-control" value="{{ !empty($session) ? $session->title : '' }}" placeholder="{{ trans('forms.maximum_255_characters') }}"/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.date') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="dateRangeLabel">
                                        <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                    </span>
                                </div>
                                <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][date]" class="js-ajax-date form-control datetimepicker" value="{{ !empty($session) ? dateTimeFormat($session->date, 'Y-m-d H:i', false, true, ($session->webinar ? $session->webinar->timezone : null)) : '' }}" aria-describedby="dateRangeLabel" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }} autocomplete="off"/>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.duration') }} <span class="braces">({{ trans('public.minutes') }})</span></label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][duration]" class="js-ajax-duration form-control" value="{{ !empty($session) ? $session->duration : '' }}" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group js-local-link {{ (!empty($session) and in_array($session->session_api, ['agora', 'jitsi'])) ? 'd-none' : '' }}">
                            <label class="input-label">{{ trans('public.link') }}</label>
                            <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][link]" class="js-ajax-link form-control" value="{{ !empty($session) ? $session->getJoinLink() : '' }}" {{ (!empty($session) and $session->session_api != 'local') ? 'disabled' :'' }}/>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">{{ trans('public.description') }}</label>
                            <textarea name="ajax[{{ !empty($session) ? $session->id : 'new' }}][description]" class="js-ajax-description form-control" rows="6">{{ !empty($session) ? $session->description : '' }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        @if(!empty(getFeaturesSettings('extra_time_to_join_status')) and getFeaturesSettings('extra_time_to_join_status'))
                            <div class="form-group">
                                <label class="input-label">{{ trans('update.extra_time_to_join') }} <span class="braces">({{ trans('public.minutes') }})</span></label>
                                <input type="text" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][extra_time_to_join]" value="{{ (!empty($session) and $session->extra_time_to_join) ? $session->extra_time_to_join : getFeaturesSettings('extra_time_to_join_default_value') }}" class="js-ajax-extra_time_to_join form-control" placeholder=""/>
                                <div class="invalid-feedback"></div>
                            </div>
                        @elseif(!empty(getFeaturesSettings('extra_time_to_join_default_value')))
                            <input type="hidden" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][extra_time_to_join]" value="{{ (!empty($session) and $session->extra_time_to_join) ? $session->extra_time_to_join : getFeaturesSettings('extra_time_to_join_default_value') }}" class="js-ajax-extra_time_to_join form-control" placeholder=""/>
                        @endif

                        <div class="form-group mt-20">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="cursor-pointer input-label" for="sessionStatusSwitch{{ !empty($session) ? $session->id : '_record' }}">{{ trans('public.active') }}</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][status]" class="custom-control-input" id="sessionStatusSwitch{{ !empty($session) ? $session->id : '_record' }}" {{ (empty($session) or $session->status == \App\Models\Session::$Active) ? 'checked' : ''  }}>
                                    <label class="custom-control-label" for="sessionStatusSwitch{{ !empty($session) ? $session->id : '_record' }}"></label>
                                </div>
                            </div>
                        </div>

                        <div class="js-agora-chat-and-rec  {{ (empty($session) or $session->session_api !== 'agora') ? 'd-none' : '' }}">
                            @if(getFeaturesSettings('agora_chat'))
                                <div class="form-group mt-20">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="cursor-pointer input-label" for="sessionAgoraChatSwitch{{ !empty($session) ? $session->id : '_record' }}">{{ trans('update.chat') }}</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][agora_chat]" class="custom-control-input" id="sessionAgoraChatSwitch{{ !empty($session) ? $session->id : '_record' }}" {{ (!empty($session) and !empty($session->agora_settings) and $session->agora_settings->chat) ? 'checked' : ''  }}>
                                            <label class="custom-control-label" for="sessionAgoraChatSwitch{{ !empty($session) ? $session->id : '_record' }}"></label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{--
                                                        <div class="form-group mt-20">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <label class="cursor-pointer input-label" for="sessionAgoraRecordSwitch{{ !empty($session) ? $session->id : '_record' }}">{{ trans('update.record') }}</label>
                                                                <div class="custom-control custom-switch">
                                                                    <input type="checkbox" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][agora_record]" class="custom-control-input" id="sessionAgoraRecordSwitch{{ !empty($session) ? $session->id : '_record' }}" {{ (!empty($session) and !empty($session->agora_settings) and $session->agora_settings->record) ? 'checked' : ''  }}>
                                                                    <label class="custom-control-label" for="sessionAgoraRecordSwitch{{ !empty($session) ? $session->id : '_record' }}"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                            --}}

                        </div>

                        @if(getFeaturesSettings('sequence_content_status'))
                            <div class="form-group mt-20">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="cursor-pointer input-label" for="SequenceContentSwitch{{ !empty($session) ? $session->id : '_record' }}">{{ trans('update.sequence_content') }}</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][sequence_content]" class="js-sequence-content-switch custom-control-input" id="SequenceContentSwitch{{ !empty($session) ? $session->id : '_record' }}" {{ (!empty($session) and ($session->check_previous_parts or !empty($session->access_after_day))) ? 'checked' : ''  }}>
                                        <label class="custom-control-label" for="SequenceContentSwitch{{ !empty($session) ? $session->id : '_record' }}"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="js-sequence-content-inputs pl-5 {{ (!empty($session) and ($session->check_previous_parts or !empty($session->access_after_day))) ? '' : 'd-none' }}">
                                <div class="form-group">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <label class="cursor-pointer input-label" for="checkPreviousPartsSwitch{{ !empty($session) ? $session->id : '_record' }}">{{ trans('update.check_previous_parts') }}</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][check_previous_parts]" class="custom-control-input" id="checkPreviousPartsSwitch{{ !empty($session) ? $session->id : '_record' }}" {{ (empty($session) or $session->check_previous_parts) ? 'checked' : ''  }}>
                                            <label class="custom-control-label" for="checkPreviousPartsSwitch{{ !empty($session) ? $session->id : '_record' }}"></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('update.access_after_day') }}</label>
                                    <input type="number" name="ajax[{{ !empty($session) ? $session->id : 'new' }}][access_after_day]" value="{{ (!empty($session)) ? $session->access_after_day : '' }}" class="js-ajax-access_after_day form-control" placeholder="{{ trans('update.access_after_day_placeholder') }}"/>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-30 d-flex align-items-center">
                    <button type="button" class="js-save-session btn btn-sm btn-primary">{{ trans('public.save') }}</button>

                    @if(!empty($session))
                        @if(!$session->isFinished())
                            <a href="{{ $session->getJoinLink(true) }}" target="_blank" class="ml-10 btn btn-sm btn-secondary">{{ trans('footer.join') }}</a>
                        @else
                            <button type="button" class="js-session-has-ended ml-10 btn btn-sm btn-secondary disabled">{{ trans('footer.join') }}</button>
                        @endif
                    @endif

                    @if(empty($session))
                        <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</li>
