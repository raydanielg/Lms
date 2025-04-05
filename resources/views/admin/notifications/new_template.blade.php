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
                    <div class="section-title ml-0 mt-0 mb-3"><h5>{{trans('admin/main.tags')}}</h5></div>
                    <div class="row">
                        @foreach(\App\Models\NotificationTemplate::$templateKeys as $key => $value)
                            <div class="col-6 col-md-4">
                                <p>{{ trans('admin/main.'.$key) }} : {{ $value }} </p>
                                <hr>
                            </div>
                        @endforeach
                    </div>

                    <strong class="mt-4 d-block">{{ trans('admin/main.use_key_in_title_and_message_body') }}</strong>

                    <form method="post" action="{{ getAdminPanelUrl() }}/notifications/templates/{{ !empty($template) ? $template->id .'/update' : 'store' }}" class="form-horizontal form-bordered mt-4">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="control-label" for="inputDefault">{!! trans('admin/main.title') !!}</label>
                            <input type="text" name="title" class="form-control col-md-6 @error('title') is-invalid @enderror" value="{{ !empty($template) ? $template->title : '' }}">
                            <div class="invalid-feedback">@error('title') {{ $message }} @enderror</div>
                        </div>


                        <div class="form-group ">
                            <label class="control-label" for="inputDefault">{{ trans('admin/main.message_body') }}</label>
                            <textarea name="template" class="summernote form-control text-left  @error('template') is-invalid @enderror">{{ (!empty($template)) ? $template->template :'' }}</textarea>
                            <div class="invalid-feedback">@error('template') {{ $message }} @enderror</div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-12">
                                <button class="btn btn-primary " type="submit">{{ trans('admin/main.save') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3"><h5>{{trans('admin/main.hints')}}</h5></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.new_notification_template_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.new_notification_template_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.new_notification_template_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.new_notification_template_hint_description_2')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
@endpush
