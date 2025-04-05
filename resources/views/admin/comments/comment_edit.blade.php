@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.edit_comment') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.edit_comment') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header"></div>
                        <div class="card-body ">
                            <form action="{{ getAdminPanelUrl() }}/comments/{{$page}}/{{ $comment->id }}/update" method="post">
                                {{ csrf_field() }}

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('admin/main.edit_comment') }}</label>
                                    <textarea id="summernote" name="comment" class="summernote form-control @error('comment')  is-invalid @enderror">{!! !empty($comment) ? $comment->comment : old('comment')  !!}</textarea>

                                    @error('comment')
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
