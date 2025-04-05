<div class="tab-pane mt-3 fade" id="purchased_courses" role="tabpanel" aria-labelledby="purchased_courses-tab">
    <div class="row">

        @can('admin_enrollment_add_student_to_items')
            <div class="col-12 col-md-6">
                <h5 class="section-title after-line">{{ trans('update.add_student_to_course') }}</h5>

                <form action="{{ getAdminPanelUrl() }}/enrollments/store" method="Post">

                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <div class="form-group">
                        <label class="input-label">{{trans('admin/main.class')}}</label>
                        <select name="webinar_id" class="form-control search-webinar-select2"
                                data-placeholder="{{trans('panel.choose_webinar')}}">

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
                <h5 class="section-title after-line">{{ trans('update.manual_added') }}</h5>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-md">
                        <tr>
                            <th>{{ trans('admin/main.class') }}</th>
                            <th>{{ trans('admin/main.type') }}</th>
                            <th>{{ trans('admin/main.price') }}</th>
                            <th>{{ trans('admin/main.instructor') }}</th>
                            <th class="text-center">{{ trans('update.added_date') }}</th>
                            <th class="text-right">{{ trans('admin/main.actions') }}</th>
                        </tr>

                        @if(!empty($manualAddedClasses))
                            @foreach($manualAddedClasses as $manualAddedClass)

                                <tr>
                                    <td width="25%">
                                        <a href="{{ !empty($manualAddedClass->webinar) ? $manualAddedClass->webinar->getUrl() : '#1' }}" target="_blank" class="">{{ !empty($manualAddedClass->webinar) ? $manualAddedClass->webinar->title : trans('update.deleted_item') }}</a>
                                    </td>

                                    <td>
                                        @if(!empty($manualAddedClass->webinar))
                                            {{ trans('admin/main.'.$manualAddedClass->webinar->type) }}
                                        @endif
                                    </td>

                                    <td>
                                        @if(!empty($manualAddedClass->webinar))
                                            {{ !empty($manualAddedClass->webinar->price) ? handlePrice($manualAddedClass->webinar->price) : '-' }}
                                        @else
                                            {{ !empty($manualAddedClass->amount) ? handlePrice($manualAddedClass->amount) : '-' }}
                                        @endif
                                    </td>

                                    <td width="25%">
                                        @if(!empty($manualAddedClass->webinar))
                                            <p>{{ $manualAddedClass->webinar->creator->full_name  }}</p>
                                        @else
                                            <p>{{ $manualAddedClass->seller->full_name  }}</p>
                                        @endif
                                    </td>

                                    <td class="text-center">{{ dateTimeFormat($manualAddedClass->created_at,'j M Y | H:i') }}</td>
                                    <td class="text-right">
                                        @can('admin_enrollment_block_access')
                                            @include('admin.includes.delete_button',[
                                                    'url' => getAdminPanelUrl().'/enrollments/'. $manualAddedClass->id .'/block-access',
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
                <h5 class="section-title after-line">{{ trans('update.manual_disabled') }}</h5>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-md">
                        <tr>
                            <th>{{ trans('admin/main.class') }}</th>
                            <th>{{ trans('admin/main.type') }}</th>
                            <th>{{ trans('admin/main.price') }}</th>
                            <th>{{ trans('admin/main.instructor') }}</th>
                            <th class="text-right">{{ trans('admin/main.actions') }}</th>
                        </tr>

                        @if(!empty($manualDisabledClasses))
                            @foreach($manualDisabledClasses as $manualDisabledClass)

                                <tr>
                                    <td width="25%">
                                        <a href="{{ !empty($manualDisabledClass->webinar) ? $manualDisabledClass->webinar->getUrl() : '#1' }}" target="_blank" class="">{{ !empty($manualDisabledClass->webinar) ? $manualDisabledClass->webinar->title : trans('update.deleted_item') }}</a>
                                    </td>

                                    <td>
                                        @if(!empty($manualDisabledClass->webinar))
                                            {{ trans('admin/main.'.$manualDisabledClass->webinar->type) }}
                                        @endif
                                    </td>

                                    <td>
                                        @if(!empty($manualDisabledClass->webinar))
                                            {{ !empty($manualDisabledClass->webinar->price) ? handlePrice($manualDisabledClass->webinar->price) : '-' }}
                                        @else
                                            {{ !empty($manualDisabledClass->amount) ? handlePrice($manualDisabledClass->amount) : '-' }}
                                        @endif
                                    </td>

                                    <td width="25%">
                                        @if(!empty($manualDisabledClass->webinar))
                                            <p>{{ $manualDisabledClass->webinar->creator->full_name  }}</p>
                                        @else
                                            <p>{{ $manualDisabledClass->seller->full_name  }}</p>
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        @can('admin_enrollment_block_access')
                                            @include('admin.includes.delete_button',[
                                                    'url' => getAdminPanelUrl().'/enrollments/'. $manualDisabledClass->id .'/enable-access',
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
                            <th>{{ trans('admin/main.class') }}</th>
                            <th>{{ trans('admin/main.type') }}</th>
                            <th>{{ trans('admin/main.price') }}</th>
                            <th>{{ trans('admin/main.instructor') }}</th>
                            <th class="text-center">{{ trans('panel.purchase_date') }}</th>
                            <th>{{ trans('admin/main.actions') }}</th>
                        </tr>

                        @if(!empty($purchasedClasses))
                            @foreach($purchasedClasses as $purchasedClass)

                                <tr>
                                    <td width="25%">
                                        <a href="{{ !empty($purchasedClass->webinar) ? $purchasedClass->webinar->getUrl() : '#1' }}" target="_blank" class="">{{ !empty($purchasedClass->webinar) ? $purchasedClass->webinar->title : trans('update.deleted_item') }}</a>
                                    </td>

                                    <td>
                                        @if(!empty($purchasedClass->webinar))
                                            {{ trans('admin/main.'.$purchasedClass->webinar->type) }}
                                        @endif
                                    </td>

                                    <td>
                                        @if(!empty($purchasedClass->webinar))
                                            {{ !empty($purchasedClass->webinar->price) ? handlePrice($purchasedClass->webinar->price) : '-' }}
                                        @else
                                            {{ !empty($purchasedClass->amount) ? handlePrice($purchasedClass->amount) : '-' }}
                                        @endif
                                    </td>

                                    <td width="25%">
                                        @if(!empty($purchasedClass->webinar))
                                            <p>{{ $purchasedClass->webinar->creator->full_name  }}</p>
                                        @else
                                            <p>{{ $purchasedClass->seller->full_name  }}</p>
                                        @endif
                                    </td>

                                    <td class="text-center">{{ dateTimeFormat($purchasedClass->created_at,'j M Y | H:i') }}</td>

                                    <td class="text-right">
                                        @can('admin_enrollment_block_access')
                                            @include('admin.includes.delete_button',[
                                                    'url' => getAdminPanelUrl().'/enrollments/'. $purchasedClass->id .'/block-access',
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
