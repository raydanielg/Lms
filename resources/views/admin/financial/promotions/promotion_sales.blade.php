@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.promotion_sales') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.promotion_sales') }}</div>
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
                                        <th>{{ trans('admin/main.title') }}</th>
                                        <th class="text-left">{{ trans('admin/main.full_name') }}</th>
                                        <th class="text-left">{{ trans('admin/main.webinar') }}</th>
                                        <th class="text-center">{{ trans('admin/main.created_at') }}</th>
                                    </tr>

                                    @foreach($promotionSales as $promotionSale)
                                        <tr>
                                            <td class="text-center">{{ !empty($promotionSale->promotion) ? $promotionSale->promotion->title : trans('update.deleted_promotion') }}</td>
                                            <td class="text-left">{{ !empty($promotionSale->buyer) ? $promotionSale->buyer->full_name : trans('update.deleted_user') }}</td>
                                            <td class="text-left">
                                                @if(!empty($promotionSale->webinar))
                                                    <a href="{{ $promotionSale->webinar->getUrl() }}" target="_blank">{{ $promotionSale->webinar->title }}</a>
                                                @else
                                                    {{ trans('update.deleted_item') }}
                                                @endif
                                            </td>
                                            <td class="text-center">{{ dateTimeFormat($promotionSale->created_at, 'Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $promotionSales->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

