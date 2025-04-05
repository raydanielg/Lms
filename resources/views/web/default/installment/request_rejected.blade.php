@extends('web.default.layouts.app')

@section('content')
    <div class="container mt-20 my-50">
        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-md-8">
                <div class="installment-request-card d-flex align-items-center justify-content-center flex-column border rounded-lg">
                    <img src="/assets/default/img/installment/request_rejected.svg" alt="{{ trans('update.installment_request_rejected') }}" width="267" height="265">

                    <h1 class="font-20 mt-30">{{ trans('update.installment_request_rejected') }}</h1>
                    <p class="font-14 text-gray mt-5">{{ trans('update.installment_request_rejected_hint') }}</p>

                    <a href="/panel/financial/installments" class="btn btn-primary mt-15">{{ trans('update.back_to_installments') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
