<div class="dropdown">
    <button type="button" class="btn btn-transparent dropdown-toggle" {{ (empty($unReadNotifications) or count($unReadNotifications) < 1) ? 'disabled' : '' }} id="navbarNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i data-feather="bell" width="20" height="20" class="mr-10"></i>

        @if(!empty($unReadNotifications) and count($unReadNotifications))
            <span class="badge badge-circle-danger d-flex align-items-center justify-content-center">{{ count($unReadNotifications) }}</span>
        @endif
    </button>

    <div class="dropdown-menu pt-20" aria-labelledby="navbarNotification">
        <div class="d-flex flex-column h-100">
            <div class="mb-auto navbar-notification-card" data-simplebar>
                <div class="d-md-none border-bottom mb-20 pb-10 text-right">
                    <i class="close-dropdown" data-feather="x" width="32" height="32" class="mr-10"></i>
                </div>

                @if(!empty($unReadNotifications) and count($unReadNotifications))

                    <div class="d-flex align-items-center p-15 border rounded-sm">
                        <div class="d-flex-center size-40 rounded-circle bg-gray100">
                            <i data-feather="bell" width="20" height="20" class="text-gray"></i>
                        </div>
                        <div class="ml-5">
                            <div class="text-secondary font-14"><span class="font-weight-bold">{{ count($unReadNotifications) }}</span> {{ trans('panel.notifications') }}</div>

                            <a href="/panel/notifications/mark-all-as-read" class="delete-action d-block mt-5 font-12 cursor-pointer text-hover-primary" data-title="{{ trans('update.convert_unread_messages_to_read') }}" data-confirm="{{ trans('update.yes_convert') }}">
                                {{ trans('update.mark_all_notifications_as_read') }}
                            </a>
                        </div>
                    </div>

                    @foreach($unReadNotifications as $unReadNotification)
                        <a href="/panel/notifications?notification={{ $unReadNotification->id }}">
                            <div class="navbar-notification-item border-bottom">
                                <h4 class="font-14 font-weight-bold text-secondary">{{ $unReadNotification->title }}</h4>
                                <span class="notify-at d-block mt-5">{{ dateTimeFormat($unReadNotification->created_at,'j M Y | H:i') }}</span>
                            </div>
                        </a>
                    @endforeach

                @else
                    <div class="d-flex align-items-center text-center py-50">
                        <i data-feather="bell" width="20" height="20" class="mr-10"></i>
                        <span class="">{{ trans('notification.empty_notifications') }}</span>
                    </div>
                @endif

            </div>

            @if(!empty($unReadNotifications) and count($unReadNotifications))
                <div class="mt-10 navbar-notification-action">
                    <a href="/panel/notifications" class="btn btn-sm btn-danger btn-block">{{ trans('notification.all_notifications') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>
