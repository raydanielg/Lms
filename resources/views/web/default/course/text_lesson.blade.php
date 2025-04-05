@extends(getTemplate().'.layouts.app')

@section('content')
    <section class="cart-banner position-relative text-center">
        <div class="container h-100">
            <div class="row h-100 align-items-center justify-content-center text-center">
                <div class="col-12 col-md-9 col-lg-7">

                    <h1 class="font-30 text-white font-weight-bold">{{ $textLesson->title }}</h1>

                    <div class="mt-20 font-16 font-weight-500 text-white">
                        <span>{{ trans('public.lesson') }} {{ $textLesson->order }}/{{ count($course->textLessons) }} </span> | <span>{{ trans('public.study_time') }}: {{ $textLesson->study_time }} {{ trans('public.min') }}</span>
                    </div>

                    <div class="mt-20 font-16 font-weight-500 text-white">
                        <span>{{ trans('product.course') }}: <a href="{{ $course->getUrl() }}" class="font-16 font-weight-500 text-white text-decoration-underline">{{ $course->title }}</a></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mt-10 mt-md-40">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="post-show mt-30">

                    @if(!empty($textLesson->image))
                        <div class="post-img pb-30">
                            <img src="{{ url($textLesson->image) }}" alt="{{ $textLesson->title }}"/>
                        </div>
                    @endif

                    {!! nl2br($textLesson->content) !!}
                </div>


                <div class="mt-30 row align-items-center">
                    <div class="col-12 col-md-5">
                        @if(auth()->check())
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="cursor-pointer font-weight-500" for="readLessonSwitch">{{ trans('public.i_passed_this_lesson') }}</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="read" class="custom-control-input" id="readLessonSwitch" data-course-id="{{ $course->id }}" data-lesson-id="{{ $textLesson->id }}" {{ !empty($textLesson->checkPassedItem()) ? 'checked' : ''  }}>
                                    <label class="custom-control-label" for="readLessonSwitch"></label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-12 col-md-7 text-right">
                        @if(!empty($course->textLessons) and count($course->textLessons))
                            <a href="{{ (!empty($previousLesson)) ? $course->getUrl() .'/lessons/'. $previousLesson->id .'/read' : '#' }}" class="btn btn-sm {{ (!empty($previousLesson)) ? 'btn-primary' : 'btn-gray disabled' }}">{{ trans('public.previous_lesson') }}</a>

                            @if(!empty($nextLesson))
                                <a href="{{ (!$nextLesson->not_purchased) ? $course->getUrl() .'/lessons/'. $nextLesson->id .'/read' : '#' }}" class="btn btn-sm {{ (!$nextLesson->not_purchased) ? 'btn-primary' : 'btn-gray disabled' }} {{ ($nextLesson->not_purchased) ? 'js-not-purchased' : '' }}">{{ trans('public.next_lesson') }}</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">

                <div class="rounded-lg shadow-sm mt-35 p-20 course-teacher-card d-flex align-items-center flex-column">
                    <div class="teacher-avatar mt-5">
                        <img src="{{ $course->teacher->getAvatar(100) }}" class="img-cover" alt="{{ $course->teacher->full_name }}">
                    </div>
                    <h3 class="mt-10 font-20 font-weight-bold text-secondary">{{ $course->teacher->full_name }}</h3>
                    <span class="mt-5 font-weight-500 text-gray">{{ trans('product.product_designer') }}</span>

                    @include('web.default.includes.webinar.rate',['rate' => $course->teacher->rates()])

                    <div class="user-reward-badges d-flex flex-wrap align-items-center mt-20">
                        @foreach($course->teacher->getBadges() as $userBadge)
                            <div class="mr-15 mt-10" data-toggle="tooltip" data-placement="bottom" data-html="true" title="{!! (!empty($userBadge->badge_id) ? nl2br($userBadge->badge->description) : nl2br($userBadge->description)) !!}">
                                <img src="{{ !empty($userBadge->badge_id) ? $userBadge->badge->image : $userBadge->image }}" width="32" height="32" alt="{{ !empty($userBadge->badge_id) ? $userBadge->badge->title : $userBadge->title }}">
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-25 d-flex flex-row align-items-center justify-content-center w-100">
                        <a href="{{ $course->teacher->getProfileUrl() }}" target="_blank" class="btn btn-sm btn-primary teacher-btn-action">{{ trans('public.profile') }}</a>

                        @if(!empty($course->teacher->hasMeeting()))
                            <a href="{{ $course->teacher->getProfileUrl() }}" class="btn btn-sm btn-primary teacher-btn-action ml-15">{{ trans('public.book_a_meeting') }}</a>
                        @else
                            <button type="button" class="btn btn-sm btn-primary disabled teacher-btn-action ml-15">{{ trans('public.book_a_meeting') }}</button>
                        @endif
                    </div>
                </div>

                @if(!empty($textLesson->attachments) and count($textLesson->attachments))
                    <div class="shadow-sm rounded-lg bg-white px-15 px-md-25 py-20 mt-30">
                        <h3 class="category-filter-title font-16 font-weight-bold text-dark-blue">{{ trans('public.attachments') }}</h3>

                        <ul class="p-0 m-0 pt-10">
                            @foreach($textLesson->attachments as $attachment)
                                <li class="mt-10 p-10 rounded bg-info-light font-14 font-weight-500 text-dark-blue d-flex align-items-center justify-content-between text-ellipsis">
                                    <span class="">{{ $attachment->file->title }}</span>

                                    <a href="{{ $course->getLearningPageUrl() }}?type=file&item={{ $attachment->file->id }}" target="_blank">
                                        <i data-feather="download-cloud" width="20" class="text-secondary"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($course->textLessons) and count($course->textLessons))
                    <div class="shadow-sm rounded-lg bg-white px-15 px-md-25 py-20 mt-30">
                        <h3 class="category-filter-title font-16 font-weight-bold text-dark-blue">{{ trans('public.course_sessions') }}</h3>

                        <div class="p-0 m-0 pt-10">
                            @foreach($course->textLessons as $lesson)
                                <a href="{{ $course->getUrl() }}/lessons/{{ $lesson->id }}/read"
                                   class="d-block mt-10 px-10 py-15 rounded font-14 font-weight-500 text-ellipsis @if($lesson->id == $textLesson->id) bg-primary text-white @else bg-info-light text-dark-blue @endif">
                                    {{ $loop->iteration .'- '. $lesson->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script>
        var learningToggleLangSuccess = '{{ trans('public.course_learning_change_status_success') }}';
        var learningToggleLangError = '{{ trans('public.course_learning_change_status_error') }}';
    </script>

    <script src="/assets/default/js/parts/text_lesson.min.js"></script>
@endpush
