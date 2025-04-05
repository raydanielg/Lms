<div class="row">
    <div class="col-12">
        <div class="accordion-content-wrapper" id="chaptersAccordion" role="tablist" aria-multiselectable="true">
            @foreach($course->chapters as $chapter)

                @if((!empty($chapter->chapterItems) and count($chapter->chapterItems)) or (!empty($chapter->quizzes) and count($chapter->quizzes)))
                    <div class="accordion-row rounded-sm border mt-20 p-15">
                        <div class="d-flex align-items-center justify-content-between" role="tab" id="chapter_{{ $chapter->id }}">
                            <div class="js-chapter-collapse-toggle d-flex align-items-center" href="#collapseChapter{{ $chapter->id }}" aria-controls="collapseChapter{{ $chapter->id }}" data-parent="#chaptersAccordion" role="button" data-toggle="collapse" aria-expanded="true">
                                <span class="chapter-icon mr-15">
                                    <i data-feather="grid" class=""></i>
                                </span>

                                <span class="font-weight-bold text-secondary font-14">{{ $chapter->title }}</span>
                            </div>

                            <div class="d-flex align-items-center">
                                <span class="mr-15 font-14 text-gray">
                                    {{ $chapter->getTopicsCount(true) }} {{ trans('public.parts') }}
                                    {{ !empty($chapter->getDuration()) ? ' - ' . convertMinutesToHourAndMinute($chapter->getDuration()) .' '. trans('public.hr') : '' }}
                                </span>

                                <i class="collapse-chevron-icon" data-feather="chevron-down" height="20" href="#collapseChapter{{ !empty($chapter) ? $chapter->id :'record' }}" aria-controls="collapseChapter{{ !empty($chapter) ? $chapter->id :'record' }}" data-parent="#chaptersAccordion" role="button" data-toggle="collapse" aria-expanded="true"></i>
                            </div>
                        </div>

                        <div id="collapseChapter{{ $chapter->id }}" aria-labelledby="chapter_{{ $chapter->id }}" class=" collapse" role="tabpanel">
                            <div class="panel-collapse">
                                @if(!empty($chapter->chapterItems) and count($chapter->chapterItems))
                                    @foreach($chapter->chapterItems as $chapterItem)
                                        @if($chapterItem->type == \App\Models\WebinarChapterItem::$chapterSession and !empty($chapterItem->session) and $chapterItem->session->status == 'active')
                                            @include('web.default.course.tabs.contents.sessions' , ['session' => $chapterItem->session, 'accordionParent' => 'chaptersAccordion'])
                                        @elseif($chapterItem->type == \App\Models\WebinarChapterItem::$chapterFile and !empty($chapterItem->file) and $chapterItem->file->status == 'active')
                                            @include('web.default.course.tabs.contents.files' , ['file' => $chapterItem->file, 'accordionParent' => 'chaptersAccordion'])
                                        @elseif($chapterItem->type == \App\Models\WebinarChapterItem::$chapterTextLesson and !empty($chapterItem->textLesson) and $chapterItem->textLesson->status == 'active')
                                            @include('web.default.course.tabs.contents.text_lessons' , ['textLesson' => $chapterItem->textLesson, 'accordionParent' => 'chaptersAccordion'])
                                        @elseif($chapterItem->type == \App\Models\WebinarChapterItem::$chapterAssignment and !empty($chapterItem->assignment) and $chapterItem->assignment->status == 'active')
                                            @include('web.default.course.tabs.contents.assignment' ,['assignment' => $chapterItem->assignment, 'accordionParent' => 'chaptersAccordion'])
                                        @elseif($chapterItem->type == \App\Models\WebinarChapterItem::$chapterQuiz and !empty($chapterItem->quiz) and $chapterItem->quiz->status == 'active')
                                            @include('web.default.course.tabs.contents.quiz' ,['quiz' => $chapterItem->quiz, 'accordionParent' => 'chaptersAccordion'])
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
