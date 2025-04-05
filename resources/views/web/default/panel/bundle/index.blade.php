@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <section>
        <h2 class="section-title">{{ trans('panel.my_activity') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/webinars.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ !empty($bundles) ? $bundlesCount : 0}}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.bundles') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/hours.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ convertMinutesToHourAndMinute($bundlesHours) }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('home.hours') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/sales.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ handlePrice($bundleSalesAmount) }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.bundle_sales') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/download-sales.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $bundleSalesCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.bundle_sales_count') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-25">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ trans('update.my_bundles') }}</h2>
        </div>

        @if(!empty($bundles) and !$bundles->isEmpty())
            @foreach($bundles as $bundle)

                <div class="row mt-30">
                    <div class="col-12">
                        <div class="webinar-card webinar-list d-flex">
                            <div class="image-box">
                                <img src="{{ $bundle->getImage() }}" class="img-cover" alt="">

                                <div class="badges-lists">
                                    @switch($bundle->status)
                                        @case(\App\Models\Bundle::$active)
                                            <span class="badge badge-primary">{{  trans('panel.active') }}</span>
                                            @break
                                        @case(\App\Models\Bundle::$isDraft)
                                            <span class="badge badge-danger">{{ trans('public.draft') }}</span>
                                            @break
                                        @case(\App\Models\Bundle::$pending)
                                            <span class="badge badge-warning">{{ trans('public.waiting') }}</span>
                                            @break
                                        @case(\App\Models\Bundle::$inactive)
                                            <span class="badge badge-danger">{{ trans('public.rejected') }}</span>
                                            @break
                                    @endswitch
                                </div>
                            </div>

                            <div class="webinar-card-body w-100 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ $bundle->getUrl() }}" target="_blank">
                                        <h3 class="font-16 text-dark-blue font-weight-bold">{{ $bundle->title }}</h3>
                                    </a>

                                    @if($authUser->id == $bundle->creator_id or $authUser->id == $bundle->teacher_id)
                                        <div class="btn-group dropdown table-actions">
                                            <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i data-feather="more-vertical" height="20"></i>
                                            </button>
                                            <div class="dropdown-menu ">

                                                @can('panel_bundles_create')
                                                    <a href="/panel/bundles/{{ $bundle->id }}/edit" class="webinar-actions d-block mt-10">{{ trans('public.edit') }}</a>
                                                @endcan

                                                @can('panel_bundles_courses')
                                                    <a href="/panel/bundles/{{ $bundle->id }}/courses" class="webinar-actions d-block mt-10">{{ trans('product.courses') }}</a>
                                                @endcan

                                                @if($authUser->id == $bundle->teacher_id or $authUser->id == $bundle->creator_id)
                                                    @can('panel_bundles_export_students_list')
                                                        <a href="/panel/bundles/{{ $bundle->id }}/export-students-list" class="webinar-actions d-block mt-10">{{ trans('public.export_list') }}</a>
                                                    @endcan
                                                @endif

                                                @if($bundle->creator_id == $authUser->id)
                                                    @can('panel_bundles_delete')
                                                        @include('web.default.panel.includes.content_delete_btn', [
                                                            'deleteContentUrl' => "/panel/bundles/{$bundle->id}/delete",
                                                            'deleteContentClassName' => 'webinar-actions d-block mt-10 text-danger',
                                                            'deleteContentItem' => $bundle,
                                                        ])
                                                    @endcan
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @include(getTemplate() . '.includes.webinar.rate',['rate' => $bundle->getRate()])

                                <div class="webinar-price-box mt-15">
                                    @if($bundle->price > 0)
                                        @if($bundle->bestTicket() < $bundle->price)
                                            <span class="real">{{ handlePrice($bundle->bestTicket(), true, true, false, null, true) }}</span>
                                            <span class="off ml-10">{{ handlePrice($bundle->price, true, true, false, null, true) }}</span>
                                        @else
                                            <span class="real">{{ handlePrice($bundle->price, true, true, false, null, true) }}</span>
                                        @endif
                                    @else
                                        <span class="real">{{ trans('public.free') }}</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between flex-wrap mt-auto">
                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.item_id') }}:</span>
                                        <span class="stat-value">{{ $bundle->id }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.category') }}:</span>
                                        <span class="stat-value">{{ !empty($bundle->category_id) ? $bundle->category->title : '' }}</span>
                                    </div>


                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('public.duration') }}:</span>
                                        <span class="stat-value">{{ convertMinutesToHourAndMinute($bundle->getBundleDuration()) }} Hrs</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('product.courses') }}:</span>
                                        <span class="stat-value">{{ $bundle->bundleWebinars->count() }}</span>
                                    </div>

                                    <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                        <span class="stat-title">{{ trans('panel.sales') }}:</span>
                                        <span class="stat-value">{{ count($bundle->sales) }} ({{ (!empty($bundle->sales) and count($bundle->sales)) ? handlePrice($bundle->sales->sum('amount')) : 0 }})</span>
                                    </div>

                                    @if($authUser->id == $bundle->teacher_id and $authUser->id != $bundle->creator_id and $bundle->creator->isOrganization())
                                        <div class="d-flex align-items-start flex-column mt-20 mr-15">
                                            <span class="stat-title">{{ trans('webinars.organization_name') }}:</span>
                                            <span class="stat-value">{{ $bundle->creator->full_name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="my-30">
                {{ $bundles->appends(request()->input())->links('vendor.pagination.panel') }}
            </div>

        @else
            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'webinar.png',
                'title' => trans('update.you_not_have_any_bundle'),
                'hint' =>  trans('update.no_result_bundle_hint') ,
                'btn' => ['url' => '/panel/bundles/new','text' => trans('update.create_a_bundle') ]
            ])
        @endif

    </section>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

@endpush
