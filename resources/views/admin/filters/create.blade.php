@extends('admin.layouts.app')

@push('styles_top')
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{!empty($filter) ?trans('/admin/main.edit'): trans('admin/main.new') }} {{ trans('admin/main.filters') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}/filters">{{ trans('admin/main.filters') }}</a>
                </div>
                <div class="breadcrumb-item">{{!empty($filter) ?trans('/admin/main.edit'): trans('admin/main.new') }}</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ getAdminPanelUrl() }}/filters/{{ !empty($filter) ? $filter->id.'/update' : 'store' }}"
                                  method="Post">
                                {{ csrf_field() }}

                                @if(!empty(getGeneralSettings('content_translate')))
                                    <div class="form-group">
                                        <label class="input-label">{{ trans('auth.language') }}</label>
                                        <select name="locale" class="form-control {{ !empty($filter) ? 'js-edit-content-locale' : '' }}">
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
                                    <label>{{ trans('/admin/main.category') }}</label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" name="category_id">
                                        <option {{ !empty($trend) ? '' : 'selected' }} disabled>{{ trans('admin/main.choose_category') }}</option>

                                        @foreach($categories as $category)
                                            @if(!empty($category->subCategories) and count($category->subCategories))
                                                <optgroup label="{{  $category->title }}">
                                                    @foreach($category->subCategories as $subCategory)
                                                        <option value="{{ $subCategory->id }}" @if(!empty($filter) and $filter->category_id == $subCategory->id) selected="selected" @endif>{{ $subCategory->title }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                <option value="{{ $category->id }}" class="font-weight-bold" @if(!empty($filter) and $filter->category_id == $category->id) selected="selected" @endif>{{ $category->title }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>


                                <div class="form-group">
                                    <label>{{ trans('admin/main.title') }}</label>
                                    <input type="text" name="title"
                                           class="form-control  @error('title') is-invalid @enderror"
                                           value="{{ !empty($filter) ? $filter->title : old('title') }}"
                                           placeholder="{{ trans('admin/main.choose_title') }}"/>

                                    @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div id="filterOptions" class="ml-1">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <strong class="d-block">{{ trans('admin/main.add_options') }}</strong>

                                        <button type="button" class="btn btn-success add-btn "><i class="fa fa-plus"></i> {{ trans('admin/main.add') }}</button>
                                    </div>

                                    <ul class="draggable-lists list-group">
                                        @if(!empty($filterOptions))
                                            @foreach($filterOptions as $key => $filterOption)

                                                <li class="form-group list-group">

                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text cursor-pointer move-icon">
                                                                <i class="fa fa-arrows-alt"></i>
                                                            </div>
                                                        </div>

                                                        <input type="text" name="sub_filters[{{ $filterOption->id }}][title]"
                                                               class="form-control w-auto flex-grow-1"
                                                               value="{{ $filterOption->title }}"
                                                               placeholder="{{ trans('admin/main.choose_title') }}"/>

                                                        <div class="input-group-append">
                                                            <button type="button" class="btn remove-btn btn-danger"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>

                                </div>

                                <div class="text-right mt-4">
                                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                </div>
                            </form>

                            <li class="form-group main-row list-group d-none">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text cursor-pointer move-icon">
                                            <i class="fa fa-arrows-alt"></i>
                                        </div>
                                    </div>

                                    <input type="text" name="sub_filters[record][title]"
                                           class="form-control w-auto flex-grow-1"
                                           placeholder="{{ trans('admin/main.choose_title') }}"/>

                                    <div class="input-group-append">
                                        <button type="button" class="btn remove-btn btn-danger"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
    <script src="/assets/default/js/admin/filters.min.js"></script>
@endpush
