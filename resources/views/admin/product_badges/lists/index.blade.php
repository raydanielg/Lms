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

            <div class="card">
                <div class="card-header">
                    @can('admin_product_badges_create')
                        <div class="text-right">
                            <a href="{{ getAdminPanelUrl("/product-badges/create") }}" class="btn btn-primary">{{ trans('update.new_badge') }}</a>
                        </div>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped font-14" id="datatable-basic">

                            <tr>
                                <th class="text-left">{{ trans('admin/main.title') }}</th>
                                <th class="text-center">{{ trans('update.contents') }}</th>
                                <th class="text-center">{{ trans('admin/main.start_date') }}</th>
                                <th class="text-center">{{ trans('admin/main.end_date') }}</th>
                                <th class="text-center">{{ trans('admin/main.status') }}</th>
                                <th>{{ trans('public.controls') }}</th>
                            </tr>

                            @foreach($badges as $badge)
                                <tr>
                                    <td>{{ $badge->title }}</td>

                                    <td class="text-center">{{ $badge->contents_count }}</td>

                                    <td class="text-center">{{ !empty($badge->start_at) ? dateTimeFormat($badge->start_at, 'j M Y') : '-' }}</td>

                                    <td class="text-center">{{ !empty($badge->end_at) ? dateTimeFormat($badge->end_at, 'j M Y') : '-' }}</td>

                                    <td class="text-center">
                                        @if(!$badge->enable)
                                            <span class="text-danger">{{ trans('admin/main.disabled') }}</span>
                                        @elseif(!empty($badge->start_at) and $badge->start_at > time())
                                            <span class="text-warning">{{ trans('admin/main.pending') }}</span>
                                        @elseif(!empty($badge->end_at) and $badge->end_at < time())
                                            <span class="text-danger">{{ trans('panel.expired') }}</span>
                                        @else
                                            <span class="text-success">{{ trans('admin/main.active') }}</span>
                                        @endif
                                    </td>

                                    <td width="100">

                                        @can('admin_product_badges_edit')
                                            <a href="{{ getAdminPanelUrl("/product-badges/{$badge->id}/edit") }}" class="btn-transparent  text-primary mr-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('admin_product_badges_delete')
                                            @include('admin.includes.delete_button',['url' => getAdminPanelUrl("/product-badges/{$badge->id}/delete"),'btnClass' => ''])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                </div>

                <div class="card-footer text-center">
                    {{ $badges->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </section>


@endsection
