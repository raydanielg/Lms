@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')
    <section>
        <h2 class="section-title">{{ trans('update.orders_statistics') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-6 col-md-3 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/physical_product3.png" width="64" height="64" alt="">
                        <strong class="font-30 font-weight-bold mt-5 text-dark-blue">{{ $totalOrders }}</strong>
                        <span class="font-16 font-weight-500 text-gray">{{ trans('update.total_orders') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/physical_product2.png" width="64" height="64" alt="">
                        <strong class="font-30 font-weight-bold mt-5 text-dark-blue">{{ $pendingOrders }}</strong>
                        <span class="font-16 font-weight-500 text-gray">{{ trans('update.pending_orders') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/physical_product1.png" width="64" height="64" alt="">
                        <strong class="font-30 font-weight-bold mt-5 text-dark-blue">{{ $canceledOrders }}</strong>
                        <span class="font-16 font-weight-500 text-gray">{{ trans('update.canceled_orders') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/33.png" width="64" height="64" alt="">
                        <strong class="font-30 font-weight-bold mt-5 text-dark-blue">{{ (!empty($totalSales) and $totalSales > 0) ? handlePrice($totalSales) : 0 }}</strong>
                        <span class="font-16 font-weight-500 text-gray">{{ trans('financial.total_sales') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="mt-25">
        <h2 class="section-title">{{ trans('update.orders_report') }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <form action="" method="get" class="row">
                <div class="col-12 col-lg-4">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.from') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="dateInputGroupPrepend">
                                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="from" autocomplete="off" class="form-control @if(!empty(request()->get('from'))) datepicker @else datefilter @endif"
                                           aria-describedby="dateInputGroupPrepend"
                                           value="{{  request()->get('from',null)  }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.to') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="dateInputGroupPrepend">
                                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="to" autocomplete="off" class="form-control @if(!empty(request()->get('to'))) datepicker @else datefilter @endif"
                                           aria-describedby="dateInputGroupPrepend"
                                           value="{{  request()->get('to',null)  }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-12 col-lg-5">
                            <div class="form-group">
                                <label class="input-label">{{ trans('update.customer') }}</label>

                                <select name="customer_id" class="form-control select2" data-allow-clear="false">
                                    <option value="all">{{ trans('public.all') }}</option>

                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" @if(request()->get('customer_id',null) == $customer->id) selected @endif>{{ $customer->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.type') }}</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="all"
                                            @if(request()->get('type',null) == 'all') selected="selected" @endif>{{ trans('public.all') }}</option>

                                    @foreach(\App\Models\Product::$productTypes as $productType)
                                        <option value="{{ $productType }}"
                                                @if(request()->get('type',null) == $productType) selected="selected" @endif>{{ trans('update.product_type_'.$productType) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.status') }}</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="all"
                                            @if(request()->get('status',null) == 'all') selected="selected" @endif>{{ trans('public.all') }}</option>

                                    @foreach(\App\Models\ProductOrder::$status as $orderStatus)
                                        @if($orderStatus != 'pending')
                                            <option value="{{ $orderStatus }}"
                                                    @if(request()->get('status',null) == $orderStatus) selected="selected" @endif>{{ trans('update.product_order_status_'.$orderStatus) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-2 d-flex align-items-center justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">{{ trans('public.show_results') }}</button>
                </div>
            </form>
        </div>
    </section>

    @if(!empty($orders) and !$orders->isEmpty())
        <section class="mt-35">
            <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                <h2 class="section-title">{{ trans('update.orders_history') }}</h2>
            </div>

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('update.customer') }}</th>
                                    <th class=" text-left">{{ trans('update.order_id') }}</th>
                                    <th class="text-center">{{ trans('public.price') }}</th>
                                    <th class="text-center">{{ trans('public.discount') }}</th>
                                    <th class="text-center">{{ trans('financial.total_amount') }}</th>
                                    <th class="text-center">{{ trans('financial.income') }}</th>
                                    <th class="text-center">{{ trans('public.type') }}</th>
                                    <th class="text-center">{{ trans('public.status') }}</th>
                                    <th class="text-center">{{ trans('public.date') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($orders as $order)
                                    <tr>
                                        <td class="text-left">
                                            <div class="user-inline-avatar d-flex align-items-center">
                                                <div class="avatar bg-gray200">
                                                    <img src="{{ !empty($order->buyer) ? $order->buyer->getAvatar() : '' }}" class="img-cover" alt="">
                                                </div>
                                                <div class=" ml-5">
                                                    <span class="d-block">{{ !empty($order->buyer) ? $order->buyer->full_name : '' }}</span>
                                                    <span class="mt-5 font-12 text-gray d-block">{{ !empty($order->buyer) ? $order->buyer->email : '' }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class=" text-left">
                                            <span class="d-block font-weight-500 text-dark-blue font-16">{{ $order->id }}</span>
                                            <span class="d-block font-12 text-gray">{{ $order->quantity }} {{ trans('update.product') }}</span>
                                        </td>

                                        <td class="align-middle">
                                            <span>{{ handlePrice($order->sale->amount) }}</span>
                                        </td>
                                        <td class="align-middle">{{ handlePrice($order->sale->discount ?? 0) }}</td>
                                        <td class="align-middle">
                                            <span>{{ handlePrice($order->sale->total_amount) }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <span>{{ handlePrice($order->sale->getIncomeItem()) }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @if(!empty($order) and !empty($order->product))
                                                <span>{{ trans('update.product_type_'.$order->product->type) }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if(!empty($order))
                                                @if($order->status == \App\Models\ProductOrder::$waitingDelivery)
                                                    <span class="text-warning">{{ trans('update.product_order_status_waiting_delivery') }}</span>
                                                @elseif($order->status == \App\Models\ProductOrder::$success)
                                                    <span class="text-dark-blue">{{ trans('update.product_order_status_success') }}</span>
                                                @elseif($order->status == \App\Models\ProductOrder::$shipped)
                                                    <span class="text-warning">{{ trans('update.product_order_status_shipped') }}</span>
                                                @elseif($order->status == \App\Models\ProductOrder::$canceled)
                                                    <span class="text-danger">{{ trans('update.product_order_status_canceled') }}</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <span>{{ dateTimeFormat($order->created_at, 'j M Y H:i') }}</span>
                                        </td>

                                        <td class="text-center align-middle">
                                            @if(!empty($order) and $order->status != \App\Models\ProductOrder::$canceled)
                                                <div class="btn-group dropdown table-actions">
                                                    <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i data-feather="more-vertical" height="20"></i>
                                                    </button>
                                                    <div class="dropdown-menu font-weight-normal">
                                                        <a href="/panel/store/sales/{{ $order->sale_id }}/productOrder/{{ $order->id }}/invoice" class="webinar-actions d-block mt-10" target="_blank">{{ trans('public.invoice') }}</a>

                                                        @if($order->status == \App\Models\ProductOrder::$waitingDelivery)
                                                            <button type="button" data-sale-id="{{ $order->sale_id }}" data-product-order-id="{{ $order->id }}" class="js-enter-tracking-code webinar-actions btn-transparent d-block mt-10">{{ trans('update.enter_tracking_code') }}</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-30">
                {{ $orders->appends(request()->input())->links('vendor.pagination.panel') }}
            </div>

        </section>
    @else
        @include(getTemplate() . '.includes.no-result',[
              'file_name' => 'sales.png',
              'title' => trans('update.product_sales_no_result'),
              'hint' => nl2br(trans('update.product_sales_no_result_hint')),
          ])
    @endif

@endsection

@push('scripts_bottom')
    <script>
        var enterTrackingCodeModalTitleLang = '{{ trans('update.enter_tracking_code') }}';
        var trackingCodeLang = '{{ trans('update.tracking_code') }}';
        var addressLang = '{{ trans('update.address') }}';
        var saveLang = '{{ trans('public.save') }}';
        var closeLang = '{{ trans('public.close') }}';
        var trackingCodeSaveSuccessLang = '{{ trans('update.tracking_code_success_save') }}';
    </script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/js/panel/store/sale.min.js"></script>
@endpush
