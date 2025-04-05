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
                    <p class="">
                        <span class="font-weight-bold">{{ trans('admin/main.course_title') }}</span>:
                        {{ $webinar->title }}
                    </p>

                    <form method="post" action="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/sendNotification" class="form-horizontal form-bordered mt-4">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label" for="inputDefault">{!! trans('admin/main.title') !!}</label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ !empty($notification) ? $notification->title : old('title') }}">
                                    <div class="invalid-feedback">@error('title') {{ $message }} @enderror</div>
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
@endpush
