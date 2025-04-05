@extends('web.default.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    <div class="forms-hero position-relative" @if(!empty($form->cover)) style="background-image: url('{{ $form->cover }}')" @endif>
        <div class="forms-hero-mask"></div>

        <div class="forms-hero-content container user-select-none position-relative">
            <h1 class="font-36 text-white text-center">{{ $form->title }}</h1>
        </div>
    </div>

    <div class="forms-body container bg-white p-20">
        @yield("formContent")
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="/assets/default/js/parts/forms.min.js"></script>
@endpush
