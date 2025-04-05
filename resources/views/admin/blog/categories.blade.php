@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.blog_categories') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.blog_categories') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                @can('admin_blog_categories')
                                    @if(!empty($blogCategories))
                                        <li class="nav-item">
                                            <a class="nav-link {{ (!empty($errors) and $errors->has('title')) ? '' : 'active' }}" id="categories-tab" data-toggle="tab" href="#categories" role="tab" aria-controls="categories" aria-selected="true">{{ trans('admin/main.categories') }}</a>
                                        </li>
                                    @endif
                                @endcan

                                @can('admin_blog_categories_create')
                                    <li class="nav-item">
                                        <a class="nav-link {{ ((!empty($errors) and $errors->has('title')) or !empty($editCategory)) ? 'active' : '' }}" id="newCategory-tab" data-toggle="tab" href="#newCategory" role="tab" aria-controls="newCategory" aria-selected="true">{{ trans('admin/main.create_category') }}</a>
                                    </li>
                                @endcan
                            </ul>

                            <div class="tab-content" id="myTabContent2">
                                @can('admin_blog_categories')
                                    @if(!empty($blogCategories))
                                        <div class="tab-pane mt-3 fade {{ (!empty($errors) and $errors->has('title')) ? '' : 'active show' }}" id="categories" role="tabpanel" aria-labelledby="categories-tab">
                                            <div class="table-responsive">
                                                <table class="table table-striped font-14">
                                                    <tr>
                                                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                                                        <th class="text-center">{{ trans('admin/main.posts') }}</th>
                                                        <th>{{ trans('admin/main.action') }}</th>
                                                    </tr>

                                                    @foreach($blogCategories as $category)
                                                        <tr>
                                                            <td class="text-left">{{ $category->title }}</td>
                                                            <td class="text-center">{{ $category->blog_count }}</td>
                                                            <td>
                                                                @can('admin_edit_trending_categories')
                                                                    <a href="{{ getAdminPanelUrl() }}/blog/categories/{{ $category->id }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('admin_delete_trending_categories')
                                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl('/blog/categories/'. $category->id .'/delete'), 'btnClass' => ''])
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                @endcan

                                @can('admin_blog_categories_create')
                                    <div class="tab-pane mt-3 fade {{ ((!empty($errors) and $errors->has('title')) or !empty($editCategory)) ? 'active show' : '' }}" id="newCategory" role="tabpanel" aria-labelledby="newCategory-tab">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <form action="{{ getAdminPanelUrl() }}/blog/categories/{{ !empty($editCategory) ? $editCategory->id.'/update' : 'store' }}" method="post">
                                                    {{ csrf_field() }}

                                                    @if(!empty(getGeneralSettings('content_translate')) and !empty($userLanguages))
                                                        <div class="form-group">
                                                            <label class="input-label">{{ trans('auth.language') }}</label>
                                                            <select name="locale" class="form-control {{ !empty($editCategory) ? 'js-edit-content-locale' : '' }}">
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
                                                        <label>{{ trans('/admin/main.title') }}</label>
                                                        <input type="text" name="title"
                                                               class="form-control  @error('title') is-invalid @enderror"
                                                               value="{{ !empty($editCategory) ? $editCategory->title : '' }}"
                                                               placeholder="{{ trans('admin/main.choose_title') }}"/>
                                                        @error('title')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>

                                                    <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
