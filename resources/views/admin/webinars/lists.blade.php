@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.type_'.$classesType.'s') }} {{trans('admin/main.list')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{trans('admin/main.classes')}}</div>

                <div class="breadcrumb-item">{{ trans('admin/main.type_'.$classesType.'s') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-file-video"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{trans('admin/main.total')}} {{ trans('admin/main.type_'.$classesType.'s') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalWebinars }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-eye"></i>
                        </div>

                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{trans('admin/main.pending_review')}} {{ trans('admin/main.type_'.$classesType.'s') }}</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalPendingWebinars }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($classesType == 'webinar')
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info">
                                <i class="fas fa-history"></i>
                            </div>

                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{trans('admin/main.inprogress_live_classes')}}</h4>
                                </div>
                                <div class="card-body">
                                    {{ $inProgressWebinars }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info">
                                <i class="fas fa-history"></i>
                            </div>

                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>{{trans('admin/main.total_durations')}}</h4>
                                </div>
                                <div class="card-body">
                                    {{ convertMinutesToHourAndMinute($totalDurations) }} {{ trans('home.hours') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-dollar-sign"></i></div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>{{trans('admin/main.total_sales')}}</h4>
                            </div>
                            <div class="card-body">
                                {{ $totalSales }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">
                        <input type="hidden" name="type" value="{{ request()->get('type') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.search')}}</label>
                                    <input name="title" type="text" class="form-control" value="{{ request()->get('title') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.start_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="from" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.end_date')}}</label>
                                    <div class="input-group">
                                        <input type="date" id="to" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.filters')}}</label>
                                    <select name="sort" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.filter_type')}}</option>
                                        <option value="has_discount" @if(request()->get('sort') == 'has_discount') selected @endif>{{trans('admin/main.discounted_classes')}}</option>
                                        <option value="sales_asc" @if(request()->get('sort') == 'sales_asc') selected @endif>{{trans('admin/main.sales_ascending')}}</option>
                                        <option value="sales_desc" @if(request()->get('sort') == 'sales_desc') selected @endif>{{trans('admin/main.sales_descending')}}</option>
                                        <option value="price_asc" @if(request()->get('sort') == 'price_asc') selected @endif>{{trans('admin/main.Price_ascending')}}</option>
                                        <option value="price_desc" @if(request()->get('sort') == 'price_desc') selected @endif>{{trans('admin/main.Price_descending')}}</option>
                                        <option value="income_asc" @if(request()->get('sort') == 'income_asc') selected @endif>{{trans('admin/main.Income_ascending')}}</option>
                                        <option value="income_desc" @if(request()->get('sort') == 'income_desc') selected @endif>{{trans('admin/main.Income_descending')}}</option>
                                        <option value="created_at_asc" @if(request()->get('sort') == 'created_at_asc') selected @endif>{{trans('admin/main.create_date_ascending')}}</option>
                                        <option value="created_at_desc" @if(request()->get('sort') == 'created_at_desc') selected @endif>{{trans('admin/main.create_date_descending')}}</option>
                                        <option value="updated_at_asc" @if(request()->get('sort') == 'updated_at_asc') selected @endif>{{trans('admin/main.update_date_ascending')}}</option>
                                        <option value="updated_at_desc" @if(request()->get('sort') == 'updated_at_desc') selected @endif>{{trans('admin/main.update_date_descending')}}</option>
                                        <option value="public_courses" @if(request()->get('sort') == 'public_courses') selected @endif>{{trans('update.public_courses')}}</option>
                                        <option value="courses_private" @if(request()->get('sort') == 'courses_private') selected @endif>{{trans('update.courses_private')}}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.instructor')}}</label>
                                    <select name="teacher_ids[]" multiple="multiple" data-search-option="just_teacher_role" class="form-control search-user-select2"
                                            data-placeholder="Search teachers">

                                        @if(!empty($teachers) and $teachers->count() > 0)
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}" selected>{{ $teacher->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.category')}}</label>
                                    <select name="category_id" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.all_categories')}}</option>

                                        @foreach($categories as $category)
                                            @if(!empty($category->subCategories) and count($category->subCategories))
                                                <optgroup label="{{  $category->title }}">
                                                    @foreach($category->subCategories as $subCategory)
                                                        <option value="{{ $subCategory->id }}" @if(request()->get('category_id') == $subCategory->id) selected="selected" @endif>{{ $subCategory->title }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                <option value="{{ $category->id }}" @if(request()->get('category_id') == $category->id) selected="selected" @endif>{{ $category->title }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{trans('admin/main.status')}}</label>
                                    <select name="status" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{trans('admin/main.all_status')}}</option>
                                        <option value="pending" @if(request()->get('status') == 'pending') selected @endif>{{trans('admin/main.pending_review')}}</option>
                                        @if($classesType == 'webinar')
                                            <option value="active_not_conducted" @if(request()->get('status') == 'active_not_conducted') selected @endif>{{trans('admin/main.publish_not_conducted')}}</option>
                                            <option value="active_in_progress" @if(request()->get('status') == 'active_in_progress') selected @endif>{{trans('admin/main.publish_inprogress')}}</option>
                                            <option value="active_finished" @if(request()->get('status') == 'active_finished') selected @endif>{{trans('admin/main.publish_finished')}}</option>
                                        @else
                                            <option value="active" @if(request()->get('status') == 'active') selected @endif>{{trans('admin/main.published')}}</option>
                                        @endif
                                        <option value="inactive" @if(request()->get('status') == 'inactive') selected @endif>{{trans('admin/main.rejected')}}</option>
                                        <option value="is_draft" @if(request()->get('status') == 'is_draft') selected @endif>{{trans('admin/main.draft')}}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group mt-1">
                                    <label class="input-label mb-4"> </label>
                                    <input type="submit" class="text-center btn btn-primary w-100" value="{{trans('admin/main.show_results')}}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @can('admin_webinars_export_excel')
                                <div class="text-right">
                                    <a href="{{ getAdminPanelUrl() }}/webinars/excel?{{ http_build_query(request()->all()) }}" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                                </div>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="">
                                <table class="table table-striped font-14 ">
                                    <tr>
                                        <th>{{trans('admin/main.id')}}</th>
                                        <th class="text-left">{{trans('admin/main.title')}}</th>
                                        <th class="text-left">{{trans('admin/main.instructor')}}</th>
                                        <th>{{trans('admin/main.price')}}</th>
                                        <th>{{trans('admin/main.sales')}}</th>
                                        <th>{{trans('admin/main.income')}}</th>
                                        <th>{{trans('admin/main.students_count')}}</th>
                                        <th>{{trans('admin/main.created_at')}}</th>
                                        @if($classesType == 'webinar')
                                            <th>{{trans('admin/main.start_date')}}</th>
                                        @else
                                            <th>{{trans('admin/main.updated_at')}}</th>
                                        @endif
                                        <th>{{trans('admin/main.status')}}</th>
                                        <th width="120">{{trans('admin/main.actions')}}</th>
                                    </tr>

                                    @foreach($webinars as $webinar)
                                        <tr class="text-center">
                                            <td>{{ $webinar->id }}</td>
                                            <td width="18%" class="text-left">
                                                <a class="text-primary mt-0 mb-1 font-weight-bold" href="{{ $webinar->getUrl() }}">{{ $webinar->title }}</a>
                                                @if(!empty($webinar->category->title))
                                                    <div class="text-small">{{ $webinar->category->title }}</div>
                                                @else
                                                    <div class="text-small text-warning">{{trans('admin/main.no_category')}}</div>
                                                @endif
                                            </td>

                                            <td class="text-left">{{ $webinar->teacher->full_name }}</td>

                                            <td>
                                                @if(!empty($webinar->price) and $webinar->price > 0)
                                                    <span class="mt-0 mb-1">
                                                        {{ handlePrice($webinar->price, true, true) }}
                                                    </span>

                                                    @if($webinar->getDiscountPercent() > 0)
                                                        <div class="text-danger text-small font-600-bold">{{ $webinar->getDiscountPercent() }}% {{trans('admin/main.off')}}</div>
                                                    @endif
                                                @else
                                                    {{ trans('public.free') }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-primary mt-0 mb-1 font-weight-bold">
                                                    {{ $webinar->sales->count() }}
                                                </span>

                                                @if(!empty($webinar->capacity))
                                                    <div class="text-small font-600-bold">{{trans('admin/main.capacity')}} : {{ $webinar->getWebinarCapacity() }}</div>
                                                @endif
                                            </td>

                                            <td>{{ handlePrice($webinar->sales->sum('total_amount')) }}</td>

                                            <td class="font-12">
                                                <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/students" target="_blank" class="">{{ $webinar->sales->count() }}</a>
                                            </td>

                                            <td class="font-12">{{ dateTimeFormat($webinar->created_at, 'Y M j | H:i') }}</td>

                                            @if($classesType == 'webinar')
                                                <td class="font-12">{{ dateTimeFormat($webinar->start_date, 'Y M j | H:i') }}</td>
                                            @else
                                                <td class="font-12">{{ dateTimeFormat($webinar->updated_at, 'Y M j | H:i') }}</td>
                                            @endif

                                            <td>
                                                @switch($webinar->status)
                                                    @case(\App\Models\Webinar::$active)
                                                        <div class="text-success font-600-bold">{{ trans('admin/main.published') }}</div>
                                                        @if($webinar->isWebinar())
                                                            @if($webinar->start_date > time())
                                                                <div class="text-danger text-small">({{  trans('admin/main.not_conducted') }})</div>
                                                            @elseif($webinar->isProgressing())
                                                                <div class="text-warning text-small">({{  trans('webinars.in_progress') }})</div>
                                                            @else
                                                                <div class="text-success text-small">({{ trans('public.finished') }})</div>
                                                            @endif
                                                        @endif
                                                        @break
                                                    @case(\App\Models\Webinar::$isDraft)
                                                        <span class="text-dark">{{ trans('admin/main.is_draft') }}</span>
                                                        @break
                                                    @case(\App\Models\Webinar::$pending)
                                                        <span class="text-warning">{{ trans('admin/main.waiting') }}</span>
                                                        @break
                                                    @case(\App\Models\Webinar::$inactive)
                                                        <span class="text-danger">{{ trans('public.rejected') }}</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td width="200" class="">
                                                <div class="btn-group dropdown table-actions">
                                                    <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-left text-left webinars-lists-dropdown">

                                                        @can('admin_webinars_edit')
                                                            @if(in_array($webinar->status, [\App\Models\Webinar::$pending, \App\Models\Webinar::$inactive]))
                                                                @include('admin.includes.delete_button',[
                                                                    'url' => getAdminPanelUrl().'/webinars/'.$webinar->id.'/approve',
                                                                    'btnClass' => 'd-flex align-items-center text-success text-decoration-none btn-transparent btn-sm mt-1',
                                                                    'btnText' => '<i class="fa fa-check"></i><span class="ml-2">'. trans("admin/main.approve") .'</span>'
                                                                    ])
                                                            @endif

                                                            @if($webinar->status == \App\Models\Webinar::$pending)
                                                                @include('admin.includes.delete_button',[
                                                                    'url' => getAdminPanelUrl().'/webinars/'.$webinar->id.'/reject',
                                                                    'btnClass' => 'd-flex align-items-center text-danger text-decoration-none btn-transparent btn-sm mt-1',
                                                                    'btnText' => '<i class="fa fa-times"></i><span class="ml-2">'. trans("admin/main.reject") .'</span>'
                                                                    ])
                                                            @endif

                                                            @if($webinar->status == \App\Models\Webinar::$active)
                                                                @include('admin.includes.delete_button',[
                                                                    'url' => getAdminPanelUrl().'/webinars/'.$webinar->id.'/unpublish',
                                                                    'btnClass' => 'd-flex align-items-center text-danger text-decoration-none btn-transparent btn-sm mt-1',
                                                                    'btnText' => '<i class="fa fa-times"></i><span class="ml-2">'. trans("admin/main.unpublish") .'</span>'
                                                                    ])
                                                            @endif
                                                        @endcan


                                                        @can('admin_webinar_notification_to_students')
                                                            <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/sendNotification" target="_blank" class="d-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm text-primary mt-1 ">
                                                                <i class="fa fa-bell"></i>
                                                                <span class="ml-2">{{ trans('notification.send_notification') }}</span>
                                                            </a>
                                                        @endcan

                                                        @can('admin_webinar_students_lists')
                                                            <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/students" target="_blank" class="d-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm text-primary mt-1 " title="{{ trans('admin/main.students') }}">
                                                                <i class="fa fa-users"></i>
                                                                <span class="ml-2">{{ trans('admin/main.students') }}</span>
                                                            </a>
                                                        @endcan

                                                        @can('admin_webinar_statistics')
                                                            <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/statistics" target="_blank" class="d-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm text-primary mt-1 " title="{{ trans('admin/main.students') }}">
                                                                <i class="fa fa-chart-pie"></i>
                                                                <span class="ml-2">{{ trans('update.statistics') }}</span>
                                                            </a>
                                                        @endcan

                                                        @can('admin_support_send')
                                                            <a href="{{ getAdminPanelUrl() }}/supports/create?user_id={{ $webinar->teacher->id }}" target="_blank" class="d-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm text-primary mt-1" title="{{ trans('admin/main.send_message_to_teacher') }}">
                                                                <i class="fa fa-comment"></i>
                                                                <span class="ml-2">{{ trans('site.send_message') }}</span>
                                                            </a>
                                                        @endcan

                                                        @can('admin_webinars_edit')
                                                            <a href="{{ getAdminPanelUrl() }}/webinars/{{ $webinar->id }}/edit" target="_blank" class="d-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm text-primary mt-1 " title="{{ trans('admin/main.edit') }}">
                                                                <i class="fa fa-edit"></i>
                                                                <span class="ml-2">{{ trans('admin/main.edit') }}</span>
                                                            </a>
                                                        @endcan

                                                        @can('admin_webinars_delete')
                                                            @include('admin.includes.delete_button',[
                                                                    'url' => getAdminPanelUrl().'/webinars/'.$webinar->id.'/delete',
                                                                    'btnClass' => 'd-flex align-items-center text-dark text-decoration-none btn-transparent btn-sm mt-1',
                                                                    'btnText' => '<i class="fa fa-times"></i><span class="ml-2">'. trans("admin/main.delete") .'</span>'
                                                                    ])
                                                        @endcan
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $webinars->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
