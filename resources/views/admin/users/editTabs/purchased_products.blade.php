<div class="tab-pane mt-3 fade" id="purchased_products" role="tabpanel" aria-labelledby="purchased_products-tab">
    <div class="row">

        @can('admin_enrollment_add_student_to_items')
            <div class="col-12 col-md-6">
                <h5 class="section-title after-line">{{ trans('update.add_student_to_product') }}</h5>

                <form action="{{ getAdminPanelUrl() }}/enrollments/store" method="Post">

                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <div class="form-group">
                        <label class="input-label">{{trans('update.product')}}</label>
                        <select name="product_id" class="form-control search-product-select2"
                                data-placeholder="{{ trans('update.search_product') }}">

                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class=" mt-4">
                        <button type="button" class="js-save-manual-add btn btn-primary">{{ trans('admin/main.submit') }}</button>
                    </div>
                </form>
            </div>
        @endcan

        <div class="col-12">
            <div class="mt-5">
                <h5 class="section-title after-line">{{ trans('update.manual_added_products') }}</h5>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-md">
                        <tr>
                            <th>{{ trans('update.product') }}</th>
                            <th>{{ trans('admin/main.type') }}</th>
                            <th>{{ trans('admin/main.price') }}</th>
                            <th>{{ trans('update.seller') }}</th>
                            <th class="text-center">{{ trans('update.added_date') }}</th>
                            <th class="text-right">{{ trans('admin/main.actions') }}</th>
                        </tr>

                        @if(!empty($manualAddedProducts))
                            @foreach($manualAddedProducts as $manualAddedProduct)

                                <tr>
                                    <td width="25%">
                                        <a href="{{ !empty($manualAddedProduct->productOrder->product) ? $manualAddedProduct->productOrder->product->getUrl() : '#!' }}" target="_blank" class="">{{ !empty($manualAddedProduct->productOrder->product) ? $manualAddedProduct->productOrder->product->title : trans('update.deleted_item') }}</a>
                                    </td>

                                    <td>
                                        @if(!empty($manualAddedProduct->productOrder->product))
                                            {{ trans('update.product_type_'.$manualAddedProduct->productOrder->product->type) }}
                                        @endif
                                    </td>

                                    <td>
                                        @if(!empty($manualAddedProduct->productOrder->product))
                                            {{ !empty($manualAddedProduct->productOrder->product->price) ? handlePrice($manualAddedProduct->productOrder->product->price) : '-' }}
                                        @else
                                            {{ !empty($manualAddedProduct->amount) ? handlePrice($manualAddedProduct->amount) : '-' }}
                                        @endif
                                    </td>

                                    <td width="25%">
                                        @if(!empty($manualAddedProduct->productOrder->product))
                                            <p>{{ $manualAddedProduct->productOrder->product->creator->full_name  }}</p>
                                        @else
                                            <p>{{ !empty($manualAddedProduct->seller) ? $manualAddedProduct->seller->full_name : trans('update.deleted_seller')  }}</p>
                                        @endif
                                    </td>

                                    <td class="text-center">{{ dateTimeFormat($manualAddedProduct->created_at,'j M Y | H:i') }}</td>
                                    <td class="text-right">
                                        @can('admin_enrollment_block_access')
                                            @include('admin.includes.delete_button',[
                                                    'url' => getAdminPanelUrl().'/enrollments/'. $manualAddedProduct->id .'/block-access',
                                                    'tooltip' => trans('update.block_access'),
                                                    'btnIcon' => 'fa-times-circle'
                                                ])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                    <p class="font-12 text-gray mt-1 mb-0">{{ trans('update.manual_add_hint') }}</p>
                </div>
            </div>
        </div>


        <div class="col-12">
            <div class="mt-5">
                <h5 class="section-title after-line">{{ trans('update.manual_disabled_products') }}</h5>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-md">
                        <tr>
                            <th>{{ trans('update.product') }}</th>
                            <th>{{ trans('admin/main.type') }}</th>
                            <th>{{ trans('admin/main.price') }}</th>
                            <th>{{ trans('update.seller') }}</th>
                            <th class="text-right">{{ trans('admin/main.actions') }}</th>
                        </tr>

                        @if(!empty($manualDisabledProducts))
                            @foreach($manualDisabledProducts as $manualDisabledProduct)

                                <tr>
                                    <td width="25%">
                                        <a href="{{ !empty($manualDisabledProduct->productOrder->product) ? $manualDisabledProduct->productOrder->product->getUrl() : '#!' }}" target="_blank" class="">{{ !empty($manualDisabledProduct->productOrder->product) ? $manualDisabledProduct->productOrder->product->title : trans('update.deleted_item') }}</a>
                                    </td>

                                    <td>
                                        @if(!empty($manualDisabledProduct->productOrder->product))
                                            {{ trans('update.product_type_'.$manualDisabledProduct->productOrder->product->type) }}
                                        @endif
                                    </td>

                                    <td>
                                        @if(!empty($manualDisabledProduct->productOrder->product))
                                            {{ !empty($manualDisabledProduct->productOrder->product->price) ? handlePrice($manualDisabledProduct->productOrder->product->price) : '-' }}
                                        @else
                                            {{ !empty($manualDisabledProduct->amount) ? handlePrice($manualDisabledProduct->amount) : '-' }}
                                        @endif
                                    </td>

                                    <td width="25%">
                                        @if(!empty($manualDisabledProduct->productOrder->product))
                                            <p>{{ $manualDisabledProduct->productOrder->product->creator->full_name  }}</p>
                                        @else
                                            <p>{{ $manualDisabledProduct->seller->full_name  }}</p>
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        @can('admin_enrollment_block_access')
                                            @include('admin.includes.delete_button',[
                                                    'url' => getAdminPanelUrl().'/enrollments/'. $manualDisabledProduct->id .'/enable-access',
                                                    'tooltip' => trans('update.enable-student-access'),
                                                ])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                    <p class="font-12 text-gray mt-1 mb-0">{{ trans('update.manual_remove_hint') }}</p>
                </div>
            </div>
        </div>


        <div class="col-12">
            <div class="mt-5">
                <h5 class="section-title after-line">{{ trans('panel.purchased') }}</h5>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-md">
                        <tr>
                            <th>{{ trans('update.product') }}</th>
                            <th>{{ trans('admin/main.type') }}</th>
                            <th>{{ trans('admin/main.price') }}</th>
                            <th>{{ trans('update.seller') }}</th>
                            <th class="text-center">{{ trans('panel.purchase_date') }}</th>
                            <th>{{ trans('admin/main.actions') }}</th>
                        </tr>

                        @if(!empty($purchasedProducts))
                            @foreach($purchasedProducts as $purchasedProduct)
                                <tr>
                                    <td width="25%">
                                        <a href="{{ !empty($purchasedProduct->productOrder->product) ? $purchasedProduct->productOrder->product->getUrl() : '#!' }}" target="_blank" class="">{{ !empty($purchasedProduct->productOrder->product) ? $purchasedProduct->productOrder->product->title : trans('update.deleted_item') }}</a>
                                    </td>
                                    <td>
                                        @if(!empty($purchasedProduct->productOrder->product))
                                            {{ trans('update.product_type_'.$purchasedProduct->productOrder->product->type) }}
                                        @endif
                                    </td>

                                    <td>
                                        @if(!empty($purchasedProduct->productOrder->product))
                                            {{ !empty($purchasedProduct->productOrder->product->price) ? handlePrice($purchasedProduct->productOrder->product->price) : '-' }}
                                        @else
                                            {{ !empty($purchasedProduct->amount) ? handlePrice($purchasedProduct->amount) : '-' }}
                                        @endif
                                    </td>

                                    <td width="25%">
                                        @if(!empty($purchasedProduct->productOrder->product))
                                            <p>{{ $purchasedProduct->productOrder->product->creator->full_name  }}</p>
                                        @else
                                            <p>{{ !empty($purchasedProduct->seller) ? $purchasedProduct->seller->full_name : trans('update.deleted_seller')  }}</p>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ dateTimeFormat($purchasedProduct->created_at,'j M Y | H:i') }}
                                    </td>

                                    <td class="text-right">
                                        @can('admin_enrollment_block_access')
                                            @include('admin.includes.delete_button',[
                                                    'url' => getAdminPanelUrl().'/enrollments/'. $purchasedProduct->id .'/block-access',
                                                    'tooltip' => trans('update.block_access'),
                                                    'btnIcon' => 'fa-times-circle'
                                                ])
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                    <p class="font-12 text-gray mt-1 mb-0">{{ trans('update.purchased_hint') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
