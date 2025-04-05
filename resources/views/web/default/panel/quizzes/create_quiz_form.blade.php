<div class="">
    <div data-action="{{ !empty($quiz) ? ('/panel/quizzes/'. $quiz->id .'/update') : ('/panel/quizzes/store') }}" class="js-content-form quiz-form webinar-form">

        <section>
            <h2 class="section-title after-line">{{ !empty($quiz) ? (trans('public.edit').' ('. $quiz->title .')') : trans('quiz.new_quiz') }}</h2>

            <div class="row">
                <div class="col-12 col-md-4">

                    @if(!empty(getGeneralSettings('content_translate')))
                        <div class="form-group mt-25">
                            <label class="input-label">{{ trans('auth.language') }}</label>
                            <select name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][locale]"
                                    class="form-control {{ !empty($quiz) ? 'js-webinar-content-locale' : '' }}"
                                    data-webinar-id="{{ !empty($quiz) ? $quiz->webinar_id : '' }}"
                                    data-id="{{ !empty($quiz) ? $quiz->id : '' }}"
                                    data-relation="quizzes"
                                    data-fields="title"
                            >
                                @foreach($userLanguages as $lang => $language)
                                    <option value="{{ $lang }}" {{ (!empty($quiz) and !empty($quiz->locale)) ? (mb_strtolower($quiz->locale) == mb_strtolower($lang) ? 'selected' : '') : ($locale == $lang ? 'selected' : '') }}>{{ $language }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][locale]" value="{{ $defaultLocale }}">
                    @endif

                    @if(empty($selectedWebinar))
                        <div class="form-group mt-25">
                            <label class="input-label">{{ trans('panel.webinar') }}</label>
                            <select name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][webinar_id]" class="js-ajax-webinar_id custom-select">
                            <option>Paid Plugin</option>
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][webinar_id]" value="{{ $selectedWebinar->id }}">
                    @endif

                    @if(!empty($quiz))
                        <div class="form-group">
                            <label class="input-label">{{ trans('public.chapter') }}</label>
                            <select name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][chapter_id]" class="js-ajax-chapter_id form-control">
                                @foreach($chapters as $ch)
                                    <option value="{{ $ch->id }}" {{ ($quiz->chapter_id == $ch->id) ? 'selected' : '' }}>{{ $ch->title }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    @else
                        <input type="hidden" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][chapter_id]" value="" class="chapter-input">
                    @endif

                    <div class="form-group @if(!empty($selectedWebinar)) mt-25 @endif">
                        <label class="input-label">{{ trans('quiz.quiz_title') }}</label>
                        <input type="text" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][title]" value="{{ !empty($quiz) ? $quiz->title : old('title') }}"  class="js-ajax-title form-control" placeholder=""/>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">{{ trans('public.time') }} <span class="braces">({{ trans('public.minutes') }})</span></label>
                        <input type="number" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][time]" value="{{ !empty($quiz) ? $quiz->time : old('time') }}"  class="js-ajax-time form-control" placeholder="{{ trans('forms.empty_means_unlimited') }}" min="0"/>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">{{ trans('quiz.number_of_attemps') }}</label>
                        <input type="number" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][attempt]" value="{{ !empty($quiz) ? $quiz->attempt : old('attempt') }}" class="js-ajax-attempt form-control " placeholder="{{ trans('forms.empty_means_unlimited') }}" min="0"/>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">{{ trans('quiz.pass_mark') }}</label>
                        <input type="number" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][pass_mark]" value="{{ !empty($quiz) ? $quiz->pass_mark : old('pass_mark') }}" class="js-ajax-pass_mark form-control " min="0"/>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">{{ trans('update.expiry_days') }}</label>
                        <input type="number" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][expiry_days]" value="{{ !empty($quiz) ? $quiz->expiry_days : old('expiry_days') }}" class="js-ajax-expiry_days form-control" min="0"/>
                        <div class="invalid-feedback"></div>

                        <p class="font-12 text-gray mt-5">{{ trans('update.quiz_expiry_days_hint') }}</p>
                    </div>

                    @if(!empty($quiz))
                        <div class="form-group mt-20 d-flex align-items-center justify-content-between">
                            <label class="cursor-pointer input-label" for="displayLimitedQuestionsSwitch{{ $quiz->id }}">{{ trans('update.display_limited_questions') }}</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][display_limited_questions]" class="js-ajax-display_limited_questions custom-control-input" id="displayLimitedQuestionsSwitch{{ $quiz->id }}" {{ ($quiz->display_limited_questions) ? 'checked' : ''}}>
                                <label class="custom-control-label" for="displayLimitedQuestionsSwitch{{ $quiz->id }}"></label>
                            </div>
                        </div>

                        <div class="form-group js-display-limited-questions-count-field {{ ($quiz->display_limited_questions) ? '' : 'd-none' }}">
                            <label class="input-label">{{ trans('update.number_of_questions') }} ({{ trans('update.total_questions') }}: {{ $quiz->quizQuestions->count() }})</label>
                            <input type="number" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][display_number_of_questions]" value="{{ $quiz->display_number_of_questions }}" class="js-ajax-display_number_of_questions form-control " min="1"/>
                            <div class="invalid-feedback"></div>
                        </div>
                    @endif

                    <div class="form-group mt-20 d-flex align-items-center justify-content-between">
                        <label class="cursor-pointer input-label" for="displayQuestionsRandomlySwitch{{ !empty($quiz) ? $quiz->id : 'record' }}">{{ trans('update.display_questions_randomly') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][display_questions_randomly]" class="js-ajax-display_questions_randomly custom-control-input" id="displayQuestionsRandomlySwitch{{ !empty($quiz) ? $quiz->id : 'record' }}" {{ (!empty($quiz) && $quiz->display_questions_randomly) ? 'checked' : ''}}>
                            <label class="custom-control-label" for="displayQuestionsRandomlySwitch{{ !empty($quiz) ? $quiz->id : 'record' }}"></label>
                        </div>
                    </div>

                    <div class="form-group mt-20 d-flex align-items-center justify-content-between">
                        <label class="cursor-pointer input-label" for="certificateSwitch{{ !empty($quiz) ? $quiz->id : 'record' }}">{{ trans('quiz.certificate_included') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][certificate]" class="js-ajax-certificate custom-control-input" id="certificateSwitch{{ !empty($quiz) ? $quiz->id : 'record' }}" {{ (!empty($quiz) && $quiz->certificate) ? 'checked' : ''}}>
                            <label class="custom-control-label" for="certificateSwitch{{ !empty($quiz) ? $quiz->id : 'record' }}"></label>
                        </div>
                    </div>

                    <div class="form-group mt-20 d-flex align-items-center justify-content-between">
                        <label class="cursor-pointer input-label" for="statusSwitch{{ !empty($quiz) ? $quiz->id : 'record' }}">{{ trans('quiz.active_quiz') }}</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][status]" class="js-ajax-status custom-control-input" id="statusSwitch{{ !empty($quiz) ? $quiz->id : 'record' }}" {{ (!empty($quiz) && $quiz->status == 'active') ? 'checked' : ''}}>
                            <label class="custom-control-label" for="statusSwitch{{ !empty($quiz) ? $quiz->id : 'record' }}"></label>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        @if(!empty($quiz))
            <section class="mt-30">
                <div class="d-block d-md-flex justify-content-between align-items-center pb-20">
                    <h2 class="section-title after-line">{{ trans('public.questions') }}</h2>

                    <div class="d-flex align-items-center mt-20 mt-md-0">
                        <button id="add_multiple_question" data-quiz-id="{{ $quiz->id }}" type="button" class="quiz-form-btn btn btn-primary btn-sm ml-10">{{ trans('quiz.add_multiple_choice') }}</button>
                        <button id="add_descriptive_question" data-quiz-id="{{ $quiz->id }}" type="button" class="quiz-form-btn btn btn-primary btn-sm ml-10">{{ trans('quiz.add_descriptive') }}</button>
                    </div>
                </div>

                @if($quizQuestions)
                    <ul class="draggable-questions-lists draggable-questions-lists-{{ $quiz->id }}" data-drag-class="draggable-questions-lists-{{ $quiz->id }}" data-order-table="quizzes_questions" data-quiz="{{ $quiz->id }}">
                        @foreach($quizQuestions as $question)
                            <li data-id="{{ $question->id }}" class="quiz-question-card d-flex align-items-center mt-20">
                                <div class="flex-grow-1">
                                    <h4 class="question-title">{{ $question->title }}</h4>
                                    <div class="font-12 mt-5 question-infos">
                                        <span>{{ $question->type === App\Models\QuizzesQuestion::$multiple ? trans('quiz.multiple_choice') : trans('quiz.descriptive') }} | {{ trans('quiz.grade') }}: {{ $question->grade }}</span>
                                    </div>
                                </div>

                                <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>

                                <div class="btn-group dropdown table-actions">
                                    <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i data-feather="more-vertical" height="20"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" data-question-id="{{ $question->id }}" class="edit_question btn btn-sm btn-transparent d-block">{{ trans('public.edit') }}</button>
                                        <a href="/panel/quizzes-questions/{{ $question->id }}/delete" class="delete-action btn btn-sm btn-transparent d-block">{{ trans('public.delete') }}</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endif

        <input type="hidden" name="ajax[{{ !empty($quiz) ? $quiz->id : 'new' }}][is_webinar_page]" value="@if(!empty($inWebinarPage) and $inWebinarPage) 1 @else 0 @endif">

        <div class="mt-20 mb-20">
            <button type="button" class="js-submit-quiz-form btn btn-sm btn-primary">{{ !empty($quiz) ? trans('public.save_change') : trans('public.create') }}</button>

            @if(empty($quiz) and !empty($inWebinarPage))
                <button type="button" class="btn btn-sm btn-danger ml-10 cancel-accordion">{{ trans('public.close') }}</button>
            @endif
        </div>
    </div>

    <!-- Modal -->
@if(!empty($quiz))
    @include(getTemplate() .'.panel.quizzes.modals.multiple_question',['quiz' => $quiz])
    @include(getTemplate() .'.panel.quizzes.modals.descriptive_question',['quiz' => $quiz])
@endif
