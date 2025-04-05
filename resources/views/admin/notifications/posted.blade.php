@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    @can('admin_notifications_send')
                        <div class="text-right">
                            <a href="{{ getAdminPanelUrl() }}/notifications/send" class="btn btn-primary">{{ trans('notification.send_notification') }}</a>
                        </div>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped font-14" id="datatable-basic">

                            <tr>
                                <th class="text-left">{{ trans('admin/main.title') }}</th>
                                <th class="text-center">{{ trans('notification.sender') }}</th>
                                <th class="text-center">{{ trans('notification.receiver') }}</th>
                                <th class="text-center">{{ trans('site.message') }}</th>
                                <th class="text-center">{{ trans('admin/main.type') }}</th>
                                <th class="text-center">{{ trans('admin/main.status') }}</th>
                                <th class="text-center">{{ trans('admin/main.created_at') }}</th>
                                <th>{{ trans('public.controls') }}</th>
                            </tr>

                            @foreach($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->title }}</td>
                                    <td class="text-center">{{ $notification->senderUser->full_name }}</td>

                                    <td class="text-center">
                                        @if(!empty($notification->user))
                                            <span class="d-block">{{ $notification->user->full_name }}</span>
                                            <span class="font-12 d-block">ID: {{ $notification->user->id }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <button type="button" data-item-id="{{ $notification->id }}" class="js-show-description btn btn-outline-primary">{{ trans('admin/main.show') }}</button>
                                        <input type="hidden" value="{{ nl2br($notification->message) }}">
                                    </td>
                                    <td class="text-center">{{ trans('admin/main.notification_'.$notification->type) }}</td>
                                    <td class="text-center">
                                        @if(empty($notification->notificationStatus))
                                            <span class="text-danger">{{ trans('admin/main.unread') }}</span>
                                        @else
                                            <span class="text-success">{{ trans('admin/main.read') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ dateTimeFormat($notification->created_at,'j M Y | H:i') }}</td>

                                    <td width="100">

                                        @can('admin_notifications_delete')
                                            @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/notifications/'. $notification->id.'/delete','btnClass' => ''])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                </div>

                <div class="card-footer text-center">
                    {{ $notifications->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="notificationMessageModal" tabindex="-1" aria-labelledby="notificationMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationMessageLabel">{{ trans('admin/main.contacts_message') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/notifications.min.js"></script>
@endpush
