@extends('admin.layouts.app')

@push('libraries_top')
    <link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.theme.min.css">

@endpush

@section('content')

    <section class="section">


        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.users_without_purchases')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $usersWithoutPurchases }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-users"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.teachers_without_class')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $teachersWithoutClass }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-star"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.featured_classes')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $featuredClasses }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-percentage"></i></div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{trans('admin/main.active_discounts')}}</h4>
                        </div>
                        <div class="card-body">
                            {{ $activeDiscounts }}
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="row">
            <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{trans('admin/main.net_profit_statistics')}}</h4>
                        <div class="card-header-action">
                            <div class="btn-group">
                                <button type="button" class="js-sale-chart-month btn">{{trans('admin/main.month')}}</button>
                                <button type="button" class="js-sale-chart-year btn btn-primary">{{trans('admin/main.year')}}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="position-relative">
                                    <canvas id="netProfitChart" height="360"></canvas>
                                </div>
                            </div>
                        </div>

                        @if(!empty($getNetProfitStatistics))
                            <div class="statistic-details mt-4 position-relative">
                                <div class="statistic-details-item">
                                    <span class="text-muted">
                                        @if($getNetProfitStatistics['todayIncome']['grow_percent']['status'] == 'up')
                                            <span class="text-primary"><i class="fas fa-caret-up"></i></span>
                                        @else
                                            <span class="text-danger"><i class="fas fa-caret-down"></i></span>
                                        @endif

                                        {{ $getNetProfitStatistics['todayIncome']['grow_percent']['percent'] }}
                                    </span>

                                    <div class="detail-value">{{ handlePrice($getNetProfitStatistics['todayIncome']['amount']) }}</div>
                                    <div class="detail-name">{{trans('admin/main.today_income')}}</div>
                                </div>
                                <div class="statistic-details-item">
                                    <span class="text-muted">
                                        @if($getNetProfitStatistics['weekIncome']['grow_percent']['status'] == 'up')
                                            <span class="text-primary"><i class="fas fa-caret-up"></i></span>
                                        @else
                                            <span class="text-danger"><i class="fas fa-caret-down"></i></span>
                                        @endif

                                        {{ $getNetProfitStatistics['weekIncome']['grow_percent']['percent'] }}
                                    </span>

                                    <div class="detail-value">{{ handlePrice($getNetProfitStatistics['weekIncome']['amount']) }}</div>
                                    <div class="detail-name">{{trans('admin/main.week_income')}}</div>
                                </div>
                                <div class="statistic-details-item">
                                    <span class="text-muted">
                                        @if($getNetProfitStatistics['monthIncome']['grow_percent']['status'] == 'up')
                                            <span class="text-primary"><i class="fas fa-caret-up"></i></span>
                                        @else
                                            <span class="text-danger"><i class="fas fa-caret-down"></i></span>
                                        @endif

                                        {{ $getNetProfitStatistics['monthIncome']['grow_percent']['percent'] }}
                                    </span>

                                    <div class="detail-value">{{ handlePrice($getNetProfitStatistics['monthIncome']['amount']) }}</div>
                                    <div class="detail-name">{{trans('admin/main.month_income')}}</div>
                                </div>
                                <div class="statistic-details-item">
                                    <span class="text-muted">
                                        @if($getNetProfitStatistics['yearIncome']['grow_percent']['status'] == 'up')
                                            <span class="text-primary"><i class="fas fa-caret-up"></i></span>
                                        @else
                                            <span class="text-danger"><i class="fas fa-caret-down"></i></span>
                                        @endif

                                        {{ $getNetProfitStatistics['yearIncome']['grow_percent']['percent'] }}
                                    </span>

                                    <div class="detail-value">{{ handlePrice($getNetProfitStatistics['yearIncome']['amount']) }}</div>
                                    <div class="detail-name">{{trans('admin/main.year_income')}}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{trans('admin/main.classes_statistics')}}</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="classesStatisticsChart" height="490"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{trans('admin/main.top_selling_classes')}}</h4>
                        <div class="card-header-action">
                            <a href="{{ getAdminPanelUrl() }}/webinars?type=course&sort=sales_desc" class="btn btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-center">
                            <table class="table table-striped font-14">

                                <tr>
                                    <th>#</th>
                                    <th class="text-left">{{trans('admin/main.name')}}</th>
                                    <th>{{trans('admin/main.sales')}}</th>
                                    <th>{{trans('admin/main.income')}}</th>
                                </tr>

                                @foreach($getTopSellingClasses as $getTopSellingClass)
                                    <tr>
                                        <td>{{ $getTopSellingClass->id }}</td>
                                        <td>
                                            <a href="{{ $getTopSellingClass->getUrl() }}" target="_blank" class="media-body text-left">
                                                <div>{{ $getTopSellingClass->title }}</div>
                                                <div class="text-primary text-small font-600-bold">{{ trans('webinars.'.$getTopSellingClass->type) }}</div>
                                            </a>
                                        </td>
                                        <td>{{ $getTopSellingClass->sales_count }}</td>
                                        <td>{{ handlePrice($getTopSellingClass->sales_amount) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{trans('admin/main.top_selling_appointments')}}</h4>
                        <div class="card-header-action">
                            <a href="{{ getAdminPanelUrl() }}/consultants?sort=appointments_desc" class="btn btn-sm btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-center">
                            <table class="table table-striped font-14">
                                <tr>
                                    <th>#</th>
                                    <th class="text-left">{{trans('admin/main.consultant')}}</th>
                                    <th>{{trans('admin/main.sales')}}</th>
                                    <th>{{trans('admin/main.income')}}</th>
                                </tr>

                                @foreach($getTopSellingAppointments as $getTopSellingAppointment)
                                    <tr>
                                        <td>{{ $getTopSellingAppointment->creator->id }}</td>
                                        <td class="text-left">{{ $getTopSellingAppointment->creator->full_name }}</td>
                                        <td>{{ $getTopSellingAppointment->sales_count }}</td>
                                        <td>{{ handlePrice($getTopSellingAppointment->sales_amount) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{trans('admin/main.top_selling_instructors')}}</h4>
                        <div class="card-header-action">
                            <a href="{{ getAdminPanelUrl() }}/instructors?sort=sales_classes_desc" class="btn btn-sm btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-center">
                            <table class="table table-striped font-14">
                                <tr>
                                    <th>{{trans('admin/main.id')}}</th>
                                    <th class="text-left">{{trans('admin/main.name')}}</th>
                                    <th>{{trans('admin/main.classes_durations')}}</th>
                                    <th>{{trans('admin/main.sales')}}</th>
                                    <th>{{trans('admin/main.income')}}</th>

                                </tr>

                                @foreach($getTopSellingTeachers as $getTopSellingTeacher)
                                    <tr>
                                        <td>{{ $getTopSellingTeacher->id }}</td>
                                        <td class="text-left">{{ $getTopSellingTeacher->full_name }}</td>
                                        <td>{{ convertMinutesToHourAndMinute($getTopSellingTeacher->classes_durations) }} Hours</td>
                                        <td>{{ $getTopSellingTeacher->sales_count }}</td>
                                        <td>{{ handlePrice($getTopSellingTeacher->sales_amount) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>{{trans('admin/main.top_selling_organizations')}}</h4>
                        <div class="card-header-action">
                            <a href="{{ getAdminPanelUrl() }}/organizations?sort=sales_classes_desc" class="btn btn-sm btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-center">
                            <table class="table table-striped font-14">
                                <tr>
                                    <th>{{trans('admin/main.id')}}</th>
                                    <th class="text-left">{{trans('admin/main.name')}}</th>
                                    <th>{{trans('admin/main.classes_durations')}}</th>
                                    <th>{{trans('admin/main.sales')}}</th>
                                    <th>{{trans('admin/main.income')}}</th>

                                </tr>
                                @foreach($getTopSellingOrganizations as $getTopSellingOrganization)
                                    <tr>
                                        <td>{{ $getTopSellingOrganization->id }}</td>
                                        <td class="text-left">{{ $getTopSellingOrganization->full_name }}</td>
                                        <td>{{ convertMinutesToHourAndMinute($getTopSellingOrganization->classes_durations) }} Hours</td>
                                        <td>{{ $getTopSellingOrganization->sales_count }}</td>
                                        <td>{{ handlePrice($getTopSellingOrganization->sales_amount) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{trans('admin/main.most_active_students')}}</h4>
                        <div class="card-header-action">
                            <a href="{{ getAdminPanelUrl() }}/students?sort=register_desc" class="btn btn-sm btn-primary">{{trans('admin/main.view_more')}}<i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-center">
                            <table class="table table-striped font-14">
                                <tr>
                                    <th>#</th>
                                    <th class="text-left">{{trans('admin/main.name')}}</th>
                                    <th>{{trans('admin/main.purchased_classes')}}</th>
                                    <th>{{trans('admin/main.reserved_appointments')}}</th>
                                    <th>{{trans('admin/main.total_purchased_amount')}}</th>

                                </tr>
                                @foreach($getMostActiveStudents as $getMostActiveStudent)
                                    <tr>
                                        <td>{{ $getMostActiveStudent->id }}</td>
                                        <td class="text-left">{{ $getMostActiveStudent->full_name }}</td>
                                        <td>{{ $getMostActiveStudent->purchased_classes }}</td>
                                        <td>{{ $getMostActiveStudent->reserved_appointments }}</td>
                                        <td>{{ handlePrice($getMostActiveStudent->total_cost) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/chartjs/chart.min.js"></script>
    <script src="/assets/admin/vendor/owl.carousel/owl.carousel.min.js"></script>

    <script src="/assets/admin/js/marketing_dashboard.min.js"></script>

    <script>
        (function ($) {
            "use strict";

            @if(!empty($getClassesStatistics))
            makeClassesStatisticsChart('', @json($getClassesStatistics['labels']),@json($getClassesStatistics['data']));
            @endif

            @if(!empty($getNetProfitChart))
            makeNetProfitChart('Income', @json($getNetProfitChart['labels']),@json($getNetProfitChart['data']));
            @endif
        })(jQuery)
    </script>
@endpush
