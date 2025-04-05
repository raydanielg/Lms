@extends('web.default.layouts.app')

@section('content')
    <div class="container become-instructor-packages">
        <div class="text-center mt-50">
            <h1 class="font-36 font-weight-bold text-dark">{{ trans('update.registration_packages') }}</h1>
            <p class="font-16 font-weight-normal text-gray mt-10">{{ trans('update.select_registration_package_hint') }}</p>

            @if(empty($becomeInstructor))
                <a href="/become-instructor" class="btn btn-primary mt-15">{{ trans('site.become_instructor') }}</a>
            @endif
        </div>

        @if(!empty($defaultPackage))
            <div class="d-flex align-items-center flex-column flex-lg-row mt-50 border rounded-lg p-15 p-lg-25">
                <div class="default-package-icon">
                    <img src="/assets/default/img/become-instructor/default.png" class="img-cover" alt="{{ trans('update.default_package') }}">
                </div>

                <div class="ml-lg-25 w-100 mt-20 mt-lg-0">
                    <h2 class="font-24 font-weight-bold text-dark-blue">{{ trans('update.default_package') }}</h2>
                    <p class="font-14 font-weight-500 text-gray">{{ trans('update.default_package_hint') }}</p>

                    <div class="d-flex flex-wrap align-items-center justify-content-between w-100">

                        <div class="d-flex align-items-center mt-20">
                            <div class="default-package-statistics-icon">
                                <img src="/assets/default/img/icons/play.svg" alt="play">
                            </div>

                            <span class="font-12 text-dark-blue font-weight-bold mx-1">
                               {{ $defaultPackage->courses_count ?? trans('update.unlimited') }}
                            </span>
                            <span class="font-14 font-weight-500 text-gray">{{ trans('product.courses') }}</span>
                        </div>

                        <div class="d-flex align-items-center mt-20">
                            <div class="default-package-statistics-icon">
                                <img src="/assets/default/img/icons/video-2.svg" alt="video-2">
                            </div>

                            <span class="font-12 text-dark-blue font-weight-bold mx-1">
                               {{ $defaultPackage->courses_capacity ?? trans('update.unlimited') }}
                            </span>
                            <span class="font-14 font-weight-500 text-gray">{{ trans('update.live_students') }}</span>
                        </div>

                        <div class="d-flex align-items-center mt-20">
                            <div class="default-package-statistics-icon">
                                <img src="/assets/default/img/icons/clock.svg" alt="clock">
                            </div>

                            <span class="font-12 text-dark-blue font-weight-bold mx-1">
                               {{ $defaultPackage->meeting_count ?? trans('update.unlimited') }}
                            </span>
                            <span class="font-14 font-weight-500 text-gray">{{ trans('update.meeting_hours') }}</span>
                        </div>

                        <div class="d-flex align-items-center mt-20">
                            <div class="default-package-statistics-icon">
                                <img src="/assets/default/img/icons/clock.svg" alt="clock">
                            </div>

                            <span class="font-12 text-dark-blue font-weight-bold mx-1">
                               {{ $defaultPackage->product_count ?? trans('update.unlimited') }}
                            </span>
                            <span class="font-14 font-weight-500 text-gray">{{ trans('update.products') }}</span>
                        </div>

                        @if($selectedRole == 'organizations')
                            <div class="d-flex align-items-center mt-20">
                                <div class="default-package-statistics-icon">
                                    <img src="/assets/default/img/icons/users.svg" alt="users">
                                </div>

                                <span class="font-12 text-dark-blue font-weight-bold mx-1">
                                   {{ $defaultPackage->instructors_count ?? trans('update.unlimited') }}
                                </span>
                                <span class="font-14 font-weight-500 text-gray">{{ trans('home.instructors') }}</span>
                            </div>

                            <div class="d-flex align-items-center mt-20">
                                <div class="default-package-statistics-icon">
                                    <img src="/assets/default/img/icons/user.svg" alt="user">
                                </div>

                                <span class="font-12 text-dark-blue font-weight-bold mx-1">
                                   {{ $defaultPackage->students_count ?? trans('update.unlimited') }}
                                </span>
                                <span class="font-14 font-weight-500 text-gray">{{ trans('public.students') }}</span>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('payRegistrationPackage') }}" method="post">
            {{ csrf_field() }}

            @if(!empty($becomeInstructor))
                <input type="hidden" name="become_instructor_id" value="{{ $becomeInstructor->id }}"/>
            @endif

            <div class="row mt-20">
                @foreach($packages as $package)
                    @php
                        $specialOffer = $package->activeSpecialOffer();
                    @endphp

                    <div class="col-12 col-sm-6 col-lg-4 mt-15 {{ !empty($becomeInstructor) ? 'charge-account-radio' : '' }}">
                        @if(!empty($becomeInstructor))
                            <input type="radio" name="id" id="package{{ $package->id }}" value="{{ $package->id }}">
                        @endif

                        <label for="package{{ $package->id }}" class="subscribe-plan cursor-pointer position-relative bg-white d-flex flex-column align-items-center rounded-sm shadow pt-50 pb-20 px-20">

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
                                <li class="mt-10">{{ $package->days ?? trans('update.unlimited') }} {{ trans('public.days') }}</li>
                                <li class="mt-10">{{ $package->courses_count ?? trans('update.unlimited') }} {{ trans('product.courses') }}</li>
                                <li class="mt-10">{{ $package->courses_capacity ?? trans('update.unlimited') }} {{ trans('update.live_students') }}</li>
                                <li class="mt-10">{{ $package->meeting_count ?? trans('update.unlimited') }} {{ trans('update.meeting_hours') }}</li>
                                <li class="mt-10">{{ $package->product_count ?? trans('update.unlimited') }} {{ trans('update.products') }}</li>

                                @if($selectedRole == 'organizations')
                                    <li class="mt-10">{{ $package->instructors_count ?? trans('update.unlimited') }} {{ trans('home.instructors') }}</li>
                                    <li class="mt-10">{{ $package->students_count ?? trans('update.unlimited') }} {{ trans('public.students') }}</li>
                                @endif
                            </ul>
                        </label>
                    </div>
                @endforeach
            </div>

            @if(!empty($becomeInstructor))
                <div class="d-flex align-items-center justify-content-between mt-20 pt-10 border-top">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-border-white">{{ trans('update.back') }}</a>

                    <div class="">
                        @if(!getRegistrationPackagesGeneralSettings('force_user_to_select_a_package'))
                            <a href="/panel" class="btn btn-sm btn-primary mr-5">{{ trans('update.skip') }}</a>
                        @endif

                        <a href="" class="js-installment-btn d-none btn btn-primary btn-sm">
                            {{ trans('update.pay_with_installments') }}
                        </a>

                        <button type="submit" id="paymentSubmit" disabled class="btn btn-sm btn-primary">{{ trans('cart.checkout') }}</button>
                    </div>
                </div>
            @endif
        </form>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/become_instructor.min.js"></script>
@endpush
