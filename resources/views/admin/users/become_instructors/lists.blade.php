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
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.user') }}</th>
                                        <th class="text-center">{{ trans('update.package') }}</th>
                                        <th class="text-center">{{ trans('admin/main.status') }}</th>
                                        <th class="text-center">{{ trans('admin/main.created_at') }}</th>
                                        <th>{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach($becomeInstructors as $become)
                                        <tr>

                                        @if(!empty($become->user->full_name))
                                        <td>{{ $become->user->full_name }}</td>
                                               @else
                                                <td class="text-danger">User Deleted</td>
                                                @endif


                                           

                                            <td>
                                                @if(!empty($become->registrationPackage))
                                                    {{ $become->registrationPackage->title }}
                                                @else
                                                    ---
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <span class="{{ ($become->status == 'accept' ? 'text-success' : ($become->status == 'pending' ? 'text-warning' : 'text-danger')) }}">
                                                    @if($become->status == 'accept')
                                                        {{ trans('admin/main.accepted') }}
                                                    @elseif($become->status == 'pending')
                                                        {{ trans('admin/main.waiting') }}
                                                    @else
                                                        {{ trans('public.rejected') }}
                                                    @endif
                                                </span>
                                            </td>

                                            <td class="text-center">{{ dateTimeFormat($become->created_at, 'Y M j | H:i') }}</td>

                                            <td>
                                                @can('admin_become_instructors_reject')
                                                    @if($become->status != 'accept')
                                                        @include('admin.includes.delete_button',[
                                                             'url' => getAdminPanelUrl("/users/{$become->user_id}/acceptRequestToInstructor") ,
                                                             'btnClass' => 'mr-1',
                                                             'btnIcon' => 'fa-check',
                                                             'tooltip' => trans('admin/main.accept_request')
                                                        ])

                                                        @include('admin.includes.delete_button',[
                                                         'url' => getAdminPanelUrl("/users/become-instructors/{$become->id}/reject") ,
                                                         'btnClass' => 'mr-1',
                                                         'btnIcon' => 'fa-times',
                                                         'tooltip' => trans('admin/main.reject_request')
                                                    ])
                                                    @endif
                                                @endcan

                                                @can('admin_users_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/users/{{ $become->user_id }}/edit?type=check_instructor_request" class="btn-transparent text-primary mr-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.check') }}">
                                                        <i class="fa fa-id-card" aria-hidden="true"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_become_instructors_delete')
                                                    @include('admin.includes.delete_button',[
                                                             'url' => getAdminPanelUrl().'/users/become-instructors/'. $become->id .'/delete' ,
                                                             'btnClass' => '',
                                                             'btnIcon' => 'fa-trash',
                                                             'tooltip' => trans('admin/main.delete')
                                                         ])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $becomeInstructors->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3"><h5>{{trans('admin/main.hints')}}</h5></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.become_instructor_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.become_instructor_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.become_instructor_hint_title_2')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.become_instructor_hint_description_2')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
