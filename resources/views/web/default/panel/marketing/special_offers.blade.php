@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')

    <section>
        <h2 class="section-title">{{ trans('panel.create_a_new_discount') }}</h2>
        @if (\Session::has('msg'))
            <div class="alert alert-warning">
                <ul>
                    <li>{!! \Session::get('msg') !!}</li>
                </ul>
            </div>
        @endif
        <div class="panel-section-card py-20 px-25 mt-20">
            <form action="/panel/marketing/special_offers/store" method="post" class="row">
                {{ csrf_field() }}

                <div class="col-12 col-lg-6">
                    <div class="row">
                        <div class="col-12 col-lg-5">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.title') }}</label>
                                <input type="text" name="name"
                                       class="form-control @error('name')  is-invalid @enderror"/>

                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="form-group">
                                <label class="input-label d-block">{{ trans('panel.webinar') }}</label>
                                <select name="webinar_id"
                                        class="form-control custom-select @error('webinar_id')  is-invalid @enderror">
                                    <option selected disabled>{{ trans('panel.select_course') }}</option>

                                    @foreach($webinars as $webinar)
                                        <option value="{{ $webinar->id }}">{{ $webinar->title }}</option>
                                    @endforeach
                                </select>

                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('panel.amount') }}(%)</label>
                                <input type="text" name="percent"
                                       class="form-control @error('percent')  is-invalid @enderror"/>

                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="row">
                        <div class="col-12 col-md-5">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.from') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="dateInputGroupPrepend">
                                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="from_date" autocomplete="off"
                                           class="form-control datetimepicker @error('from_date')  is-invalid @enderror"
                                           aria-describedby="dateInputGroupPrepend"/>

                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="form-group">
                                <label class="input-label">{{ trans('public.to') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="dateInputGroupPrepend">
                                            <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="to_date" autocomplete="off"
                                           class="form-control datetimepicker @error('to_date')  is-invalid @enderror"
                                           aria-describedby="dateInputGroupPrepend"/>

                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-1 d-flex align-items-center justify-content-end">
                    <button type="button" id="formSubmit" class="btn btn-sm btn-primary">{{ trans('public.create') }}</button>
                </div>
            </form>
        </div>
    </section>

    @if(!empty($specialOffers) and $specialOffers->count() > 0)

        <section class="mt-35">
            <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
                <h2 class="section-title">{{ trans('panel.discounts') }}</h2>

                <form action="" method="get" class="d-flex align-items-center flex-row-reverse flex-md-row justify-content-start justify-content-md-center mt-20 mt-md-0">
                    <label class="cursor-pointer mb-0 mr-10 text-gray font-14 font-weight-500" for="activeDiscountsSwitch">{{ trans('panel.show_only_active_discounts') }}</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="active_discounts" class="js-panel-list-switch-filter custom-control-input" {{ request()->get('active_discounts', '') == 'on' ? 'checked' : '' }}
                        id="activeDiscountsSwitch">
                        <label class="custom-control-label" for="activeDiscountsSwitch"></label>
                    </div>
                </form>
            </div>

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table custom-table text-center">
                                <thead>
                                <tr>
                                    <th class="text-left text-gray">{{ trans('panel.name') }}</th>
                                    <th class="text-left text-gray">{{ trans('panel.webinar') }}</th>
                                    <th class="text-center text-gray">{{ trans('panel.amount') }}</th>
                                    <th class="text-center text-gray">{{ trans('public.status') }}</th>
                                    <th class="text-center text-gray">{{ trans('public.from') }}</th>
                                    <th class="text-center text-gray">{{ trans('public.to') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($specialOffers as $specialOffer)
                                    <tr>
                                        <td class="text-left align-middle text-dark-blue font-weight-500">{{ $specialOffer->name }}</td>
                                        <td class="text-left align-middle">
                                            <a href="{{ $specialOffer->webinar->getUrl() }}" class="text-dark-blue font-weight-500" target="_blank">{{ $specialOffer->webinar->title }}</a>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-dark-blue font-weight-500">{{ $specialOffer->percent }}%</span>
                                        </td>
                                        <td class="align-middle">
                                            @switch($specialOffer->status)
                                                @case(\App\Models\SpecialOffer::$active)
                                                <span class="text-primary font-weight-500">{{ trans('public.active') }}</span>

                                                @break
                                                @case(\App\Models\SpecialOffer::$inactive)
                                                <span class="text-warning font-weight-500">{{ trans('public.inactive') }}</span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td class="align-middle text-dark-blue font-weight-500">{{ dateTimeFormat($specialOffer->from_date, 'j M Y | H:i') }}</td>
                                        <td class="align-middle text-dark-blue font-weight-500">{{ dateTimeFormat($specialOffer->to_date, 'j M Y | H:i') }}</td>
                                        <td class="text-right align-middle">
                                            @if($specialOffer->status != \App\Models\SpecialOffer::$inactive)
                                                <div class="btn-group dropdown table-actions">
                                                    <button type="button" class="btn-transparent dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i data-feather="more-vertical" height="20"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a href="/panel/marketing/special_offers/{{ $specialOffer->id }}/disable" type="button"
                                                           class="delete-action btn-transparent">{{ trans('public.disable') }}</a>
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

        </section>

        <div class="my-30">
            {{ $specialOffers->appends(request()->input())->links('vendor.pagination.panel') }}
        </div>

    @else

        @include(getTemplate() . '.includes.no-result',[
            'file_name' => 'offer.png',
            'title' => trans('panel.discount_no_result'),
            'hint' =>  nl2br(trans('panel.discount_no_result_hint')) ,
        ])

    @endif
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>

    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
    </script>

    <script src="/assets/default/js/panel/marketing/special_offers.min.js"></script>
@endpush
