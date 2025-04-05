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

            @if(!empty(session()->has('send_email_error')))
                <div class="alert alert-danger my-25">
                    <h4 class="alert-heading">Error !</h4>

                    <p class="">{{ session()->get('send_email_error') }}</p>
                </div>

                @php
                    session()->forget('send_email_error');
                @endphp
            @endif

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ getAdminPanelUrl() }}/newsletters/send" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-12 col-md-8 col-lg-6">

                                        <div class="form-group">
                                            <label>{{ trans('admin/main.title') }}</label>
                                            <input type="text" name="title"
                                                   class="form-control  @error('title') is-invalid @enderror"
                                                   value="{{ old('title') }}"/>
                                            @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('update.send_method') }}</label>
                                            <select name="send_method" class="js-newsletter-send-method form-control">
                                                <option value="send_to_all" {{ old('send_method') == 'send_to_all' ? 'selected' : '' }}>{{ trans('update.send_newsletter_to_all') }}</option>
                                                <option value="send_to_bcc" {{ old('send_method') == 'send_to_bcc' ? 'selected' : '' }}>{{ trans('update.send_newsletter_to_bcc') }}</option>
                                                <option value="send_to_excel" {{ old('send_method') == 'send_to_excel' ? 'selected' : '' }}>{{ trans('update.send_newsletter_to_excel') }}</option>
                                            </select>
                                        </div>

                                        <div class="form-group js-newsletter-bcc-email {{ (old('send_method') != 'send_to_bcc') ? 'd-none' : '' }}">
                                            <label>{{ trans('update.send_newsletter_bcc_email') }}</label>
                                            <input type="text" name="bcc_email"
                                                   class="form-control  @error('bcc_email') is-invalid @enderror"
                                                   value="{{ old('bcc_email') }}"/>
                                            @error('bcc_email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group js-newsletter-excel {{ (old('send_method') != 'send_to_excel') ? 'd-none' : '' }}">
                                            <label>{{ trans('update.send_newsletter_select_excel_file') }}</label>
                                            <input type="file" name="excel"
                                                   class="form-control h-auto @error('excel') is-invalid @enderror"
                                                   value=""/>
                                            @error('excel')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('public.description') }}</label>
                                    <textarea id="summernote" name="description" class="summernote form-control @error('description')  is-invalid @enderror" placeholder="{{ trans('admin/main.description_placeholder') }}">{{ old('description') }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="text-muted text-small mb-3">{{ trans('update.send_newsletter_description_hint') }}</div>
                                <button type="submit" class="btn btn-primary mt-3">{{ trans('admin/main.send') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/assets/default/js/admin/newsletter.min.js"></script>
@endpush
