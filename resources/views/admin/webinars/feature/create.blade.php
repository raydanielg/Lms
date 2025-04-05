@extends('admin.layouts.app')

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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @php
                                $pages = \App\Models\FeatureWebinar::$pages;
                            @endphp

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <form action="{{ getAdminPanelUrl() }}/webinars/features/{{ !empty($feature) ? $feature->id.'/update' : 'store'  }}" method="post">
                                        {{ csrf_field() }}

                                        @if(!empty(getGeneralSettings('content_translate')))
                                            <div class="form-group">
                                                <label class="input-label">{{ trans('auth.language') }}</label>
                                                <select name="locale" class="form-control {{ !empty($feature) ? 'js-edit-content-locale' : '' }}">
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


                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.position') }}</label>
                                            <select name="page" class="form-control">
                                                @foreach($pages as $page)
                                                    <option value="{{ $page }}" @if(!empty($feature) and $feature->page == $page) selected @endif>{{ trans('admin/main.page_'.$page) }}</option>
                                                @endforeach
                                            </select>
                                            @error('locale')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label d-block">{{ trans('admin/main.webinar') }}</label>
                                            <select name="webinar_id" class="form-control search-webinar-select2 @error('webinar_id') is-invalid @enderror" data-placeholder="{{ trans('admin/main.search_webinar') }}">
                                                @if(!empty($feature))
                                                    <option value="{{ $feature->webinar->id }}">{{ $feature->webinar->title }}</option>
                                                @endif
                                            </select>

                                            @error('webinar_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label d-block">{{ trans('public.description') }}</label>
                                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6">{{ !empty($feature) ? $feature->description : '' }}</textarea>

                                            @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.status') }}</label>
                                            <select class="custom-select" name="status">
                                                <option value="pending" {{ (!empty($feature) and $feature->status == 'pending') ? 'selected' : '' }}>{{ trans('admin/main.pending') }}</option>
                                                <option value="publish" {{ (!empty($feature) and $feature->status == 'publish') ? 'selected' : '' }}>{{ trans('admin/main.published') }}</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
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
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.new_featured_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold mb-2">{{trans('admin/main.new_featured_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.new_featured_hint_title_2')}}</div>
                        <div class=" text-small font-600-bold mb-2">{{trans('admin/main.new_featured_hint_description_2')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts_bottom')

@endpush
