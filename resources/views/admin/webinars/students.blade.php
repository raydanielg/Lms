@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">

    <style>
        .select2-container {
            z-index: 1212 !important;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $webinar->title }} - {{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a></div>
                <div class="breadcrumb-item"><a>{{ $pageTitle }}</a></div>
            </div>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ trans('admin/main.total_students') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalStudents }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-briefcase"></i></div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ trans('update.active_students') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalActiveStudents }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-info-circle"></i></div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ trans('update.expire_students') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalExpireStudents }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-ban"></i></div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ trans('update.average_learning') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $averageLearning }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="card">
        <div class="card-body">
            <form method="get" class="mb-0">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.search') }}</label>
                            <input name="full_name" type="text" class="form-control" value="{{ request()->get('full_name') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                            <div class="input-group">
                                <input type="date" id="from" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                            <div class="input-group">
                                <input type="date" id="to" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.filters') }}</label>
                            <select name="sort" data-plugin-selectTwo class="form-control populate">
                                <option value="">{{ trans('admin/main.filter_type') }}</option>
                                <option value="rate_asc" @if(request()->get('sort') == 'rate_asc') selected @endif>{{ trans('update.rate_ascending') }}</option>
                                <option value="rate_desc" @if(request()->get('sort') == 'rate_desc') selected @endif>{{ trans('update.rate_descending') }}</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.users_group') }}</label>
                            <select name="group_id" data-plugin-selectTwo class="form-control populate">
                                <option value="">{{ trans('admin/main.select_users_group') }}</option>
                                @foreach($userGroups as $userGroup)
                                    <option value="{{ $userGroup->id }}" @if(request()->get('group_id') == $userGroup->id) selected @endif>{{ $userGroup->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.role') }}</label>
                            <select name="role_id" class="form-control">
                                <option value="">{{ trans('admin/main.all_roles') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @if($role->id == request()->get('role_id')) selected @endif>{{ $role->caption }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="input-label">{{ trans('admin/main.status') }}</label>
                            <select name="status" data-plugin-selectTwo class="form-control populate">
                                <option value="">{{ trans('admin/main.all_status') }}</option>
                                <option value="active" @if(request()->get('status') == 'active') selected @endif>{{ trans('admin/main.active') }}</option>
                                <option value="expire" @if(request()->get('status') == 'expire') selected @endif>{{ trans('panel.expired') }}</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group mt-1">
                            <label class="input-label mb-4"> </label>
                            <input type="submit" class="text-center btn btn-primary w-100" value="{{ trans('admin/main.show_results') }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div class="card">
        <div class="card-header">
            @can('admin_webinar_notification_to_students')
                <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/sendNotification" class="btn btn-primary mr-2">{{ trans('notification.send_notification') }}</a>
            @endcan

            @can('admin_enrollment_add_student_to_items')
                <button type="button" id="addStudentToCourse" class="btn btn-primary mr-2">{{ trans('update.add_student_to_course') }}</button>
            @endcan
            <div class="h-10"></div>
        </div>

        <div class="card-body">
            <div class="table-responsive text-center">
                <table class="table table-striped font-14">
                    <tr>
                        <th class="text-left">ID</th>
                        <th class="text-left">{{ trans('admin/main.name') }}</th>
                        <th>{{ trans('admin/main.rate') }}(5)</th>
                        <th>{{ trans('update.learning') }}</th>
                        <th>{{ trans('admin/main.user_group') }}</th>
                        <th>{{ trans('panel.purchase_date') }}</th>
                        <th>{{ trans('admin/main.status') }}</th>
                        <th width="120">{{ trans('admin/main.actions') }}</th>
                    </tr>

                    @foreach($students as $student)

                        <tr>
                            <td class="text-left">{{ $student->id ?? '-' }}</td>
                            <td class="text-left">
                                <div class="d-flex align-items-center">
                                    <figure class="avatar mr-2">
                                        <img src="{{ $student->getAvatar() }}" alt="{{ $student->full_name }}">
                                    </figure>
                                    <div class="media-body ml-1">
                                        <div class="mt-0 mb-1 font-weight-bold">{{ $student->full_name }}</div>

                                        @if($student->mobile)
                                            <div class="text-primary text-small font-600-bold">{{ $student->mobile }}</div>
                                        @endif

                                        @if($student->email)
                                            <div class="text-primary text-small font-600-bold">{{ $student->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span>{{ $student->rates ?? '-' }}</span>
                            </td>

                            <td>
                                <span>{{ $student->learning }}%</span>
                            </td>

                            <td>
                                @if(!empty($student->getUserGroup()))
                                    <span>{{ $student->getUserGroup()->name }}</span>
                                @else
                                    -
                                @endif
                            </td>

                            <td>{{ dateTimeFormat($student->purchase_date, 'j M Y | H:i') }}</td>

                            <td>
                                @if(empty($student->id))
                                    {{-- Gift recipient who has not registered yet --}}
                                    <div class="mt-0 mb-1 font-weight-bold text-warning">{{ trans('update.unregistered') }}</div>
                                @elseif(!empty($webinar->access_days) and !$webinar->checkHasExpiredAccessDays($student->purchase_date, $student->gift_id))
                                    <div class="mt-0 mb-1 font-weight-bold text-warning">{{ trans('panel.expired') }}</div>
                                @elseif(!$student->access_to_purchased_item)
                                    <div class="mt-0 mb-1 font-weight-bold text-danger">{{ trans('update.access_blocked') }}</div>
                                @else
                                    <div class="mt-0 mb-1 font-weight-bold text-success">{{ trans('admin/main.active') }}</div>
                                @endif
                            </td>

                            <td class="text-center mb-2" width="120">
                                @if(!empty($student->id))
                                    {{-- null id => Gift recipient who has not registered yet --}}
                                    @can('admin_users_impersonate')
                                        <a href="{{ getAdminPanelUrl() }}/users/{{ $student->id }}/impersonate" target="_blank" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.login') }}">
                                            <i class="fa fa-user-shield"></i>
                                        </a>
                                    @endcan

                                    @can('admin_users_edit')
                                        <a href="{{ getAdminPanelUrl() }}/users/{{ $student->id }}/edit" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan

                                    @can('admin_webinar_students_delete')
                                        @if(!$student->access_to_purchased_item)
                                            @include('admin.includes.delete_button',[
                                                'url' => getAdminPanelUrl().'/enrollments/'. $student->sale_id .'/enable-access',
                                                'tooltip' => trans('update.enable-student-access'),
                                                'btnIcon' => 'fa-check'
                                            ])
                                        @else
                                            @include('admin.includes.delete_button',[
                                                        'url' => getAdminPanelUrl().'/enrollments/'. $student->sale_id .'/block-access',
                                                        'tooltip' => trans('update.block_access'),
                                                    ])
                                        @endif
                                    @endcan
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="card-footer text-center">
            {{ $students->appends(request()->input())->links() }}
        </div>

    </div>


    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3"><h5>{{trans('admin/main.hints')}}</h5></div>
            <div class="row">
                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.students_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.students_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.students_hint_title_2')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.students_hint_description_2')}}</div>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.students_hint_title_3')}}</div>
                        <div class="text-small font-600-bold">{{trans('admin/main.students_hint_description_3')}}</div>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <div id="addStudentToCourseModal" class="d-none">
        <h3 class="section-title after-line">{{ trans('update.add_student_to_course') }}</h3>
        <div class="mt-25">
            <form action="{{ getAdminPanelUrl() }}/enrollments/store" method="post">
                <input type="hidden" name="webinar_id" value="{{ $webinar->id }}">

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('admin/main.user') }}</label>
                    <select name="user_id" class="form-control user-search" data-placeholder="{{ trans('public.search_user') }}">

                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="d-flex align-items-center justify-content-end mt-3">
                    <button type="button" class="js-save-manual-add btn btn-sm btn-primary">{{ trans('public.save') }}</button>
                    <button type="button" class="close-swl btn btn-sm btn-danger ml-2">{{ trans('public.close') }}</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>


    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
    </script>

    <script src="/assets/default/js/admin/webinar_students.min.js"></script>
@endpush
