@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.users_groups') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.users_groups') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-left">{{ trans('admin/main.name') }}</th>
                                        <th>{{ trans('admin/main.users') }}</th>
                                        <th>{{ trans('admin/main.commission') }}</th>
                                        <th>{{ trans('admin/main.discount') }}</th>
                                        <th>{{ trans('admin/main.status') }}</th>
                                        <th>{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach($groups as $group)
                                        <tr>
                                            <td>{{ $group->id }}</td>
                                            <td class="text-left">
                                                <span>{{ $group->name }}</span>
                                            </td>
                                            <td>{{ $group->groupUsers->count() }}</td>
                                            <td>{{ $group->commission ?? 0 }}%</td>
                                            <td>{{ $group->discount ?? 0 }}%</td>
                                            <td>
                                                <span class="{{ $group->status == 'active' ? 'text-success' : 'text-danger' }}">{{ trans('admin/main.'.$group->status) }}</span>
                                            </td>
                                            <td>
                                                @can('admin_group_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/users/groups/{{ $group->id }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_group_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/users/groups/'. $group->id.'/delete','btnClass' => ''])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $groups->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
