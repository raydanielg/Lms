@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.special_offers') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.special_offers') }}</div>
            </div>
        </div>


        <div class="section-body">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8 col-lg-6">
                            <form action="{{ getAdminPanelUrl() }}/financial/special_offers/{{ !empty($specialOffer) ? $specialOffer->id.'/update' : 'store' }}"
                                  method="Post">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label>{{ trans('admin/main.name') }}</label>
                                    <input type="text" name="name"
                                           class="form-control  @error('name') is-invalid @enderror"
                                           value="{{ !empty($specialOffer) ? $specialOffer->name : old('name') }}"
                                           placeholder="{{ trans('admin/main.name_placeholder') }}"/>
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                @php
                                    $types = [
                                        'courses' => 'webinar_id',
                                        'bundles' => 'bundle_id',
                                        'subscription_packages' => 'subscribe_id',
                                        'registration_packages' => 'registration_package_id',
                                    ];
                                @endphp

                                <div class="form-group">
                                    <label>{{ trans('admin/main.type') }}</label>

                                    <select name="type" class="js-offer-type form-control @error('type')  is-invalid @enderror">

                                        @foreach($types as $type => $typeItem)
                                            <option value="{{ $type }}" {{ (!empty($specialOffer) and !empty($specialOffer->{$typeItem})) ? 'selected' : '' }}>{{ trans('update.'.$type) }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="js-course-field form-group {{ (empty($specialOffer) or !empty($specialOffer->webinar_id)) ? '' : 'd-none' }}">
                                    <label>{{ trans('admin/main.class') }}</label>

                                    <select name="webinar_id" class="form-control search-webinar-select2 @error('webinar_id')  is-invalid @enderror" data-placeholder="{{ trans('update.search_and_select_class') }}">

                                        @if(!empty($specialOffer) and !empty($specialOffer->webinar))
                                            <option value="{{ $specialOffer->webinar->id }}" selected>{{ $specialOffer->webinar->title }}</option>
                                        @endif
                                    </select>
                                    @error('webinar_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="js-bundle-field form-group {{ (!empty($specialOffer) and !empty($specialOffer->bundle_id)) ? '' : 'd-none' }}">
                                    <label>{{ trans('update.bundle') }}</label>

                                    <select name="bundle_id" class="form-control search-bundle-select2 @error('bundle_id')  is-invalid @enderror" data-placeholder="{{ trans('update.search_and_select_bundle') }}">

                                        @if(!empty($specialOffer) and !empty($specialOffer->bundle))
                                            <option value="{{ $specialOffer->bundle->id }}" selected>{{ $specialOffer->bundle->title }}</option>
                                        @endif
                                    </select>
                                    @error('bundle_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="js-subscribe-field form-group {{ (!empty($specialOffer) and !empty($specialOffer->subscribe_id)) ? '' : 'd-none' }}">
                                    <label>{{ trans('public.subscribe') }}</label>

                                    <select name="subscribe_id" class="form-control @error('subscribe_id')  is-invalid @enderror">
                                        <option value="">{{ trans('update.select_subscribe') }}</option>
                                        @foreach($subscribes as $subscribe)
                                            <option value="{{ $subscribe->id }}" {{ (!empty($specialOffer) and $specialOffer->subscribe_id == $subscribe->id) ? 'selected' : '' }}>{{ $subscribe->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('subscribe_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="js-registration_package-field form-group {{ (!empty($specialOffer) and !empty($specialOffer->registration_package_id)) ? '' : 'd-none' }}">
                                    <label>{{ trans('update.registration_package') }}</label>

                                    <select name="registration_package_id" class="form-control @error('registration_package_id')  is-invalid @enderror">
                                        <option value="">{{ trans('update.select_registration_package') }}</option>
                                        @foreach($registrationPackages as $registration_package)
                                            <option value="{{ $registration_package->id }}" {{ (!empty($specialOffer) and $specialOffer->registration_package_id == $registration_package->id) ? 'selected' : '' }}>{{ $registration_package->title }}</option>
                                        @endforeach
                                    </select>

                                    @error('registration_package_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label>{{ trans('admin/main.discount_percentage') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-percentage"></i>
                                            </div>
                                        </div>
                                        <input type="number"
                                               name="percent" class="spinner-input form-control text-center  @error('percent') is-invalid @enderror"
                                               value="{{ !empty($specialOffer) ? $specialOffer->percent : old('percent') }}"
                                               maxlength="3" min="0" max="100">
                                        @error('percent')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.from_date') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateRangeLabel">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="from_date" class="form-control text-center datetimepicker"
                                               aria-describedby="dateRangeLabel" autocomplete="off"
                                               value="{{ !empty($specialOffer) ? dateTimeFormat($specialOffer->from_date,'Y-m-d H:i',false) : old('from_date') }}"/>
                                        @error('from_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="input-label">{{ trans('admin/main.to_date') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="dateRangeLabel">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" name="to_date" class="form-control text-center datetimepicker"
                                               aria-describedby="dateRangeLabel" autocomplete="off"
                                               value="{{ !empty($specialOffer) ? dateTimeFormat($specialOffer->to_date,'Y-m-d H:i',false) : old('to_date') }}"/>
                                        @error('to_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ trans('admin/main.status') }}</label>
                                    <select name="status" class="form-control custom-select @error('status')  is-invalid @enderror">
                                        <option value="active" {{ !empty($specialOffer) and $specialOffer->status == \App\Models\SpecialOffer::$active ? 'selected' : '' }}>{{ trans('panel.active') }}</option>
                                        <option value="inactive" {{ !empty($specialOffer) and $specialOffer->status == \App\Models\SpecialOffer::$inactive ? 'selected' : '' }}>{{ trans('panel.inactive') }}</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class=" mt-4">
                                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/special_offers.min.js"></script>
@endpush
