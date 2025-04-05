@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.templates') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.templates') }}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped font-14" id="datatable-basic">

                        <tr>
                            <th>{{ trans('admin/main.title') }}</th>
                            <th>{{ trans('admin/main.actions') }}</th>
                        </tr>

                        @foreach($templates as $template)
                            <tr>
                                <td>{{ $template->title }}</td>

                                <td width="100">
                                    @can('admin_notifications_template_edit')
                                        <a href="{{ getAdminPanelUrl() }}/notifications/templates/{{ $template->id }}/edit" class="btn-transparent btn-sm text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan

                                    @can('admin_notifications_template_delete')
                                        @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/notifications/templates/'. $template->id.'/delete','btnClass' => 'btn-sm'])
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div class="card-footer text-center">
                    {{ $templates->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

