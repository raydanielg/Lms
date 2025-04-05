@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $webinarTitle }} - {{ trans('update.waitlists') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{trans('update.waitlists')}}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.search')}}</label>
                                    <input type="text" class="form-control" name="search" placeholder="" value="{{ request()->get('search') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.start_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="fsdate" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.end_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="lsdate" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('update.registration_status')}}</label>
                                    <select name="registration_status" class="form-control">
                                        <option value="">{{trans('admin/main.all')}}</option>
                                        <option value="registered" {{ (request()->get('registration_status') == "registered") ? 'selected' : '' }}>{{ trans('update.registered') }}</option>
                                        <option value="unregistered" {{ (request()->get('registration_status') == "unregistered") ? 'selected' : '' }}>{{ trans('update.unregistered') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group mt-1">
                                    <label class="input-label mb-4"> </label>
                                    <input type="submit" class="text-center btn btn-primary w-100" value="{{trans('admin/main.show_results')}}">
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    @can('admin_waitlists_exports')
                        <a href="{{ getAdminPanelUrl("/waitlists/{$waitlistId}/export_list") }}" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                    @endcan
                </div>

                <div class="card-body">
                    <table class="table table-striped font-14" id="datatable-details">
                        <thead>
                        <tr>
                            <th class="text-left">{{ trans('admin/main.name') }}</th>
                            <th class="">{{ trans('auth.email') }}</th>
                            <th class="">{{ trans('public.phone') }}</th>
                            <th class="">{{ trans('update.registration_status') }}</th>
                            <th class="">{{ trans('update.submission_date') }}</th>
                            <th class="text-left">{{ trans('admin/main.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($waitlists as $waitlist)
                            <tr>
                                <td class="text-left">{{ !empty($waitlist->user) ? $waitlist->user->full_name : $waitlist->full_name }}</td>

                                <td>{{ !empty($waitlist->user) ? $waitlist->user->email : $waitlist->email }}</td>

                                <td>{{ !empty($waitlist->user) ? $waitlist->user->mobile : $waitlist->phone }}</td>

                                <td>
                                    @if(!empty($waitlist->user))
                                        <span class="">{{ trans('update.registered') }}</span>
                                    @else
                                        <span class="">{{ trans('update.unregistered') }}</span>
                                    @endif
                                </td>

                                <td>{{ dateTimeFormat($waitlist->created_at, 'j M Y H:i') }}</td>

                                <td class="">
                                    <div class="d-flex align-items-center justify-content-end">
                                        @include('admin.includes.delete_button',[
                                            'url' => getAdminPanelUrl("/waitlists/items/{$waitlist->id}/delete"),
                                            'btnClass' => 'text-danger',
                                            'btnText' => '<i class="fa fa-times"></i>'
                                        ])

                                        @if(!empty($waitlist->user))
                                            @can('admin_users_impersonate')
                                                <a href="{{ getAdminPanelUrl() }}/users/{{ $waitlist->user->id }}/impersonate" target="_blank" class="btn-transparent  text-primary ml-2" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.login') }}">
                                                    <i class="fa fa-user-shield"></i>
                                                </a>
                                            @endcan

                                            @can('admin_users_edit')
                                                <a href="{{ getAdminPanelUrl() }}/users/{{ $waitlist->user->id }}/edit" class="btn-transparent  text-primary ml-2" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="card-footer text-center">
                    {{ $waitlists->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
