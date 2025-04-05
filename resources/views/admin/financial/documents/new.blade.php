@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.new_document') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.new_document') }}</div>
            </div>
        </div>


        <div class="section-body card">


            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card-body">

                        <form action="{{ getAdminPanelUrl() }}/financial/documents/store" method="post">
                            {{ csrf_field() }}

                            @if(!empty($currencies) and count($currencies))
                                <div class="form-group">
                                    <label class="input-label d-block">{{ trans('admin/main.currency') }}</label>
                                    <select name="currency" class="js-ajax-currency form-control select2" data-placeholder="{{ trans('admin/main.currency') }}">
                                        <option value=""></option>
                                        @foreach($currencies as $currencyItem)
                                            <option value="{{ $currencyItem->currency }}" {{ (currency() == $currencyItem->currency) ? 'selected' : '' }} >{{ currenciesLists($currencyItem->currency) }} ({{ currencySign($currencyItem->currency) }})</option>
                                        @endforeach
                                    </select>

                                    @error('currency')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <input type="hidden" name="currency" value="{{ getDefaultCurrency() }}">
                            @endif

                            <div class="form-group">
                                <label class="control-label">{{ trans('admin/main.amount') }} ({{ currencySign(getDefaultCurrency()) }})</label>
                                <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror">

                                @error('amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label class="input-label d-block">{{ trans('admin/main.type') }}</label>
                                <select name="type" class="form-control @error('type') is-invalid @enderror">
                                    <option value="addiction">{{ trans('admin/main.addiction') }}</option>
                                    <option value="deduction">{{ trans('admin/main.deduction') }}</option>
                                </select>

                                @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="input-label d-block">{{ trans('admin/main.user') }}</label>
                                <select name="user_id" class="form-control search-user-select2 @error('user_id') is-invalid @enderror" data-placeholder="{{ trans('public.search_user') }}">

                                </select>

                                @error('user_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="control-label">{{ trans('admin/main.description') }}</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6"></textarea>
                                @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

