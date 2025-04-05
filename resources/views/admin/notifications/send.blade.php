@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
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
                <div class="card-body">

                    <form method="post" action="{{ getAdminPanelUrl() }}/notifications/{{ !empty($notification) ? $notification->id .'/update' : 'store' }}" class="form-horizontal form-bordered mt-4">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label" for="inputDefault">{!! trans('admin/main.title') !!}</label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ !empty($notification) ? $notification->title : old('title') }}">
                                    <div class="invalid-feedback">@error('title') {{ $message }} @enderror</div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{!! trans('admin/main.type') !!}</label>
                                    <select name="type" id="typeSelect" class="form-control @error('type') is-invalid @enderror">
                                        <option value="" selected disabled></option>

                                        @foreach(\App\Models\Notification::$notificationsType as $type)
                                            <option value="{{ $type }}" @if(!empty($notification) and $notification->type == $type) selected @endif>{{ trans('admin/main.notification_'.$type) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">@error('type') {{ $message }} @enderror</div>
                                    <div class="text-muted text-small mt-1">{{ trans('admin/main.new_notification_hint') }}</div>
                                </div>

                                <div class="form-group @if(!$errors->has('user_id') and (empty($notification) or empty($notification->user))) d-none @endif" id="userSelect">
                                    <label class="input-label d-block">{{ trans('admin/main.user') }}</label>
                                    <select name="user_id" class="form-control search-user-select2 @error('user_id') is-invalid @enderror"
                                            data-placeholder="{{ trans('public.search_user') }}">

                                        @if(!empty($notification) and !empty($notification->user))
                                            <option value="{{ $notification->user->id }}" selected>{{ $notification->user->full_name }}</option>
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">@error('user_id') {{ $message }} @enderror</div>
                                </div>

                                <div class="form-group @if(!$errors->has('webinar_id') and (empty($notification) or empty($notification->webinar))) d-none @endif" id="webinarSelect">
                                    <label class="input-label d-block">{{ trans('admin/main.course') }}</label>
                                    <select name="webinar_id" class="form-control search-webinar-select2 @error('webinar_id') is-invalid @enderror"
                                            data-placeholder="{{ trans('admin/main.search_webinar') }}">

                                        @if(!empty($notification) and !empty($notification->webinar))
                                            <option value="{{ $notification->webinar->id }}" selected>{{ $notification->webinar->title }}</option>
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">@error('webinar_id') {{ $message }} @enderror</div>
                                </div>

                                <div class="form-group @if(!$errors->has('group_id') and empty($notification->group)) d-none @endif" id="groupSelect">
                                    <label class="input-label d-block">{{ trans('admin/main.group') }}</label>
                                    <select name="group_id" class="form-control select2 @error('group_id') is-invalid @enderror">
                                        <option value="" selected disabled></option>

                                        @foreach($userGroups as $userGroup)
                                            <option value="{{ $userGroup->id }}" @if(!empty($notification) and !empty($notification->group) and $notification->group->id == $userGroup->id) selected @endif>{{ $userGroup->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">@error('group_id') {{ $message }} @enderror</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="control-label">{{ trans('admin/main.message') }}</label>
                            <textarea name="message" class="summernote form-control text-left  @error('message') is-invalid @enderror">{{ (!empty($notification)) ? $notification->message :'' }}</textarea>
                            <div class="invalid-feedback">@error('message') {{ $message }} @enderror</div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-12">
                                <button class="btn btn-primary" type="submit">{{ trans('notification.send_notification') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>

    <script src="/assets/default/js/admin/notifications.min.js"></script>
@endpush
