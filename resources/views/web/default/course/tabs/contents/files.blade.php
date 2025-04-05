@php
    $checkSequenceContent = $file->checkSequenceContent();
    $sequenceContentHasError = (!empty($checkSequenceContent) and (!empty($checkSequenceContent['all_passed_items_error']) or !empty($checkSequenceContent['access_after_day_error'])));
@endphp

<div class="accordion-row rounded-sm border mt-15 p-15">
    <div class="d-flex align-items-center justify-content-between" role="tab" id="files_{{ $file->id }}">
        <div class="d-flex align-items-center" href="#collapseFiles{{ $file->id }}" aria-controls="collapseFiles{{ $file->id }}" data-parent="#{{ $accordionParent }}" role="button" data-toggle="collapse" aria-expanded="true">

            <span class="d-flex align-items-center justify-content-center mr-15">
                <span class="chapter-icon chapter-content-icon">
                <i data-feather="{{ $file->getIconByType() }}" width="20" height="20" class="text-gray"></i>
                </span>
            </span>

            <span class="font-weight-bold text-secondary font-14 file-title">{{ $file->title }}</span>
        </div>

        <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseFiles{{ !empty($file) ? $file->id :'record' }}" aria-controls="collapseFiles{{ !empty($file) ? $file->id :'record' }}" data-parent="#{{ $accordionParent }}" role="button" data-toggle="collapse" aria-expanded="true"></i>
    </div>

    <div id="collapseFiles{{ $file->id }}" aria-labelledby="files_{{ $file->id }}" class=" collapse" role="tabpanel">
        <div class="panel-collapse">
            <div class="text-gray text-14">
                {!! nl2br(clean($file->description)) !!}
            </div>

            @if(!empty($user) and $hasBought)
                <div class="d-flex align-items-center mt-20">
                    <label class="mb-0 mr-10 cursor-pointer font-weight-500" for="fileReadToggle{{ $file->id }}">{{ trans('public.i_passed_this_lesson') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" @if($sequenceContentHasError) disabled @endif id="fileReadToggle{{ $file->id }}" data-file-id="{{ $file->id }}" value="{{ $course->id }}" class="js-file-learning-toggle custom-control-input" @if(!empty($file->checkPassedItem())) checked @endif>
                        <label class="custom-control-label" for="fileReadToggle{{ $file->id }}"></label>
                    </div>
                </div>
            @endif

            <div class="d-flex align-items-center justify-content-between mt-20">

                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center text-gray text-center font-14 mr-20">
                        <i data-feather="download-cloud" width="18" height="18" class="text-gray mr-5"></i>
                        <span class="line-height-1">{{ ($file->volume > 0) ? $file->getVolume() : '-' }}</span>
                    </div>
                </div>

                <div class="">
                    @if(!empty($checkSequenceContent) and $sequenceContentHasError)
                        <button
                            type="button"
                            class="course-content-btns btn btn-sm btn-gray flex-grow-1 disabled js-sequence-content-error-modal"
                            data-passed-error="{{ !empty($checkSequenceContent['all_passed_items_error']) ? $checkSequenceContent['all_passed_items_error'] : '' }}"
                            data-access-days-error="{{ !empty($checkSequenceContent['access_after_day_error']) ? $checkSequenceContent['access_after_day_error'] : '' }}"
                        >{{ trans('public.play') }}</button>
                    @elseif($file->accessibility == 'paid')
                        @if(!empty($user) and $hasBought)
                            @if($file->downloadable)
                                <a href="{{ $course->getUrl() }}/file/{{ $file->id }}/download" class="course-content-btns btn btn-sm btn-primary">
                                    {{ trans('home.download') }}
                                </a>
                            @else
                                <a href="{{ $course->getLearningPageUrl() }}?type=file&item={{ $file->id }}" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                                    {{ trans('public.play') }}
                                </a>
                            @endif
                        @else
                            <button type="button" class="course-content-btns btn btn-sm btn-gray disabled {{ ((empty($user)) ? 'not-login-toast' : (!$hasBought ? 'not-access-toast' : '')) }}">
                                @if($file->downloadable)
                                    {{ trans('home.download') }}
                                @else
                                    {{ trans('public.play') }}
                                @endif
                            </button>
                        @endif
                    @else
                        @if($file->downloadable)
                            <a href="{{ $course->getUrl() }}/file/{{ $file->id }}/download" class="course-content-btns btn btn-sm btn-primary">
                                {{ trans('home.download') }}
                            </a>
                        @else
                            @if(!empty($user) and $hasBought)
                                <a href="{{ $course->getLearningPageUrl() }}?type=file&item={{ $file->id }}" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                                    {{ trans('public.play') }}
                                </a>
                            @elseif($file->storage == 'upload_archive')
                                <a href="/course/{{ $course->slug }}/file/{{ $file->id }}/showHtml" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                                    {{ trans('public.play') }}
                                </a>
                            @elseif(in_array($file->storage, ['iframe', 'google_drive', 'dropbox']))
                                <a href="/course/{{ $course->slug }}/file/{{ $file->id }}/play" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                                    {{ trans('public.play') }}
                                </a>
                            @elseif($file->isVideo())
                                <button type="button" data-id="{{ $file->id }}" data-title="{{ $file->title }}" class="js-play-video course-content-btns btn btn-sm btn-primary">
                                    {{ trans('public.play') }}
                                </button>
                            @else
                                <a href="{{ $file->file }}" target="_blank" class="course-content-btns btn btn-sm btn-primary">
                                    {{ trans('public.play') }}
                                </a>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
