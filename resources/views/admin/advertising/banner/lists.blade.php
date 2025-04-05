@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle}}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @can('admin_advertising_banners_create')
                                <a href="{{ getAdminPanelUrl() }}/advertising/banners/new" class="btn btn-primary">{{ trans('admin/main.new_banner') }}</a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.title') }}</th>
                                        <th class="text-center">{{ trans('admin/main.position') }}</th>
                                        <th class="text-center">{{ trans('admin/main.banner_size') }}</th>
                                        <th class="text-center">{{ trans('admin/main.published') }}</th>
                                        <th class="text-center">{{ trans('admin/main.created_at') }}</th>
                                        <th>{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach($banners as $banner)
                                        <tr>
                                            <td>{{ $banner->title }}</td>
                                            <td class="text-center">{{ $banner->position }}</td>
                                            <td class="text-center">{{ \App\Models\AdvertisingBanner::$size[$banner->size] }}</td>
                                            <td class="text-center">
                                                @if($banner->published)
                                                    <span class="text-success fas fa-check"></span>
                                                @else
                                                    <span class="text-danger fas fa-times"></span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ dateTimeFormat($banner->created_at, 'Y M j | H:i') }}</td>
                                            <td>
                                                @can('admin_advertising_banners_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/advertising/banners/{{ $banner->id }}/edit" class="btn-sm btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_advertising_banners_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/advertising/banners/'. $banner->id.'/delete','btnClass' => ''])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $banners->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
