<div class="installment-card p-15 mt-20">
    <div class="row">
        <div class="col-8">
            <h4 class="font-16 font-weight-bold text-dark-blue">{{ $installment->main_title }}</h4>

            <div class="">
                <p class="text-gray font-14 text-ellipsis">{{ nl2br($installment->description) }}</p>
            </div>

            @if(!empty($installment->capacity))
                @php
                    $reachedCapacityPercent = $installment->reachedCapacityPercent();
                @endphp

                @if($reachedCapacityPercent > 0)
                    <div class="mt-20 d-flex align-items-center">
                        <div class="progress card-progress flex-grow-1">
                            <span class="progress-bar rounded-sm {{ $reachedCapacityPercent > 50 ? 'bg-danger' : 'bg-primary' }}" style="width: {{ $reachedCapacityPercent }}%"></span>
                        </div>
                        <div class="ml-10 font-12 text-danger">{{ trans('update.percent_capacity_reached',['percent' => $reachedCapacityPercent]) }}</div>
                    </div>
                @endif
            @endif

            @if(!empty($installment->banner))
                <div class="mt-20">
                    <img src="{{ $installment->banner }}" alt="{{ $installment->main_title }}" class="img-fluid">
                </div>
            @endif

            @if(!empty($installment->options))
                <div class="mt-20">
                    @php
                        $installmentOptions = explode(\App\Models\Installment::$optionsExplodeKey, $installment->options);
                    @endphp

                    @foreach($installmentOptions as $installmentOption)
                        <div class="d-flex align-items-center mb-1">
                            <i data-feather="check" width="25" height="25" class="text-primary"></i>
                            <span class="ml-10 font-14 text-gray">{{ $installmentOption }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-4 p-0 pr-15">
            <div class="installment-card__payments d-flex flex-column w-100 h-100">

                @php
                    $totalPayments = $installment->totalPayments($itemPrice ?? 1);
                    $installmentTotalInterest = $installment->totalInterest($itemPrice, $totalPayments);
                @endphp

                <div class="d-flex align-items-center justify-content-center flex-column">
                    <span class="font-36 font-weight-bold text-primary">{{ handlePrice($totalPayments) }}</span>
                    <span class="mt-10 font-12 text-gray">{{ trans('update.total_payment') }} @if($installmentTotalInterest > 0)
                            ({{ trans('update.percent_interest',['percent' => $installmentTotalInterest]) }})
                        @endif</span>
                </div>

                <div class="mt-25 mb-15">
                    <div class="installment-step d-flex align-items-center font-12 text-gray">{{ !empty($installment->upfront) ? (trans('update.amount_upfront',['amount' => handlePrice($installment->getUpfront($itemPrice))]) . ($installment->upfront_type == "percent" ? " ({$installment->upfront}%)" : '')) : trans('update.no_upfront') }}</div>

                    @foreach($installment->steps as $installmentStep)
                        <div class="installment-step d-flex align-items-center font-12 text-gray">{{ $installmentStep->getDeadlineTitle($itemPrice) }}</div>
                    @endforeach
                </div>

                <a href="/installments/{{ $installment->id }}?item={{ $itemId }}&item_type={{ $itemType }}&{{ http_build_query(request()->all()) }}" target="_blank" class="btn btn-primary btn-block mt-auto">{{ trans('update.pay_with_installments') }}</a>
            </div>
        </div>
    </div>
</div>
