<li data-id="{{ !empty($chapterItem) ? $chapterItem->id :'' }}" class="accordion-row bg-white rounded-sm border border-gray300 mt-20 py-15 py-lg-30 px-10 px-lg-20">
    <div class="d-flex align-items-center justify-content-between " role="tab" id="quiz_{{ !empty($quizInfo) ? $quizInfo->id :'record' }}">
        <div class="d-flex align-items-center" href="#collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" aria-controls="collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" data-parent="#{{ !empty($chapter) ? 'chapterContentAccordion'.$chapter->id : 'quizzesAccordion' }}" role="button" data-toggle="collapse" aria-expanded="true">
            <span class="chapter-icon chapter-content-icon mr-10">
                <i data-feather="award" class=""></i>
            </span>

            <span class="font-weight-bold text-dark-blue d-block cursor-pointer">{{ !empty($quizInfo) ? $quizInfo->title : trans('public.add_new_quizzes') }}</span>
        </div>

        <div class="d-flex align-items-center">

            @if(!empty($quizInfo) and $quizInfo->status != \App\Models\WebinarChapter::$chapterActive)
                <span class="disabled-content-badge mr-10">{{ trans('public.disabled') }}</span>
            @endif

            @if(!empty($quizInfo) and !empty($chapterItem))
                <button type="button" data-item-id="{{ $quizInfo->id }}" data-item-type="{{ \App\Models\WebinarChapterItem::$chapterQuiz }}" data-chapter-id="{{ !empty($chapter) ? $chapter->id : '' }}" class="js-change-content-chapter btn btn-sm btn-transparent text-gray mr-10">
                    <i data-feather="grid" class="" height="20"></i>
                </button>
            @endif

            @if(!empty($chapter))
                <i data-feather="move" class="move-icon mr-10 cursor-pointer" height="20"></i>
            @endif

            @if(!empty($quizInfo))
                <a href="{{ getAdminPanelUrl() }}/quizzes/{{ $quizInfo->id }}/delete" class="delete-action btn btn-sm btn-transparent text-gray">
                    <i data-feather="trash-2" class="mr-10 cursor-pointer" height="20"></i>
                </a>
            @endif

            <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" aria-controls="collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" data-parent="#quizzesAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
        </div>
    </div>

    <div id="collapseQuiz{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" aria-labelledby="quiz_{{ !empty($quizInfo) ? $quizInfo->id :'record' }}" class=" collapse @if(empty($quizInfo)) show @endif" role="tabpanel">
        <div class="panel-collapse text-gray">
            @include('admin.quizzes.create_quiz_form',
                    [
                        'inWebinarPage' => true,
                        'selectedWebinar' => $webinar,
                        'quiz' => $quizInfo ?? null,
                        'quizQuestions' => !empty($quizInfo) ? $quizInfo->quizQuestions : [],
                        'chapters' => $webinar->chapters,
                        'webinarChapterPages' => !empty($webinarChapterPages),
                        'creator' => $webinar->creator
                    ]
                )
        </div>
    </div>
</li>
