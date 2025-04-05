@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.discounts') }}</div>
            </div>
        </div>

        <div class="section-body">

            <section class="card">
                <div class="card-body">
                    <form class="mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.search') }}</label>
                                    <input type="text" class="form-control" name="search" value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.expiration_from') }}</label>
                                    <div class="input-group">
                                        <input type="date" id="fsdate" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.expiration_to') }}</label>
                                    <div class="input-group">
                                        <input type="date" id="lsdate" class="text-center form-control" name="to" value="{{ request()->get('from') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.filters') }}</label>
                                    <select name="sort" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_users_discount') }}</option>
                                        <option value="percent_asc" @if(request()->get('sort') == 'percent_asc') selected @endif>{{ trans('admin/main.percentage_ascending') }}</option>
                                        <option value="percent_desc" @if(request()->get('sort') == 'percent_desc') selected @endif>{{ trans('admin/main.percentage_descending') }}</option>
                                        <option value="amount_asc" @if(request()->get('sort') == 'amount_asc') selected @endif>{{ trans('admin/main.max_amount_ascending') }}</option>
                                        <option value="amount_desc" @if(request()->get('sort') == 'amount_desc') selected @endif>{{ trans('admin/main.max_amount_descending') }}</option>
                                        <option value="usable_time_asc" @if(request()->get('sort') == 'usable_time_asc') selected @endif>{{ trans('admin/main.usable_times_ascending') }}</option>
                                        <option value="usable_time_desc" @if(request()->get('sort') == 'usable_time_desc') selected @endif>{{ trans('admin/main.usable_times_descending') }}</option>
                                        <option value="usable_time_remain_asc" @if(request()->get('sort') == 'usable_time_remain_asc') selected @endif>{{ trans('admin/main.usable_times_remain_ascending') }}</option>
                                        <option value="usable_time_remain_desc" @if(request()->get('sort') == 'usable_time_remain_desc') selected @endif>{{ trans('admin/main.usable_times_remain_descending') }}</option>
                                        <option value="created_at_asc" @if(request()->get('sort') == 'created_at_asc') selected @endif>{{ trans('admin/main.create_date_ascending') }}</option>
                                        <option value="created_at_desc" @if(request()->get('sort') == 'created_at_desc') selected @endif>{{ trans('admin/main.create_date_descending') }}</option>
                                        <option value="expire_at_asc" @if(request()->get('sort') == 'expire_at_asc') selected @endif>{{ trans('admin/main.expire_date_ascending') }}</option>
                                        <option value="expire_at_desc" @if(request()->get('sort') == 'expire_at_desc') selected @endif>{{ trans('admin/main.expire_date_descending') }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.user') }}</label>
                                    <select name="user_ids[]" multiple="multiple" class="form-control search-user-select2"
                                            data-placeholder="Search users">

                                        @if(!empty($users) and $users->count() > 0)
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" selected>{{ $user->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.status') }}</label>
                                    <select name="status" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_status') }}</option>
                                        <option value="active" @if(request()->get('status') == 'active') selected @endif>Active</option>
                                        <option value="expired" @if(request()->get('status') == 'expired') selected @endif>Expired</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label mb-4"> </label>
                                    <input type="submit" class="text-center btn btn-primary w-100" value="{{ trans('admin/main.show_results') }}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th class="text-left" width="150">{{ trans('admin/main.title') }}</th>
                                        @if($isInstructorCoupons)
                                            <th class="text-left" width="150">{{ trans('admin/main.creator') }}</th>
                                        @endif
                                        <th width="150">{{ trans('admin/main.type') }}</th>
                                        <th class="text-left" width="150">{{ trans('admin/main.code') }}</th>
                                        <th class="text-left" width="150">{{ trans('admin/main.user') }}</th>
                                        <th width="250">{{ trans('admin/main.created_date') }}</th>
                                        <th width="250">{{ trans('admin/main.ext_date') }}</th>
                                        <th width="150">{{ trans('admin/main.usable_times') }}</th>
                                        <th width="150">{{ trans('admin/main.percentage') }}</th>
                                        <th width="150">{{ trans('admin/main.max_amount') }}</th>
                                        <th width="150">{{ trans('admin/main.amount') }}</th>
                                        <th width="150">{{ trans('update.minimum_order') }}</th>
                                        <th width="50">{{ trans('admin/main.status') }}</th>
                                        <th width="50">{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach($discounts as $discount)
                                        <tr>
                                            <td>
                                                <div class="white-space-nowrap">{{ $discount->title }}</div>
                                            </td>

                                            @if($isInstructorCoupons)
                                                <td class="text-left">
                                                    <div class="d-flex align-items-center">
                                                        <figure class="avatar mr-2">
                                                            <img src="{{ $discount->creator->getAvatar() }}" alt="{{ $discount->creator->full_name }}">
                                                        </figure>
                                                        <div class="media-body ml-1">
                                                            <div class="mt-0 mb-1 font-weight-bold">{{ $discount->creator->full_name }}</div>

                                                            @if($discount->creator->mobile)
                                                                <div class="text-primary text-small font-600-bold">{{ $discount->creator->mobile }}</div>
                                                            @endif

                                                            @if($discount->creator->email)
                                                                <div class="text-primary text-small font-600-bold">{{ $discount->creator->email }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif

                                            <td>
                                                @if($discount->discount_type == \App\Models\Discount::$discountTypeFixedAmount)
                                                    {{ trans('update.fixed_amount') }}
                                                @else
                                                    {{ trans('admin/main.percentage') }}
                                                @endif
                                            </td>
                                            <td class="text-left">{{ $discount->code }}</td>
                                            <td class="text-left">
                                                @if($discount->user_type == 'all_users')
                                                    <span class="text-primary">{{ trans('admin/main.all_users') }}</span>
                                                @elseif(!empty($discount->discountUsers) and !empty($discount->discountUsers->user))
                                                    <span class="">{{ $discount->discountUsers->user->full_name }}</span>
                                                @endif
                                            </td>

                                            <td>{{  dateTimeFormat($discount->created_at, 'Y M d') }}</td>

                                            <td>{{  dateTimeFormat($discount->expired_at, 'Y M d - H:i') }}</td>

                                            <td>
                                                <div class="media-body">
                                                    <div class=" mt-0 mb-1 font-weight-bold">{{ $discount->count }}</div>
                                                    <div class="text-primary text-small">{{ trans('admin/main.remain') }} : {{ $discount->discountRemain() }}</div>
                                                </div>
                                            </td>

                                            <td>{{  $discount->percent ?  $discount->percent . '%' : '-' }}</td>
                                            <td>{{  $discount->max_amount ?  handlePrice($discount->max_amount) : '-' }}</td>
                                            <td>{{  $discount->amount ?  handlePrice($discount->amount) : '-' }}</td>
                                            <td>{{  $discount->minimum_order ?  handlePrice($discount->minimum_order) : '-' }}</td>

                                            <td>
                                                @if($discount->expired_at < time())
                                                    <span class="text-danger">{{ trans('panel.expired') }}</span>
                                                @else
                                                    <span class="text-success">{{ trans('admin/main.active') }}</span>
                                                @endif
                                            </td>

                                            <td>
                                                @can('admin_discount_codes_edit')
                                                    <a href="{{ getAdminPanelUrl() }}/financial/discounts/{{ $discount->id }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('admin_discount_codes_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/financial/discounts/'. $discount->id.'/delete','btnClass' => ''])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $discounts->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

