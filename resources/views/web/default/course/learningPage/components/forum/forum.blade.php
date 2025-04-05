<section class="p-15 m-15 border rounded-lg">
    <div class="course-forum-top-stats d-flex flex-wrap flex-md-nowrap align-items-center justify-content-around">
        <div class="d-flex align-items-center justify-content-center pb-5 pb-md-0">
            <div class="d-flex flex-column align-items-center text-center">
                <img src="/assets/default/img/activity/47.svg" class="course-forum-top-stats__icon" alt="">
                <strong class="font-20 text-dark-blue font-weight-bold mt-5">{{ $questionsCount }}</strong>
                <span class="font-14 text-gray font-weight-500">{{ trans('public.questions') }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center pb-5 pb-md-0">
            <div class="d-flex flex-column align-items-center text-center">
                <img src="/assets/default/img/activity/120.svg" class="course-forum-top-stats__icon" alt="">
                <strong class="font-20 text-dark-blue font-weight-bold mt-5">{{ $resolvedCount }}</strong>
                <span class="font-14 text-gray font-weight-500">{{ trans('update.resolved') }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center pb-5 pb-md-0">
            <div class="d-flex flex-column align-items-center text-center">
                <img src="/assets/default/img/activity/119.svg" class="course-forum-top-stats__icon" alt="">
                <strong class="font-20 text-dark-blue font-weight-bold mt-5">{{ $openQuestionsCount }}</strong>
                <span class="font-14 text-gray font-weight-500">{{ trans('update.open_questions') }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center pb-5 pb-md-0">
            <div class="d-flex flex-column align-items-center text-center">
                <img src="/assets/default/img/activity/39.svg" class="course-forum-top-stats__icon" alt="">
                <strong class="font-20 text-dark-blue font-weight-bold mt-5">{{ $commentsCount }}</strong>
                <span class="font-14 text-gray font-weight-500">{{ trans('update.answers') }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center pb-5 pb-md-0">
            <div class="d-flex flex-column align-items-center text-center">
                <img src="/assets/default/img/activity/49.svg" class="course-forum-top-stats__icon" alt="">
                <strong class="font-20 text-dark-blue font-weight-bold mt-5">{{ $activeUsersCount }}</strong>
                <span class="font-14 text-gray font-weight-500">{{ trans('update.active_users') }}</span>
            </div>
        </div>
    </div>

    <div class="container-fluid p-15 rounded-lg bg-info-light font-14 text-gray mt-20">
        <div class="row align-items-center">
            <div class="col-12 col-lg-4">
                <div class="">
                    <h3 class="font-16 font-weight-bold text-dark-blue">{{ trans('update.course_forum') }}</h3>
                    <span class="d-block font-14 font-weight-500 text-gray mt-1">{{ trans('update.communicate_others_and_ask_your_questions') }}</span>
                </div>
            </div>
            <div class="col-12 col-lg-5 mt-15 mt-lg-0">
                <form action="{{ request()->url() }}" method="get">
                    <div class="d-flex align-items-center">
                        <input type="text" name="search" class="form-control flex-grow-1" value="{{ request()->get('search') }}" placeholder="{{ trans('update.search_in_this_forum') }}">
                        <button type="submit" class="btn btn-primary btn-sm ml-10 course-forum-search-btn">
                            <i data-feather="search" class="text-white" width="16" height="16"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-12 col-lg-3 mt-15 mt-lg-0 text-right">
                <button type="button" id="askNewQuestion" class="btn btn-primary btn-sm course-forum-search-btn">
                    <i data-feather="file" class="text-white" width="16" height="16"></i>
                    <span class="ml-1">{{ trans('update.ask_new_question') }}</span>
                </button>
            </div>
        </div>
    </div>
</section>

@if($forums and count($forums))
    @foreach($forums as $forum)
        <div class="course-forum-question-card p-15 m-15 border rounded-lg">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="d-flex align-items-start">
                        <div class="question-user-avatar">
                            <img src="{{ $forum->user->getAvatar(64) }}" class="img-cover rounded-circle" alt="{{ $forum->user->full_name }}">
                        </div>
                        <div class="ml-10">
                            <a href="{{ $course->getForumPageUrl() }}/{{ $forum->id }}/answers" class="">
                                <h4 class="font-16 font-weight-bold text-dark-blue">{{ $forum->title }}</h4>
                            </a>

                            <span class="d-block font-12 text-gray mt-5">{{ trans('public.by') }} {{ $forum->user->full_name }} {{ trans('public.in') }} {{ dateTimeFormat($forum->created_at, 'j M Y | H:i') }}</span>

                            <p class="d-block font-14 text-gray mt-10 white-space-pre-wrap">{{ $forum->description }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mt-15 mt-lg-0 border-left">
                    @if($course->isOwner($user->id))
                        <button type="button" data-action="{{ $course->getForumPageUrl() }}/{{ $forum->id }}/pinToggle" class="question-forum-pin-btn d-flex align-items-center justify-content-center">
                            <img src="/assets/default/img/learning/{{ $forum->pin ? 'un_pin' : 'pin' }}.svg" alt="pin icon" class="">
                        </button>
                    @endif


                    @if(!empty($forum->answers) and count($forum->answers))
                        <div class="py-15 row">
                            <div class="col-3">
                                <span class="d-block font-12 text-gray">{{ trans('public.answers') }}</span>
                                <span class="d-block font-14 text-dark mt-10">{{ $forum->answer_count }}</span>
                            </div>

                            <div class="col-3">
                                <span class="d-block font-12 text-gray">{{ trans('panel.users') }}</span>
                                <div class="answers-user-icons d-flex align-items-center">
                                    @if(!empty($forum->usersAvatars))
                                        @foreach($forum->usersAvatars as $userAvatar)
                                            <div class="user-avatar-card rounded-circle">
                                                <img src="{{ $userAvatar->getAvatar(32) }}" class="img-cover rounded-circle" alt="{{ $userAvatar->full_name }}">
                                            </div>
                                        @endforeach
                                    @endif

                                    @if(($forum->answers->groupBy('user_id')->count() - count($forum->usersAvatars)) > 0)
                                        <span class="answer-count d-flex align-items-center justify-content-center font-12 text-gray rounded-circle">+{{ $forum->answer_count - count($forum->usersAvatars) }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-6 position-relative">
                                <span class="d-block font-12 text-gray">{{ trans('update.last_activity') }}</span>
                                <span class="d-block font-14 text-dark mt-10">{{ dateTimeFormat($forum->lastAnswer->created_at,'j M Y | H:i') }}</span>
                            </div>
                        </div>

                        <div class="py-15 border-top position-relative">
                            <span class="d-block font-12 text-gray text-left">{{ trans('update.last_answer') }}</span>

                            <div class="d-flex align-items-start mt-20">
                                <div class="last-answer-user-avatar">
                                    <img src="{{ $forum->lastAnswer->user->getAvatar(30) }}" class="img-cover rounded-circle" alt="{{ $forum->lastAnswer->user->full_name }}">
                                </div>
                                <div class="ml-10">
                                    <h4 class="font-14 text-dark font-weight-bold">{{ $forum->lastAnswer->user->full_name }}</h4>
                                    <p class="font-12 font-weight-500 text-gray mt-5">{!! truncate($forum->lastAnswer->description, 160) !!}</p>
                                </div>
                            </div>

                            @if(!empty($forum->resolved))
                                <div class="resolved-answer-badge d-flex align-items-center font-12 text-primary">
                                    <span class="badge-icon d-flex align-items-center justify-content-center">
                                        <i data-feather="check" width="20" height="20"></i>
                                    </span>
                                    {{ trans('update.resolved') }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="d-flex flex-column justify-content-center text-center py-15 h-100">
                            <p class="text-gray font-14 font-weight-bold">{{ trans('update.be_the_first_to_answer_this_question') }}</p>

                            <div class="">
                                <a href="{{ $course->getForumPageUrl() }}/{{ $forum->id }}/answers" class="btn btn-primary btn-sm mt-15">{{ trans('public.answer') }}</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="learning-page-forum-empty d-flex align-items-center justify-content-center flex-column">
        <div class="learning-page-forum-empty-icon d-flex align-items-center justify-content-center">
            <img src="/assets/default/img/learning/forum-empty.svg" class="img-fluid" alt="">
        </div>

        <div class="d-flex align-items-center flex-column mt-10 text-center">
            <h3 class="font-20 font-weight-bold text-dark-blue text-center"></h3>
            <p class="font-14 font-weight-500 text-gray mt-5 text-center">{{ trans('update.learning_page_empty_content_title_hint') }}</p>
        </div>
    </div>
@endif

@include('web.default.course.learningPage.components.forum.ask_question_modal')
