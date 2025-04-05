@extends('admin.layouts.app')

@push('libraries_top')

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
                        <div class="card-header">
                            @can('admin_pages_create')
                                <a href="{{ getAdminPanelUrl() }}/pages/create" class="btn btn-primary">{{ trans('admin/main.add_new') }}</a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.name') }}</th>
                                        <th>{{ trans('admin/main.link') }}</th>
                                        <th class="text-center">{{ trans('admin/main.robot') }}</th>
                                        <th class="text-center">{{ trans('admin/main.status') }}</th>
                                        <th>{{ trans('admin/main.created_at') }}</th>
                                        <th>{{ trans('admin/main.action') }}</th>
                                    </tr>
                                    @foreach($pages as $page)
                                        <tr>
                                            <td>{{ $page->name }}</td>
                                            <td>{{ $page->link }}</td>
                                            <td class="text-center">
                                                @if($page->robot)
                                                    <span class="text-success">{{ trans('admin/main.follow') }}</span>
                                                @else
                                                    <span class="text-danger">{{ trans('admin/main.no_follow') }}</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if($page->status == 'publish')
                                                    <span class="text-success">{{ trans('admin/main.published') }}</span>
                                                @else
                                                    <span class="text-warning">{{ trans('admin/main.is_draft') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ dateTimeFormat($page->created_at, 'j M Y | H:i') }}</td>
                                            <td width="150px">

                                                @can('admin_pages_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/pages/{{ $page->id }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_pages_toggle')
                                                    <a href="{{ getAdminPanelUrl() }}/pages/{{ $page->id }}/toggle" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ ($page->status == 'draft') ? trans('admin/main.publish') : trans('admin/main.draft') }}">
                                                        @if($page->status == 'draft')
                                                            <i class="fa fa-arrow-up"></i>
                                                        @else
                                                            <i class="fa fa-arrow-down"></i>
                                                        @endif
                                                    </a>
                                                @endcan

                                                @can('admin_pages_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/pages/'.$page->id.'/delete' , 'btnClass' => ''])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $pages->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

