<div class="row">
    <div class="col-12">
        <div class="accordion-content-wrapper" id="certificateAccordion" role="tablist" aria-multiselectable="true">
            @foreach($quizzes as $quiz)
                @if(!empty($quiz->certificate))
                    <div class="accordion-row rounded-sm border mt-20 p-15">
                        <div class="d-flex align-items-center justify-content-between" role="tab" id="quizCertificate_{{ $quiz->id }}">

                            <div class="d-flex align-items-center" href="#collapseQuizCertificate{{ $quiz->id }}" aria-controls="collapseQuizCertificate{{ $quiz->id }}" data-parent="#certificateAccordion" role="button" data-toggle="collapse" aria-expanded="true">
                                    <span class="chapter-icon chapter-content-icon mr-15">
                                        <i data-feather="award" width="20" height="20" class="text-gray"></i>
                                    </span>

                                <span class="font-weight-bold font-14 text-secondary d-block">{{ $quiz->title }}</span>
                            </div>

                            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseQuizCertificate{{ !empty($quiz) ? $quiz->id :'record' }}" aria-controls="collapseQuizCertificate{{ !empty($quiz) ? $quiz->id :'record' }}" data-parent="#certificateAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
                        </div>

                        <div id="collapseQuizCertificate{{ $quiz->id }}" aria-labelledby="quizCertificate_{{ $quiz->id }}" class=" collapse" role="tabpanel">
                            <div class="panel-collapse">
                                <div class="d-flex align-items-center justify-content-between mt-20">
                                    <div class="d-flex align-items-center">
                                        @if(!empty($quiz->result))
                                            <div class="d-flex align-items-center text-gray text-center font-14 mr-20">
                                                <i data-feather="calendar" width="18" height="18" class="text-gray mr-5"></i>
                                                <span class="line-height-1">{{ dateTimeFormat($quiz->result->created_at, 'j M Y') }}</span>
                                            </div>
                                        @endif

                                        <div class="d-flex align-items-center text-gray text-center font-14 mr-20">
                                            <i data-feather="check-square" width="18" height="18" class="text-gray mr-5"></i>
                                            <span class="line-height-1">{{ trans('update.passed_grade') }}: {{ $quiz->pass_mark }}/{{ $quiz->quizQuestions->sum('grade') }}</span>
                                        </div>
                                    </div>
                                    <div class="">
                                        @if(!empty($user) and $quiz->can_download_certificate and $hasBought)
                                            <a href="/panel/quizzes/results/{{ $quiz->result->id }}/showCertificate" target="_blank" class="course-content-btns btn btn-sm btn-primary">{{ trans('home.download') }}</a>
                                        @else
                                            <button type="button" class="course-content-btns btn btn-sm btn-gray disabled {{ ((empty($user)) ? 'not-login-toast' : (!$hasBought ? 'not-access-toast' : (!$quiz->can_download_certificate ? 'can-not-download-certificate-toast' : ''))) }}">
                                                {{ trans('home.download') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
