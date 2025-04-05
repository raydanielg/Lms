@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.supports') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.supports') }}</div>
            </div>
        </div>

        <div class="row">


            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-envelope"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.total_conversations')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalConversations }}
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-hourglass-start"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.pending_reply')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $pendingReplySupports }}
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-envelope-open"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.open_conversations')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $openConversationsCount }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-envelope"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.closed_conversations')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $closeConversationsCount }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="section-body">
            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.search')}}</label>
                                    <input type="text" name="title" value="{{ request()->get('title') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="fsdate" class="text-center form-control" name="date" value="{{ request()->get('date') }}" placeholder="Date">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.department')}}</label>
                                    <select name="department_id" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.all_departments')}}</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" @if(request()->get('department_id') == $department->id) selected @endif>{{ $department->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.role')}}</label>
                                    <select name="role_id" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.all_user_roles')}}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @if(request()->get('role_id') == $role->id) selected @endif>{{ $role->caption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.status')}}</label>
                                    <select name="status" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.all_status')}}</option>
                                        <option value="open" @if(request()->get('status') == 'open') selected @endif>{{trans('admin/main.open')}}</option>
                                        <option value="replied" @if(request()->get('status') == 'replied') selected @endif>{{trans('admin/main.pending_reply')}}</option>
                                        <option value="supporter_replied" @if(request()->get('status') == 'supporter_replied') selected @endif>{{trans('admin/main.replied')}}</option>
                                        <option value="close" @if(request()->get('status') == 'close') selected @endif>{{trans('admin/main.closed')}}</option>
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
            </section>

            <section class="card">
                <div class="card-body">
                    <div class="table-responsive text-center">
                        <table class="table table-striped font-14">

                            <tr>
                                <th>{{trans('admin/main.title')}}</th>
                                <th class="text-center">{{trans('admin/main.created_date')}}</th>
                                <th class="text-center">{{trans('admin/main.last_update')}}</th>
                                <th class="text-left">{{trans('admin/main.user')}}</th>
                                <th class="text-center">{{trans('admin/main.role')}}</th>
                                <th class="text-center">{{trans('admin/main.department')}}</th>
                                <th class="text-center">{{trans('admin/main.status')}}</th>
                                <th class="text-center">{{trans('admin/main.actions')}}</th>
                            </tr>

                            @foreach($supports as $support)
                                <tr>
                                    <td>
                                        <a href="{{ getAdminPanelUrl() }}/supports/{{ $support->id }}/conversation">
                                            {{ $support->title }}
                                        </a>
                                    </td>

                                    <td class="text-center">{{ dateTimeFormat($support->created_at,'j M Y | H:i') }}</td>

                                    <td class="text-center">{{ (!empty($support->updated_at)) ? dateTimeFormat($support->updated_at,'j M Y | H:i') : '-' }}</td>

                                    <td class="text-left">
                                        <a title="{{ $support->user->full_name }}" href="{{ $support->user->getProfileUrl() }}" target="_blank">{{ $support->user->full_name }}</a>
                                    </td>

                                    <td class="text-center">
                                        @if($support->user->isUser())
                                            Student
                                        @elseif($support->user->isTeacher())
                                            Teacher
                                        @elseif($support->user->isOrganization())
                                            Organization
                                        @endif
                                    </td>

                                    <td class="text-center">{{ $support->department->title }}</td>

                                    <td class="text-center">
                                        @if($support->status == 'close')
                                            <span class="text-danger">{{ trans('admin/main.close') }}</span>
                                        @elseif($support->status == 'replied' or $support->status == 'open')
                                            <span class="text-warning">{{trans('admin/main.pending_reply')}}</span>
                                        @else
                                            <span class="text-primary">{{trans('admin/main.replied')}}</span>
                                        @endif
                                    </td>

                                    <td class="text-center" width="50">
                                        @can('admin_supports_reply')
                                            <a href="{{ getAdminPanelUrl() }}/supports/{{ $support->id }}/conversation" class="btn-transparent btn-sm text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.reply') }}">
                                                <i class="fa fa-reply" aria-hidden="true"></i>
                                            </a>
                                        @endcan

                                        @can('admin_supports_delete')
                                            @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/supports/'.$support->id.'/delete' , 'btnClass' => 'btn-sm'])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                </div>

                <div class="card-footer text-center">
                    {{ $supports->appends(request()->input())->links() }}
                </div>
            </section>

        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
