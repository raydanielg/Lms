@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.blog') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.blog') }}</div>
            </div>
        </div>

        <div class="section-body">

            <section class="card">
                <div class="card-body">
                    <form action="{{ getAdminPanelUrl() }}/blog" method="get" class="mb-0">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.search') }}</label>
                                    <input name="title" type="text" class="form-control" value="{{ request()->get('title') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                    <div class="input-group">
                                        <input type="date" id="from" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                    <div class="input-group">
                                        <input type="date" id="to" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.category') }}</label>
                                    <select name="category_id" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_categories') }}</option>

                                        @foreach($blogCategories as $category)
                                            <option value="{{ $category->id }}" @if(request()->get('category_id') == $category->id) selected="selected" @endif>{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.author') }}</label>
                                    <select name="author_id" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_authors') }}</option>

                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}" @if(request()->get('author_id') == $author->id) selected="selected" @endif>{{ $author->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.status') }}</label>
                                    <select name="status" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_status') }}</option>
                                        <option value="pending" @if(request()->get('status') == 'pending') selected @endif>{{ trans('admin/main.draft') }}</option>
                                        <option value="publish" @if(request()->get('status') == 'publish') selected @endif>{{ trans('admin/main.publish') }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group mt-1">
                                    <label class="input-label mb-4"> </label>
                                    <input type="submit" class="text-center btn btn-primary w-100" value="{{ trans('admin/main.show_results') }}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @can('admin_blog_create')
                                <a href="{{ getAdminPanelUrl() }}/blog/create" class="btn btn-success">{{ trans('admin/main.create_blog') }}</a>
                            @endcan

                            @can('admin_blog_categories')
                                <a href="{{ getAdminPanelUrl() }}/blog/categories" class="btn btn-primary ml-2">{{ trans('admin/main.create_category') }}</a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.title') }}</th>
                                        <th>{{ trans('admin/main.category') }}</th>
                                        <th>{{ trans('admin/main.author') }}</th>
                                        <th>{{ trans('admin/main.comments') }}</th>
                                        <th>{{ trans('public.date') }}</th>
                                        <th>{{ trans('admin/main.status') }}</th>
                                        <th>{{ trans('admin/main.action') }}</th>
                                    </tr>
                                    @foreach($blog as $post)
                                        <tr>
                                            <td>
                                                <a href="{{ $post->getUrl() }}" target="_blank">{{ $post->title }}</a>
                                            </td>
                                            <td>{{ $post->category->title }}</td>
                                            @if(!empty($post->author->full_name))
                                            <td>{{ $post->author->full_name }}</td>
                                            @else
                                            <td class="text-danger">Deleted</td>
                                            @endif
                                            <td>
                                                <a href="{{ $post->getUrl() }}" target="_blank">{{ $post->comments_count }}</a>
                                            </td>
                                            <td>{{ dateTimeFormat($post->created_at, 'j M Y | H:i') }}</td>
                                            <td>
                                                <span class="text-{{ ($post->status == 'pending') ? 'warning' : 'success' }}">
                                                    {{ ($post->status == 'pending') ? trans('admin/main.pending') : trans('admin/main.published') }}
                                                </span>
                                            </td>

                                            <td width="150px">
                                                @can('admin_blog_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/blog/{{ $post->id }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('admin_blog_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl('/blog/'.$post->id.'/delete'),'btnClass' => ''])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $blog->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

