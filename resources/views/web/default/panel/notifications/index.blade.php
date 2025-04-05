@extends(getTemplate() .'.panel.layouts.panel_layout')

@section('content')
    <section>
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="section-title">{{ trans('panel.notifications') }}</h2>

            <a href="/panel/notifications/mark-all-as-read" class="delete-action d-flex align-items-center cursor-pointer text-hover-primary" data-title="{{ trans('update.convert_unread_messages_to_read') }}" data-confirm="{{ trans('update.yes_convert') }}">
                <i data-feather="check" width="20" height="20"></i>
                <span class="ml-5 font-16">{{ trans('update.mark_all_notifications_as_read') }}</span>
            </a>
        </div>

        @if(!empty($notifications) and !$notifications->isEmpty())
            @foreach($notifications as $notification)
                <div class="notification-card rounded-sm panel-shadow bg-white py-15 py-lg-20 px-15 px-lg-40 mt-20">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-3 mt-10 mt-lg-0 d-flex align-items-start">
                            @if(empty($notification->notificationStatus))
                                <span class="notification-badge badge badge-circle-danger mr-5 mt-5 d-flex align-items-center justify-content-center"></span>
                            @endif

                            <div class="">
                                <h3 class="notification-title font-16 font-weight-bold text-dark-blue">{{ $notification->title }}</h3>
                                <span class="notification-time d-block font-12 text-gray mt-5">{{ dateTimeFormat($notification->created_at,'j M Y | H:i') }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-lg-5 mt-10 mt-lg-0">
                            <span class="font-weight-500 text-gray font-14">{!! truncate($notification->message, 150, true) !!}</span>
                        </div>

                        <div class="col-12 col-lg-4 mt-10 mt-lg-0 text-right">
                            <button type="button" data-id="{{ $notification->id }}" id="showNotificationMessage{{ $notification->id }}" class="js-show-message btn btn-border-white @if(!empty($notification->notificationStatus)) seen-at @endif">{{ trans('public.view') }}</button>
                            <input type="hidden" class="notification-message" value="{!! $notification->message !!}">
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="my-30">
                {{ $notifications->appends(request()->input())->links('vendor.pagination.panel') }}
            </div>
        @else
            @include(getTemplate() . '.includes.no-result',[
               'file_name' => 'webinar.png',
               'title' => trans('panel.notification_no_result'),
               'hint' => nl2br(trans('panel.notification_no_result_hint')),
           ])
        @endif
    </section>

    <div class="mt-5 d-none" id="messageModal">
        <div class="text-center">
            <h3 class="modal-title font-16 font-weight-bold text-dark-blue"></h3>
            <span class="modal-time d-block font-12 text-gray mt-5"></span>
            <span class="modal-message text-gray mt-20"></span>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script>
        (function ($) {
            "use strict";

            @if(!empty(request()->get('notification')))
            setTimeout(() => {
                $('body #showNotificationMessage{{ request()->get('notification') }}').trigger('click');

                let url = window.location.href;
                url = url.split('?')[0];
                window.history.pushState("object or string", "Title", url);
            }, 400);
            @endif
        })(jQuery)
    </script>

    <script src="/assets/default/js/panel/notifications.min.js"></script>
@endpush
