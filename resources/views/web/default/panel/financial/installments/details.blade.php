@extends(getTemplate() .'.panel.layouts.panel_layout')

@section('content')

    @if(!empty($overdueInstallments) and count($overdueInstallments))
        <div class="d-flex align-items-center mb-20 p-15 danger-transparent-alert">
            <div class="danger-transparent-alert__icon d-flex align-items-center justify-content-center">
                <i data-feather="credit-card" width="18" height="18" class=""></i>
            </div>
            <div class="ml-10">
                <div class="font-14 font-weight-bold ">{{ trans('update.overdue_installments') }}</div>
                <div class="font-12 ">{{ trans('update.you_have_count_overdue_installments_please_pay_them_to_avoid_restrictions_and_negative_effects_on_your_account',['count' => count($overdueInstallments)]) }}</div>
            </div>
        </div>
    @endif

    {{-- Installments Overview --}}
    <section>
        <h2 class="section-title">{{ trans('update.installments_overview') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/127.png" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $totalParts }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.total_parts') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/38.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $remainedParts }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.remained_parts') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/33.png" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ handlePrice($remainedAmount) }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.remained_amount') }}</span>
                    </div>
                </div>

                <div class="col-6 col-md-3 mt-30 mt-md-0 d-flex align-items-center justify-content-center mt-5 mt-md-0">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/128.png" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ handlePrice($overdueAmount) }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.overdue_amount') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="mt-25">
        <div class="d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row">
            <h2 class="section-title">{{ trans('update.installments_list') }}</h2>
        </div>

        <div class="panel-section-card py-20 px-25 mt-20">
            <div class="row">
                <div class="col-12 ">
                    <div class="table-responsive">
                        <table class="table text-center custom-table">
                            <thead>
                            <tr>
                                <th>{{ trans('public.title') }}</th>
                                <th class="text-center">{{ trans('panel.amount') }}</th>
                                <th class="text-center">{{ trans('update.due_date') }}</th>
                                <th class="text-center">{{ trans('update.payment_date') }}</th>
                                <th class="text-center">{{ trans('public.status') }}</th>
                                <th class=""></th>
                            </tr>
                            </thead>
                            <tbody>

                            @if(!empty($installment->upfront))
                                @php
                                    $upfrontPayment = $payments->where('type','upfront')->first();
                                @endphp
                                <tr>
                                    <td class="text-left">
                                        {{ trans('update.upfront') }}
                                        @if($installment->upfront_type == 'percent')
                                            <span class="ml-5">({{ $installment->upfront }}%)</span>
                                        @endif
                                    </td>

                                    <td class="text-center">{{ handlePrice($installment->getUpfront($itemPrice)) }}</td>

                                    <td class="text-center">-</td>

                                    <td class="text-center">{{ !empty($upfrontPayment) ? dateTimeFormat($upfrontPayment->created_at, 'j M Y H:i') : '-' }}</td>

                                    <td class="text-center">
                                        @if(!empty($upfrontPayment))
                                            <span class="text-primary">{{ trans('public.paid') }}</span>
                                        @else
                                            <span class="text-dark-blue">{{ trans('update.unpaid') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">

                                    </td>
                                </tr>
                            @endif

                            @foreach($installment->steps as $step)
                                @php
                                    $stepPayment = $payments->where('selected_installment_step_id', $step->id)->where('status', 'paid')->first();
                                    $dueAt = ($step->deadline * 86400) + $order->created_at;
                                    $isOverdue = ($dueAt < time() and empty($stepPayment));
                                @endphp

                                <tr>
                                    <td class="text-left">
                                        <div class="d-block font-16 font-weight-500 text-dark-blue">
                                            {{ $step->title }}

                                            @if($step->amount_type == 'percent')
                                            <span class="ml-5 font-12 text-gray">({{ $step->amount }}%)</span>
                                            @endif
                                        </div>
                                        <span class="d-block font-12 text-gray">{{ trans('update.n_days_after_purchase', ['days' => $step->deadline]) }}</span>
                                    </td>

                                    <td class="text-center">{{ handlePrice($step->getPrice($itemPrice)) }}</td>

                                    <td class="text-center">
                                        <span class="{{ $isOverdue ? 'text-danger' : '' }}">{{ dateTimeFormat($dueAt, 'j M Y') }}</span>
                                    </td>

                                    <td class="text-center">{{ !empty($stepPayment) ? dateTimeFormat($stepPayment->created_at, 'j M Y H:i') : '-' }}</td>

                                    <td class="text-center">
                                        @if(!empty($stepPayment))
                                            <span class="text-primary">{{ trans('public.paid') }}</span>
                                        @else
                                            <span class="{{ $isOverdue ? 'text-danger' : 'text-dark-blue' }}">{{ trans('update.unpaid') }} {{ $isOverdue ? "(". trans('update.overdue') .")" : '' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if(empty($stepPayment))
                                            <div class="btn-group dropdown table-actions">
                                                <button type="button" class="btn-transparent dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                    <i data-feather="more-vertical" height="20"></i>
                                                </button>
                                                <div class="dropdown-menu menu-lg">

                                                    <a href="/panel/financial/installments/{{ $order->id }}/steps/{{ $step->id }}/pay" target="_blank"
                                                       class="webinar-actions d-block mt-10 font-weight-normal">{{ trans('panel.pay') }}</a>
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
@endsection

@push('scripts_bottom')

@endpush
