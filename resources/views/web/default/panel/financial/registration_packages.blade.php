@extends(getTemplate() .'.panel.layouts.panel_layout')

@section('content')

    @if(!empty($activePackage))
        <section>
            <h2 class="section-title">{{ trans('financial.my_active_plan') }}</h2>

            <div class="activities-container mt-25 p-20 p-lg-35">
                <div class="row">
                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/webinars.svg" width="64" height="64" alt="">
                            <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $activePackage->title }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('financial.active_plan') }}</span>
                        </div>
                    </div>

                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/53.svg" width="64" height="64" alt="">
                            <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ dateTimeFormat($activePackage->activation_date, 'j M Y') }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('update.activation_date') }}</span>
                        </div>
                    </div>

                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="/assets/default/img/activity/54.svg" width="64" height="64" alt="">
                            <strong class="font-30 text-dark-blue text-dark-blue font-weight-bold mt-5">{{ $activePackage->days_remained ?? trans('update.unlimited') }}</strong>
                            <span class="font-16 text-gray font-weight-500">{{ trans('financial.days_remained') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif

    <section class="mt-30">
        <h2 class="section-title">{{ trans('update.account_statistics') }}</h2>

        <div class="d-flex align-items-center justify-content-around bg-white rounded-sm shadow mt-15 p-15">

            <div class="registration-package-statistics d-flex flex-column align-items-center">
                <div class="registration-package-statistics-icon">
                    <img src="/assets/default/img/icons/play.svg" alt="">
                </div>
                <span class="font-14 text-dark-blue font-weight-bold mt-5">
                    @if(!empty($activePackage) and isset($activePackage->courses_count))
                        {{ $accountStatistics['myCoursesCount'] }}/{{ $activePackage->courses_count }}
                    @else
                        {{ trans('update.unlimited') }}
                    @endif
                </span>
                <span class="font-14 font-weight-500 text-gray">{{ trans('product.courses') }}</span>
            </div>

            <div class="registration-package-statistics d-flex flex-column align-items-center">
                <div class="registration-package-statistics-icon">
                    <img src="/assets/default/img/icons/video-2.svg" alt="">
                </div>
                <span class="font-14 text-dark-blue font-weight-bold mt-5">
                    @if(!empty($activePackage) and isset($activePackage->courses_capacity))
                        {{ $activePackage->courses_capacity }}
                    @else
                        {{ trans('update.unlimited') }}
                    @endif
                </span>
                <span class="font-14 font-weight-500 text-gray">{{ trans('update.live_students') }}</span>
            </div>

            <div class="registration-package-statistics d-flex flex-column align-items-center">
                <div class="registration-package-statistics-icon">
                    <img src="/assets/default/img/icons/clock.svg" alt="">
                </div>
                <span class="font-14 text-dark-blue font-weight-bold mt-5">
                    @if(!empty($activePackage) and isset($activePackage->meeting_count))
                        {{ $accountStatistics['myMeetingCount'] }}/{{ $activePackage->meeting_count }}
                    @else
                        {{ trans('update.unlimited') }}
                    @endif
                </span>
                <span class="font-14 font-weight-500 text-gray">{{ trans('update.meeting_hours') }}</span>
            </div>

            <div class="registration-package-statistics d-flex flex-column align-items-center">
                <div class="registration-package-statistics-icon">
                    <img src="/assets/default/img/activity/products.svg" alt="">
                </div>
                <span class="font-14 text-dark-blue font-weight-bold mt-5">
                    @if(!empty($activePackage) and isset($activePackage->product_count))
                        {{ $accountStatistics['myProductCount'] }}/{{ $activePackage->product_count }}
                    @else
                        {{ trans('update.unlimited') }}
                    @endif
                </span>
                <span class="font-14 font-weight-500 text-gray">{{ trans('update.products') }}</span>
            </div>

            @if($authUser->isOrganization())
                <div class="registration-package-statistics d-flex flex-column align-items-center">
                    <div class="registration-package-statistics-icon">
                        <img src="/assets/default/img/icons/users.svg" alt="">
                    </div>
                    <span class="font-14 text-dark-blue font-weight-bold mt-5">
                        @if(!empty($activePackage) and isset($activePackage->instructors_count))
                            {{ $accountStatistics['myInstructorsCount'] }}/{{ $activePackage->instructors_count }}
                        @else
                            {{ trans('update.unlimited') }}
                        @endif
                    </span>
                    <span class="font-14 font-weight-500 text-gray">{{ trans('home.instructors') }}</span>
                </div>

                <div class="registration-package-statistics d-flex flex-column align-items-center">
                    <div class="registration-package-statistics-icon">
                        <img src="/assets/default/img/icons/user.svg" alt="">
                    </div>
                    <span class="font-14 text-dark-blue font-weight-bold mt-5">
                        @if(!empty($activePackage) and isset($activePackage->students_count))
                            {{ $accountStatistics['myStudentsCount'] }}/{{ $activePackage->students_count }}
                        @else
                            {{ trans('update.unlimited') }}
                        @endif
                    </span>
                    <span class="font-14 font-weight-500 text-gray">{{ trans('public.students') }}</span>
                </div>
            @endif
        </div>
    </section>

    <section class="mt-30">
        <h2 class="section-title">{{ trans('update.upgrade_your_account') }}</h2>

        <div class="row mt-15">

            @foreach($packages as $package)
                @php
                    $specialOffer = $package->activeSpecialOffer();
                @endphp

                <div class="col-12 col-sm-6 col-lg-3 mt-15">
                    <div class="subscribe-plan position-relative bg-white d-flex flex-column align-items-center rounded-sm shadow pt-50 pb-20 px-20">

                        @if(!empty($activePackage) and $activePackage->package_id == $package->id)
                            <span class="badge badge-primary badge-popular px-15 py-5">{{ trans('update.activated') }}</span>
                        @elseif(!empty($specialOffer))
                            <span class="badge badge-danger badge-popular px-15 py-5">{{ trans('update.percent_off', ['percent' => $specialOffer->percent]) }}</span>
                        @endif


                        <div class="plan-icon">
                            <img src="{{ $package->icon }}" class="img-cover" alt="">
                        </div>

                        <h3 class="mt-20 font-30 text-secondary">{{ $package->title }}</h3>
                        <p class="font-weight-500 font-14 text-gray mt-10">{{ $package->description }}</p>

                        <div class="d-flex align-items-start mt-30">
                            @if(!empty($package->price) and $package->price > 0)
                                @if(!empty($specialOffer))
                                    <div class="d-flex align-items-end line-height-1">
                                        <span class="font-36 text-primary">{{ handlePrice($package->getPrice(), true, true, false, null, true) }}</span>
                                        <span class="font-14 text-gray ml-5 text-decoration-line-through">{{ handlePrice($package->price, true, true, false, null, true) }}</span>
                                    </div>
                                @else
                                    <span class="font-36 text-primary line-height-1">{{ handlePrice($package->price, true, true, false, null, true) }}</span>
                                @endif
                            @else
                                <span class="font-36 text-primary line-height-1">{{ trans('public.free') }}</span>
                            @endif
                        </div>

                        <ul class="mt-20 plan-feature">
                            <li class="mt-10">{{ !isset($package->days) ? trans('update.unlimited'): $package->days }} {{ trans('public.days') }}</li>
                            <li class="mt-10">{{ !isset($package->courses_count) ? trans('update.unlimited') : $package->courses_count }} {{ trans('product.courses') }}</li>
                            <li class="mt-10">{{ !isset($package->courses_capacity) ? trans('update.unlimited') : $package->courses_capacity }} {{ trans('update.live_students') }}</li>
                            <li class="mt-10">{{ !isset($package->meeting_count) ? trans('update.unlimited') : $package->meeting_count }} {{ trans('update.meeting_hours') }}</li>
                            <li class="mt-10">{{ !isset($package->product_count) ? trans('update.unlimited') : $package->product_count }} {{ trans('update.products') }}</li>

                            @if($authUser->isOrganization())
                                <li class="mt-10">{{ $package->instructors_count ?? trans('update.unlimited') }} {{ trans('home.instructors') }}</li>
                                <li class="mt-10">{{ $package->students_count ?? trans('update.unlimited') }} {{ trans('public.students') }}</li>
                            @endif
                        </ul>

                        <form action="{{ route('payRegistrationPackage') }}" method="post" class="btn-block">
                            {{ csrf_field() }}
                            <input name="id" value="{{ $package->id }}" type="hidden">

                            <div class="d-flex align-items-center mt-50 w-100">
                                <button type="submit" class="btn btn-sm btn-primary flex-grow-1 {{ !empty($package->has_installment) ? '' : 'btn-block' }}">{{ trans('update.upgrade') }}</button>

                                @if(!empty($package->has_installment))
                                    <a href="/panel/financial/registration-packages/{{ $package->id }}/installments" class="btn btn-sm btn-outline-primary flex-grow-1 ml-10">{{ trans('update.installments') }}</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/panel/financial/subscribes.min.js"></script>
@endpush
