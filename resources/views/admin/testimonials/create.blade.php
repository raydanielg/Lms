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
                <div class="breadcrumb-item">{{ trans('admin/main.testimonials') }}</div>
            </div>
        </div>


        <div class="section-body">

            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <h2 class="section-title ml-4">{{ !empty($testimonial) ? trans('admin/main.edit') : trans('admin/main.create') }}</h2>

                        <div class="card-body">
                            <form action="{{ getAdminPanelUrl() }}/testimonials/{{ !empty($testimonial) ? $testimonial->id.'/update' : 'store' }}" method="Post">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-12 col-lg-6">

                                        @if(!empty(getGeneralSettings('content_translate')))
                                            <div class="form-group">
                                                <label class="input-label">{{ trans('auth.language') }}</label>
                                                <select name="locale" class="form-control {{ !empty($testimonial) ? 'js-edit-content-locale' : '' }}">
                                                    @foreach($userLanguages as $lang => $language)
                                                        <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                                                    @endforeach
                                                </select>
                                                @error('locale')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        @else
                                            <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
                                        @endif

                                        <div class="form-group mt-15">
                                            <label class="input-label">{{ trans('admin/main.user_avatar') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text admin-file-manager" data-input="user_avatar" data-preview="holder">
                                                        <i class="fa fa-upload"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="user_avatar" id="user_avatar" value="{{ !empty($testimonial->user_avatar) ? $testimonial->user_avatar : old('user_avatar') }}" class="form-control @error('user_avatar') is-invalid @enderror"/>
                                                <div class="input-group-append">
                                                    <button type="button" class="input-group-text admin-file-view" data-input="user_avatar">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>

                                                @error('user_avatar')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label>{{ trans('admin/main.user_name') }}</label>
                                            <input type="text" name="user_name" class="form-control  @error('user_name') is-invalid @enderror"
                                                   value="{{ !empty($testimonial) ? $testimonial->user_name : old('user_name') }}"/>
                                            @error('user_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label>{{ trans('admin/main.user_bio') }}</label>
                                            <input type="text" name="user_bio" class="form-control  @error('user_bio') is-invalid @enderror"
                                                   value="{{ !empty($testimonial) ? $testimonial->user_bio : old('user_bio') }}"/>
                                            @error('user_bio')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label>{{ trans('admin/main.rate') }}</label>
                                            <input type="number" name="rate" class="form-control  @error('rate') is-invalid @enderror"
                                                   value="{{ !empty($testimonial) ? $testimonial->rate : old('rate') }}" placeholder="{{ trans('admin/main.testimonial_rate_placeholder') }}"/>
                                            @error('rate')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('admin/main.comment') }}</label>
                                    <textarea id="summernote" name="comment" class="summernote form-control @error('comment')  is-invalid @enderror">{!! !empty($testimonial) ? $testimonial->comment : old('comment')  !!}</textarea>
                                    @error('comment')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group custom-switches-stacked">
                                    <label class="custom-switch pl-0">
                                        <input type="hidden" name="status" value="disable">
                                        <input type="checkbox" name="status" id="testimonialStatus" value="active" {{ (!empty($testimonial) and $testimonial->status == 'active') ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                        <span class="custom-switch-indicator"></span>
                                        <label class="custom-switch-description mb-0 cursor-pointer" for="testimonialStatus">{{ trans('admin/main.active') }}</label>
                                    </label>
                                </div>

                                <div class=" mt-4">
                                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                </div>
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
