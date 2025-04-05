@extends('web.default.panel.layouts.panel_layout')

@section('content')
    <section>
        <h2 class="section-title">{{ trans('update.points_statistics') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/trophy_cup.png" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $availablePoints }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.available_points') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/rank.png" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $totalPoints }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.total_points') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/spent.png" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $spentPoints }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('update.spent_points') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row mt-20">
        <div class="col-12 col-lg-6">
            <div class="rounded-sm bg-white shadow-lg px-15 px-lg-25 py-15 py-lg-35">
                <div class="row">
                    <div class="col-12 col-lg-5 d-flex align-items-center justify-content-center">
                        <div class="reward-gift-img">
                            <img src="/assets/default/img/rewards/gift_icon.svg" class="img-fluid" alt="gift">
                        </div>
                    </div>

                    <div class="col-12 col-lg-7 mt-20 mt-lg-0 text-center">
                        <h3 class="font-16 font-weight-bold text-dark-blue">{{ trans('update.exchange_or_get_a_course') }}</h3>
                        @if(!empty($rewardsSettings) and !empty($rewardsSettings['exchangeable']) and $rewardsSettings['exchangeable'] == '1')
                            <p class="mt-15 text-gray font-16 font-weight-500">{{ trans('update.exchange_or_get_a_course_by_spending_points_hint') }}</p>

                            <span class="font-30 font-weight-bold mt-15 text-primary d-block">{{ handlePrice($earnByExchange) }}</span>

                            <p class="mt-15 text-gray font-16 font-weight-500">{{ trans('update.for_your_available_points') }}</p>
                        @else
                            <p class="mt-15 text-gray font-16 font-weight-500">{{ trans('update.just_get_a_course_by_spending_points_hint') }}</p>
                        @endif

                        <div class="d-flex align-items-center justify-content-center w-100 mt-25">
                            @if(!empty($rewardsSettings) and !empty($rewardsSettings['exchangeable']) and $rewardsSettings['exchangeable'] == '1')
                                <button type="button" class="btn btn-sm mr-15 {{ $earnByExchange > 0 ? 'js-exchange-btn  btn-primary' : 'bg-gray300 text-gray disabled' }}" {{ $earnByExchange > 0 ? '' : 'disabled' }}>{{ trans('update.exchange') }}</button>
                            @endif

                            <a href="/reward-courses" class="btn btn-sm btn-outline-primary">{{ trans('update.browse_courses') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 mt-20 mt-lg-0">
            <div class="d-flex align-items-center justify-content-between rounded-sm bg-white p-15 shadow-lg">
                <div class="d-flex align-items-center ">
                    <img src="/assets/default/img/rewards/medal.png" width="51" height="51" alt="medal">

                    <div class="ml-15">
                        <h3 class="font-16 font-weight-bold text-dark-blue">{{ trans('update.want_more_points') }}</h3>
                        <p class="mt-5 text-gray font-12 font-weight-500">{{ trans('update.want_more_points_hint') }}</p>
                    </div>
                </div>

                <div class="flex-grow-1 ml-15 text-right">
                    <a href="{{ (!empty($rewardsSettings) and !empty($rewardsSettings['want_more_points_link'])) ? $rewardsSettings['want_more_points_link'] : '' }}" class="btn btn-sm btn-border-white">{{ trans('update.view_more') }}</a>
                </div>
            </div>

            @if(!empty($mostPointsUsers) and count($mostPointsUsers))
                <div class="rounded-sm bg-white p-15 shadow-lg mt-20">
                    <div class="row">
                        @php
                            $leaderboard = $mostPointsUsers->shift();
                        @endphp
                        <div class="col-12 col-lg-5">
                            <h3 class="text-dark-blue font-16 font-weight-bold">{{ trans('update.leaderboard') }}</h3>

                            <div class="d-flex align-items-center justify-content-center flex-column mt-20">
                                <div class="leaderboard-avatar">
                                    <img src="{{ $leaderboard->user->getAvatar() }}" class="img-cover rounded-circle" alt="{{ $leaderboard->user->full_name }}">
                                </div>

                                <span class="font-14 font-weight-bold text-secondary mt-10 d-block">{{ $leaderboard->user->full_name }}</span>
                                <span class="mt-5 text-gray font-12 font-weight-500">{{ $leaderboard->total_points }} {{ trans('update.points') }}</span>
                            </div>
                        </div>

                        <div class="col-12 col-lg-7 mt-20 mt-lg-0">
                            @foreach($mostPointsUsers as $mostPoint)
                                <div class="rounded-sm border p-10 d-flex align-items-center {{ ($loop->iteration > 1) ? 'mt-10' : '' }}">
                                    <div class="leaderboard-others-avatar">
                                        <img src="{{ $mostPoint->user->getAvatar() }}" class="img-cover rounded-circle" alt="{{ $mostPoint->user->full_name }}">
                                    </div>

                                    <div class="flex-grow-1 ml-15">
                                        <span class="font-14 font-weight-bold text-secondary d-block">{{ $mostPoint->user->full_name }}</span>
                                        <span class="text-gray font-12 font-weight-500">{{ $mostPoint->total_points }} {{ trans('update.points') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <section class="mt-35">
        <h2 class="section-title">{{ trans('update.points_statistics') }}</h2>

        @if(!empty($rewards))

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table text-center custom-table">
                                <thead>
                                <tr>
                                    <th class="text-left">{{ trans('public.title') }}</th>
                                    <th class="text-center">{{ trans('update.points') }}</th>
                                    <th class="text-center">{{ trans('public.type') }}</th>
                                    <th class="text-center">{{ trans('public.date_time') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($rewards as $reward)
                                    <tr>
                                        <td class="text-left">{{ trans('update.reward_type_'.$reward->type) }}</td>
                                        <td class="text-center align-middle">{{ $reward->score }}</td>
                                        <td class="text-center align-middle">
                                            @if($reward->status == \App\Models\RewardAccounting::ADDICTION)
                                                <span class="text-primary">{{ trans('update.add') }}</span>
                                            @else
                                                <span class="text-danger">{{ trans('update.minus') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">{{ dateTimeFormat($reward->created_at, 'j F Y H:i') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-30">
                {{ $rewards->links('vendor.pagination.panel') }}
            </div>
        @else

            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'quiz.png',
                'title' => trans('update.reward_no_result'),
                'hint' => nl2br(trans('update.reward_no_result_hint')),
            ])

        @endif
    </section>

    @if(!empty($rewardsSettings) and !empty($rewardsSettings['exchangeable']) and $rewardsSettings['exchangeable'] == '1')
        @include('web.default.panel.rewards.exchange_modal')
    @endif
@endsection

@push('scripts_bottom')
    <script>
        var exchangeSuccessAlertTitleLang = '{{ trans('update.exchange_success_alert_title') }}';
        var exchangeSuccessAlertDescLang = '{{ trans('update.exchange_success_alert_desc') }}';
        var exchangeErrorAlertTitleLang = '{{ trans('update.exchange_error_alert_title') }}';
        var exchangeErrorAlertDescLang = '{{ trans('update.exchange_error_alert_desc') }}';
    </script>
    <script src="/assets/default/js/panel/reward.min.js"></script>
@endpush
