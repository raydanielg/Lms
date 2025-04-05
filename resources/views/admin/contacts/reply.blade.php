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

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header flex-column align-items-start">
                            <h4>{{ trans('admin/main.user_name') }}: <span class="text-black-50">{{ $contact->name }}</span></h4>
                            <h4>{{ trans('admin/main.email') }} : <span class="text-black-50">{{ $contact->email }}</span></h4>
                            <h4>{{ trans('admin/main.phone') }} : <span class="text-black-50">{{ $contact->phone }}</span></h4>
                            <h4>{{ trans('site.message') }} :</h4>
                            <p class="mt-2">{{ nl2br($contact->message) }}</p>
                        </div>

                        <div class="card-body ">
                            <form action="{{ getAdminPanelUrl() }}/contacts/{{ $contact->id }}/reply" method="post">
                                {{ csrf_field() }}

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('admin/main.reply_comment') }}</label>
                                    <textarea id="summernote" name="reply" class="summernote form-control @error('reply')  is-invalid @enderror">{!! !empty($contact->reply) ? $contact->reply : old('reply')  !!}</textarea>

                                    @error('reply')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <button type="submit" class="mt-3 btn btn-primary">{{ trans('admin/main.save_change') }}</button>
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
@endpush
