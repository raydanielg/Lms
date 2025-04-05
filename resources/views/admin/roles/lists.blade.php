@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.roles') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.roles') }}</div>
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
                                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                                        <th>{{ trans('admin/main.users_count') }}</th>
                                        <th>{{ trans('admin/main.is_admin') }}</th>
                                        <th>{{ trans('admin/main.created_at') }}</th>
                                        <th>{{ trans('admin/main.actions') }}</th>
                                    </tr>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>{{$role->id}}</td>
                                            <td class="text-left">{{$role->caption}}</td>
                                            <td>{{$role->users->count()}}</td>
                                            <td>
                                                @if($role->is_admin)
                                                    <span class="text-success fas fa-check"></span>
                                                @else
                                                    <span class="text-danger fas fa-times"></span>
                                                @endif
                                            </td>
                                            <td>{{ dateTimeFormat($role->created_at,'j M Y') }}</td>
                                            <td>
                                                @can('admin_roles_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/roles/{{ $role->id }}/edit" class="btn-transparent text-primary">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @if($role->canDelete())
                                                    @can('admin_roles_delete')
                                                        @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/roles/'.$role->id.'/delete'])
                                                    @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $roles->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
