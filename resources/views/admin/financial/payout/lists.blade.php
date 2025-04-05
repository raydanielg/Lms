@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.payouts') }}</div>
            </div>
        </div>


        <div class="section-body">

            <section class="card">
                <div class="card-body">
                    <form method="get" class="mb-0">
                        <input type="hidden" name="payout" value="{{ request()->get('payout') }}">

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.search') }}</label>
                                    <input type="text" class="form-control text-center" name="search" value="{{ request()->get('search') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                    <div class="input-group">
                                        <input type="date" id="fsdate" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                    <div class="input-group">
                                        <input type="date" id="lsdate" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.role') }}</label>
                                    <select name="role_id" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_roles') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @if($role->id == request()->get('role_id')) selected @endif>{{ $role->caption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.user') }}</label>
                                    <select name="user_ids[]" multiple="multiple" class="form-control search-user-select2"
                                            data-placeholder="Search teachers">

                                        @if(!empty($users) and $users->count() > 0)
                                            @foreach($users as $user_filter)
                                                <option value="{{ $user_filter->id }}" selected>{{ $user_filter->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.bank') }}</label>
                                    <select name="account_type" data-plugin-selectTwo class="form-control populate">
                                        <option value="">{{ trans('admin/main.all_banks') }}</option>

                                        @foreach($offlineBanks as $offlineBank)
                                            <option value="{{ $offlineBank->id }}" @if(request()->get('account_type') == $offlineBank->id) selected @endif>{{ $offlineBank->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.filters') }}</label>
                                    <select name="sort" data-plugin-selectTwo class="form-control populate">
                                        <option value="">Filter Type</option>
                                        <option value="amount_asc" @if(request()->get('sort') == 'amount_asc') selected @endif>{{ trans('admin/main.amount_ascending') }}</option>
                                        <option value="amount_desc" @if(request()->get('sort') == 'amount_desc') selected @endif>{{ trans('admin/main.amount_descending') }}</option>
                                        <option value="created_at_asc" @if(request()->get('sort') == 'created_at_asc') selected @endif>{{ trans('admin/main.last_payout_date_ascending') }}</option>
                                        <option value="created_at_desc" @if(request()->get('sort') == 'created_at_desc') selected @endif>{{ trans('admin/main.last_payout_date_descending') }}</option>
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
                        <div class="card-header">
                            @can('admin_payouts_export_excel')
                                <a href="{{ getAdminPanelUrl() }}/financial/payouts/excel?{{ http_build_query(request()->all()) }}" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.user') }}</th>
                                        <th>{{ trans('admin/main.role') }}</th>
                                        <th>{{ trans('admin/main.payout_amount') }}</th>
                                        <th class="">{{ trans('admin/main.bank') }}</th>

                                        <th>{{ trans('admin/main.phone') }}</th>
                                        <th width="180px">{{ trans('admin/main.last_payout_date') }}</th>

                                        @if(request()->get('payout') == 'history')
                                            <th>{{ trans('admin/main.status') }}</th>
                                        @endif

                                        <th width="150px">{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @if($payouts->count() > 0)
                                        @foreach($payouts as $payout)

                                            <tr>
                                                <td class="text-left">
                                                    <span class="d-block">{{ $payout->user->full_name }}</span>
                                                </td>

                                                <td>{{ $payout->user->role->caption }}</td>

                                                <td>{{ handlePrice($payout->amount) }}</td>


                                                @if(!empty($payout->userSelectedBank->bank))
                                                <td class="">
                                                    @php
                                                        $bank = $payout->userSelectedBank->bank;
                                                    @endphp
                                                    <div class="font-weight-500">{{ $bank->title }}</div>

                                                    {{-- For Modal --}}
                                                    <input type="hidden" class="js-bank-details" data-name="{{ trans("admin/main.bank") }}" value="{{ $bank->title }}">
                                                    @foreach($bank->specifications as $specification)
                                                        @php
                                                            $selectedBankSpecification = $payout->userSelectedBank->specifications->where('user_selected_bank_id', $payout->userSelectedBank->id)->where('user_bank_specification_id', $specification->id)->first();
                                                        @endphp

                                                        @if(!empty($selectedBankSpecification))
                                                            <input type="hidden" class="js-bank-details" data-name="{{ $specification->name }}" value="{{ $selectedBankSpecification->value }}">
                                                        @endif
                                                    @endforeach

                                                </td>
                                                @else
                                                <td>-</td>
                                                @endif

                                                <td>{{ $payout->user->mobile }}</td>

                                                <td>{{ dateTimeFormat($payout->created_at, 'j M Y H:i') }}</td>

                                                @if(request()->get('payout') == 'history')
                                                    <td>
                                                        <span class="{{ ($payout->status == 'done') ? 'text-success' : 'text-danger' }}">{{ trans('public.'.$payout->status) }}</span>
                                                    </td>
                                                @endif


                                                <td width="150px">
                                                    <div class="">
                                                        <button type="button" class="js-show-details btn-sm btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('update.show_details') }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>

                                                        @if(request()->get('payout') == 'requests' and $payout->status === \App\Models\Payout::$waiting)

                                                            @can('admin_payouts_payout')
                                                                @include('admin.includes.delete_button',[
                                                                        'url' => getAdminPanelUrl().'/financial/payouts/'. $payout->id .'/payout',
                                                                        'tooltip' => trans('admin/main.payout'),
                                                                        'btnClass' => 'ml-2',
                                                                        'btnIcon' => 'fa-credit-card'
                                                                    ])
                                                            @endcan

                                                            @can('admin_payouts_reject')
                                                                @include('admin.includes.delete_button',[
                                                                        'url' => getAdminPanelUrl().'/financial/payouts/'. $payout->id .'/reject',
                                                                        'tooltip' => trans('public.reject'),
                                                                        'btnIcon' => 'fa-times-circle',
                                                                        'btnClass' => 'ml-2',
                                                                    ])
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $payouts->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3"><h5>{{trans('admin/main.hints')}}</h5></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.payout_list_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.payout_list_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.payout_list_hint_title_2')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.payout_list_hint_description_2')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts_bottom')
    <script>
        var payoutDetailsLang = '{{ trans('update.payout_details') }}';
        var closeLang = '{{ trans('public.close') }}';
    </script>

    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/js/admin/payout.min.js"></script>
@endpush
