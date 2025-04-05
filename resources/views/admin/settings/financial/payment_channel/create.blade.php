@extends('admin.layouts.app')



@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body ">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">

                        <div class="card-body">

                            <form action="{{ getAdminPanelUrl() }}/settings/payment_channels/{{ (!empty($paymentChannel) ? $paymentChannel->id.'/update':'store') }}" method="post">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group">
                                            <label>{{ trans('admin/main.title') }}</label>
                                            <input type="text" name="title"
                                                   class="form-control  @error('title') is-invalid @enderror"
                                                   value="{{ !empty($paymentChannel) ? $paymentChannel->title : old('title') }}"
                                                   placeholder="{{ trans('admin/main.choose_title') }}"/>
                                            @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>{{trans('update.class_dev')}}</label>
                                            <input type="text" disabled name="class_name"
                                                   class="form-control  @error('class_name') is-invalid @enderror"
                                                   value="{{ !empty($paymentChannel) ? $paymentChannel->class_name : old('class_name') }}"/>
                                            @error('class_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('public.image') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text admin-file-manager" data-input="image" data-preview="holder">
                                                        <i class="fa fa-upload"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="image" id="image" value="{{ !empty($paymentChannel->image) ? $paymentChannel->image : old('image') }}" class="form-control @error('image') is-invalid @enderror"/>
                                                <div class="input-group-append">
                                                    <button type="button" class="input-group-text admin-file-view" data-input="image">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>

                                                @error('image')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if(!empty($credentialItems))
                                            <div class="payment-channel-credentials-card border mb-3">
                                                <h5 class="mb-2">{{ trans('update.channel_credentials') }}</h5>

                                                @foreach($credentialItems as $credentialKey => $credentialItem)
                                                    @if(is_array($credentialItem))
                                                        <div class="form-group">
                                                            <label>{{ $credentialKey }}</label>
                                                            <select name="credentials[{{ $credentialKey }}]" class="form-control">
                                                                <option value="">{{ trans('update.select_a_option') }}</option>

                                                                @foreach($credentialItem as $cItem)
                                                                    <option value="{{ $cItem }}" {{ (!empty($paymentChannel) and !empty($paymentChannel->credentials) and !empty($paymentChannel->credentials[$credentialKey]) and $paymentChannel->credentials[$credentialKey] == $cItem) ? 'selected' : '' }}>{{ $cItem }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @else
                                                        <div class="form-group">
                                                            <label>{{ $credentialItem }}</label>
                                                            <input type="text" name="credentials[{{ $credentialItem }}]"
                                                                   class="form-control"
                                                                   value="{{ (!empty($paymentChannel) and !empty($paymentChannel->credentials) and !empty($paymentChannel->credentials[$credentialItem])) ? $paymentChannel->credentials[$credentialItem] : '' }}"/>
                                                        </div>
                                                    @endif
                                                @endforeach

                                                @if($showTestModeToggle)
                                                    <div class="form-group custom-switches-stacked mb-0">
                                                        <label class="custom-switch pl-0">
                                                            <input type="hidden" name="credentials[test_mode]" value="">
                                                            <input type="checkbox" name="credentials[test_mode]" id="test_modeSwitch" value="on" class="custom-switch-input" {{ (!empty($paymentChannel) and !empty($paymentChannel->credentials) and !empty($paymentChannel->credentials['test_mode'])) ? 'checked' : '' }}/>
                                                            <span class="custom-switch-indicator"></span>
                                                            <label class="custom-switch-description mb-0 cursor-pointer" for="test_modeSwitch">{{ trans('update.test_mode') }}</label>
                                                        </label>
                                                    </div>
                                                @endif

                                            </div>
                                        @endif

                                        @php
                                            $selectedCurrency = !empty($paymentChannel->currencies) ? $paymentChannel->currencies : [];
                                        @endphp

                                        @if(!empty($currencies) and count($currencies))
                                            <div class="form-group">
                                                <label class="input-label">{{ trans('update.supported_currencies') }}</label>
                                                <select name="currencies[]" class="form-control select2" multiple>
                                                    @foreach($currencies as $currencyItem)
                                                        <option value="{{ $currencyItem->currency }}" {{ in_array($currencyItem->currency, $selectedCurrency) ? 'selected' : '' }}>{{ currenciesLists($currencyItem->currency) }} ({{ currencySign($currencyItem->currency) }})</option>
                                                    @endforeach
                                                </select>
                                                @error('currency')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        @else
                                            <input type="hidden" name="currencies[]" value="{{ getDefaultCurrency() }}">
                                        @endif


                                        <div class="form-group custom-switches-stacked">
                                            <label class="custom-switch pl-0">
                                                <input type="hidden" name="status" value="inactive">
                                                <input type="checkbox" name="status" id="statusSwitch" value="active" {{ (!empty($paymentChannel) and $paymentChannel->status == 'active') ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                                <span class="custom-switch-indicator"></span>
                                                <label class="custom-switch-description mb-0 cursor-pointer" for="statusSwitch">{{ trans('admin/main.active') }}</label>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mt-4">{{ trans('admin/main.save_change') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

