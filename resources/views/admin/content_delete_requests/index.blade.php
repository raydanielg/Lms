@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a></div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>


        <div class="section-body">

            <section class="card">
                <div class="card-body">
                    <form action="{{ getAdminPanelUrl("/content-delete-requests") }}" method="get" class="row mb-0">
                        <div class="col-12 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.search') }}</label>
                                <input type="text" name="search" class="form-control" value="{{ request()->get('search',null) }}"/>
                            </div>
                        </div>

                        <div class="col-12 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.content_type') }}</label>

                                <select name="content_type" class="form-control">
                                    <option value="">{{ trans('admin/main.all') }}</option>
                                    <option value="course" {{ (request()->get('content_type') == "course") ? 'selected' : '' }}>{{ trans('update.course') }}</option>
                                    <option value="bundle" {{ (request()->get('content_type') == "bundle") ? 'selected' : '' }}>{{ trans('update.bundle') }}</option>
                                    <option value="product" {{ (request()->get('content_type') == "product") ? 'selected' : '' }}>{{ trans('update.product') }}</option>
                                    <option value="post" {{ (request()->get('content_type') == "post") ? 'selected' : '' }}>{{ trans('update.post') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="input-label">{{trans('admin/main.start_date')}}</label>
                                <div class="input-group">
                                    <input type="date" id="fsdate" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="input-label">{{trans('admin/main.end_date')}}</label>
                                <div class="input-group">
                                    <input type="date" id="lsdate" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-2">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.status') }}</label>

                                <select name="status" class="form-control">
                                    <option value="">{{ trans('admin/main.all') }}</option>
                                    <option value="pending" {{ (request()->get('status') == "pending") ? 'selected' : '' }}>{{ trans('admin/main.pending') }}</option>
                                    <option value="approved" {{ (request()->get('status') == "approved") ? 'selected' : '' }}>{{ trans('admin/main.approved') }}</option>
                                    <option value="rejected" {{ (request()->get('status') == "rejected") ? 'selected' : '' }}>{{ trans('admin/main.rejected') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-2 d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn btn-primary w-100">{{ trans('admin/main.show_results') }}</button>
                        </div>
                    </form>
                </div>
            </section>


            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14 ">
                                    <tr>
                                        <th class="text-left">{{trans('admin/main.content')}}</th>
                                        <th class="text-left">{{trans('admin/main.instructor')}}</th>
                                        <th class="text-center">{{trans('update.customers')}}</th>
                                        <th class="text-center">{{ trans('admin/main.sales') }}</th>
                                        <th class="text-center">{{ trans('product.reason') }}</th>
                                        <th class="text-center">{{ trans('update.published_date') }}</th>
                                        <th class="text-center">{{ trans('update.request_date') }}</th>
                                        <th class="text-center">{{ trans('admin/main.status') }}</th>
                                        <th class="text-right" width="120">{{trans('admin/main.actions')}}</th>
                                    </tr>

                                    @foreach($requests as $request)
                                        @php
                                            $contentItem = $request->getContentItem();
                                            $contentType = $request->getContentType();

                                            $contentItemTitle = null;
                                            $customers = null;
                                            $sales = null;
                                            $publishedDate = null;

                                            if (!empty($contentItem)) {
                                                $contentItemTitle = $contentItem->title;
                                                $publishedDate = $contentItem->created_at;

                                                if ($contentType == "course" or $contentType == "bundle") {
                                                    $sales = $contentItem->sales->whereNull('refund_at')->sum('total_amount');
                                                    $customers = $contentItem->sales->whereNull('refund_at')->count();
                                                } elseif ($contentType == "product") {
                                                    $sales = $contentItem->sales()->sum('total_amount');
                                                    $customers = $contentItem->salesCount();
                                                }
                                            } else {
                                                $contentItemTitle = $request->content_title;
                                                $customers = $request->customers_count;
                                                $sales = $request->sales;
                                                $publishedDate = $request->content_published_date;
                                            }
                                        @endphp

                                        <tr>
                                            <th class="text-left">
                                                <span class="d-block">{{ $contentItemTitle }}</span>

                                                @if(!empty($contentType))
                                                    <span class="d-block font-12 text-gray mt-1">{{ trans('update.'.$contentType) }}</span>
                                                @endif
                                            </th>

                                            <th class="text-left">
                                                <span class="d-block">{{ $request->user->full_name }}</span>

                                                @if(!empty($request->user->email))
                                                    <span class="d-block font-12 text-gray mt-1">{{ $request->user->email }}</span>
                                                @endif

                                                @if(!empty($request->user->mobile))
                                                    <span class="d-block font-12 text-gray mt-1">{{ $request->user->mobile }}</span>
                                                @endif
                                            </th>

                                            <td class=" text-center align-middle">
                                                @if(!empty($customers))
                                                    {{ $customers }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td class=" text-center align-middle">
                                                @if(!empty($sales))
                                                    {{ handlePrice($sales) }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td class="align-middle">
                                                <input type="hidden" class="" value="{{ $request->description }}">
                                                <button type="button" class="js-show-description btn btn-outline-primary">{{ trans('admin/main.show') }}</button>
                                            </td>

                                            <td class="align-middle">
                                                @if(!empty($publishedDate))
                                                    {{ dateTimeFormat($publishedDate, 'j M Y | H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td class="align-middle">{{ dateTimeFormat($request->created_at,'j M Y | H:i') }}</td>

                                            <td class="align-middle">
                                                {{ trans('admin/main.'.$request->status) }}
                                            </td>

                                            <td class="align-middle text-right">

                                                @can('admin_content_delete_requests_actions')
                                                    @if($request->status == "pending")
                                                        @include('admin.includes.delete_button',[
                                                                'url' => getAdminPanelUrl("/content-delete-requests/{$request->id}/approve"),
                                                                'btnClass' => 'text-primary text-decoration-none btn-transparent btn-sm mr-1',
                                                                'btnText' => '<i class="fa fa-check"></i>',
                                                                'tooltip' => trans("admin/main.approve"),
                                                                ])

                                                        @include('admin.includes.delete_button',[
                                                        'url' => getAdminPanelUrl("/content-delete-requests/{$request->id}/reject"),
                                                        'btnClass' => 'text-danger text-decoration-none btn-transparent btn-sm',
                                                        'btnText' => '<i class="fa fa-times"></i>',
                                                        'tooltip' => trans("admin/main.reject"),
                                                        ])
                                                    @else
                                                        -
                                                    @endif
                                                @endcan

                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $requests->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="contactMessage" tabindex="-1" aria-labelledby="contactMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactMessageLabel">{{ trans('admin/main.message') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/content-delete-requests.min.js"></script>
@endpush
