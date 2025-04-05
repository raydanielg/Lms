@extends('web.default.layouts.app')

@section('content')
    <div class="container mt-20 my-50">
        <div class="row align-items-center justify-content-center">
            <div class="col-12 col-md-8">
                <div class="installment-request-card d-flex align-items-center justify-content-center flex-column border rounded-lg">
                    <img src="/assets/default/img/course/course_access_denied.svg" alt="{{ trans('update.access_denied') }}" width="267" height="265">

                    <h1 class="font-20 mt-30">{{ trans('update.access_denied') }}</h1>
                    <p class="font-14 text-gray mt-5">{{ trans('update.this_course_is_not_accessible_publicly_at_this_moment') }}</p>

                    <a href="/" class="btn btn-primary mt-15">{{ trans('update.back_to_home') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
