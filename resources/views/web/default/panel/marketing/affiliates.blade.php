@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')

@endpush

@section('content')
    <section class="">
        <h2 class="section-title">{{ trans('panel.affiliate_statistics') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/48.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $referredUsersCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.referred_users') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/38.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ handlePrice($registrationBonus) }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.registration_bonus') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/36.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ handlePrice($affiliateBonus) }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.affiliate_bonus') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="mt-25">
        <h2 class="section-title">{{ trans('panel.affiliate_summary') }}</h2>

        @if(!empty($referralSettings))
            <div class="mt-15 font-14 text-gray">
                @if(!empty($referralSettings['affiliate_user_amount']))<p>- {{ trans('panel.user_registration_reward') }}: {{ handlePrice($referralSettings['affiliate_user_amount']) }}</p>@endif
                @if(!empty($referralSettings['referred_user_amount']))<p>- {{ trans('panel.referred_user_registration_reward') }}: {{ handlePrice($referralSettings['referred_user_amount']) }}</p>@endif
                @if(!empty($referralSettings['affiliate_user_commission']))<p>- {{ trans('panel.referred_user_purchase_commission') }}: {{ $referralSettings['affiliate_user_commission'] }}%</p>@endif
                <p>- {{ trans('panel.your_affiliate_code') }}: {{ $affiliateCode->code }}</p>
                @if(!empty($referralSettings['referral_description']))<p>- {{ $referralSettings['referral_description'] }}</p>@endif
            </div>
        @endif

        <div class="row mt-15">
            <div class="col-12 col-lg-5">
                <h3 class="font-16 font-weight-500">{{ trans('panel.affiliate_url') }}</h3>

                <div class="form-group mt-5">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text js-copy" data-input="affiliate_url" data-toggle="tooltip" data-placement="top" title="{{ trans('public.copy') }}" data-copy-text="{{ trans('public.copy') }}" data-done-text="{{ trans('public.done') }}">
                                <i data-feather="copy" width="18" height="18" class="text-white"></i>
                            </button>
                        </div>
                        <input type="text" name="affiliate_url" readonly value="{{ $affiliateCode->getAffiliateUrl() }}" class="form-control"/>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section class="mt-25">
        <h2 class="section-title">{{ trans('panel.earnings') }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <div class="row">
                <div class="col-12 ">
                    <div class="table-responsive">
                        <table class="table text-center custom-table">
                            <thead>
                            <tr>
                                <th>{{ trans('panel.user') }}</th>
                                <th class="text-center">{{ trans('panel.registration_bonus') }}</th>
                                <th class="text-center">{{ trans('panel.affiliate_bonus') }}</th>
                                <th class="text-center">{{ trans('panel.registration_date') }}</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($affiliates as $affiliate)
                                <tr>
                                    <td class="text-left">
                                        <div class="user-inline-avatar d-flex align-items-center">
                                            <div class="avatar bg-gray200">
                                                <img src="{{ $affiliate->referredUser->getAvatar() }}" class="img-cover" alt="{{ $affiliate->referredUser->full_name }}">
                                            </div>
                                            <div class=" ml-5">
                                                <span class="d-block font-weight-500">{{ $affiliate->referredUser->full_name }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>{{ handlePrice($affiliate->getAffiliateRegistrationAmountsOfEachReferral()) }}</td>

                                    <td>{{ handlePrice($affiliate->getTotalAffiliateCommissionOfEachReferral()) }}</td>

                                    <td>{{ dateTimeFormat($affiliate->created_at, 'Y M j | H:i') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="my-30">
                        {{ $affiliates->appends(request()->input())->links('vendor.pagination.panel') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
