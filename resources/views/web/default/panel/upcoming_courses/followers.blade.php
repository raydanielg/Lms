@extends('web.default.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')
    <div class="">
        <h2 class="font-20 text-dark-blue font-weight-bold">
            "<a href="{{ $upcomingCourse->getUrl() }}" target="_blank" class="">{{ $upcomingCourse->title }}</a>"
            <span class="ml-5">{{ trans('update.followers') }}</span>
        </h2>
    </div>

    <div class="bg-white mt-20 shadow rounded-sm">
        <div class="row">
            <div class="col-12 col-lg-6 upcoming-followers-card py-25">
                <div class="px-30 h-100">
                    <div class="font-16 font-weight-500 text-gray">{{ trans('update.followers') }}</div>

                    @if(!empty($followers) and $followers->isNotEmpty())
                        <div class="upcoming-followers-scrollable pb-20" data-simplebar @if((!empty($isRtl) and $isRtl)) data-simplebar-direction="rtl" @endif>
                            @foreach($followers as $follower)
                                <div class="d-flex align-items-center mt-20">
                                    <div class="size-50 rounded-circle">
                                        <img src="{{ $follower->user->getAvatar(50) }}" alt="{{ $follower->user->full_name }}" class="img-cover rounded-circle">
                                    </div>
                                    <div class="ml-10">
                                        <h4 class="font-16 font-weight-500 text-dark-blue">{{ $follower->user->full_name }}</h4>
                                        <p class="font-12 text-gray mt-5">{{ dateTimeFormat($follower->created_at, 'j M Y H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="d-flex-center flex-column h-100 text-center">
                            <img src="/assets/default/img/upcoming/no_followers.svg" alt="no followers" width="251" height="239">
                            <h4 class="mt-10 font-20 font-weight-bold text-dark-blue">{{ trans('update.no_followers') }}</h4>
                            <p class="mt-5 font-14 font-weight-500 text-gray">{{ trans('update.this_course_doesnt_have_any_followers') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-6 py-15">
                <div class="d-flex justify-content-center flex-column h-100">
                    <div class="d-flex-center flex-column text-center flex-grow-1 mt-20 mt-lg-0">
                        <div class="">
                            <img src="/assets/default/img/upcoming/send_notification.svg" alt="send notification" width="274" height="240">
                        </div>

                        <h4 class="mt-20 font-20 font-weight-bold text-dark-blue">{{ trans('update.send_a_notification') }}</h4>

                        @if(!empty($upcomingCourse->webinar_id))
                            <p class="mt-5 font-14 font-weight-500 text-gray">{{ trans('update.published_upcoming_course_send_a_notification_hint') }}</p>
                        @else
                            <p class="mt-5 font-14 font-weight-500 text-gray">{{ trans('update.upcoming_course_send_a_notification_hint') }}</p>
                        @endif
                    </div>

                    <div class="d-flex align-items-lg-center justify-content-between flex-column flex-lg-row p-15 rounded-sm border bg-info-light mt-20 mt-lg-auto">
                        @if(!empty($upcomingCourse->webinar_id))
                            <div class="">
                                <h5 class="font-16 font-weight-500 text-dark-blue">{{ trans('update.course_published') }}</h5>
                                <p class="font-12 text-gray mt-5">{{ trans('update.his_course_already_published') }}</p>
                            </div>

                            <a href="{{ $upcomingCourse->webinar->getUrl() }}" target="_blank" class="btn btn-primary btn-sm mt-15 mt-lg-0">{{ trans('update.view_course') }}</a>
                        @else
                            <div class="">
                                <h5 class="font-16 font-weight-500 text-dark-blue">{{ trans('update.notify_followers') }}</h5>
                                <p class="font-12 text-gray mt-5">{{ trans('update.send_a_notifications_to_all_followers_and_let_them_know_course_publishing') }}</p>
                            </div>

                            <button type="button" data-id="{{ $upcomingCourse->id }}" class="js-mark-as-released webinar-actions btn btn-primary btn-sm mt-15 mt-lg-0">{{ trans('update.assign_a_course') }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')

    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/js/panel/upcoming_course.min.js"></script>
@endpush
