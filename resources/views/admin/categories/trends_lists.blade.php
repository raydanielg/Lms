@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('home.trending_categories') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('home.trending_categories') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                           @can('admin_create_trending_categories')
                                <a href="{{ getAdminPanelUrl() }}/categories/trends/create" class="text-right btn btn-sm btn-success mb-3">{{ trans('admin/main.create_trend_category') }}</a>
                            @endcan
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.title') }}</th>
                                        <th>{{ trans('admin/main.trend_color') }}</th>
                                        <th>{{ trans('admin/main.action') }}</th>
                                    </tr>

                                    @foreach($trends as $trend)
                                        <tr>
                                            <td>{{ $trend->category->title }}</td>
                                            <td>
                                                <span class="badge text-white" style="background-color: {{ $trend->color }}">
                                                    {{ $trend->color }}
                                                </span>
                                            </td>
                                            <td>
                                                @can('admin_edit_trending_categories')
                                                    <a href="{{ getAdminPanelUrl() }}/categories/trends/{{ $trend->id }}/edit" class="btn-transparent btn-sm text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan
                                            @can('admin_delete_trending_categories')
                                              @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/categories/trends/'.$trend->id.'/delete','btnClass' => ''])
                                            @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                        {{ $trends->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="card">
        <div class="card-body">
           <div class="section-title ml-0 mt-0 mb-3"> <h5>{{trans('admin/main.hints')}}</h5> </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{ trans('admin/main.trend_hint_title_1') }}</div>
                        <div class=" text-small font-600-bold">{{ trans('admin/main.trend_hint_description_1') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection

