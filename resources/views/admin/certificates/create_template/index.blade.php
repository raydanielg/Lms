@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
    <link rel="stylesheet" href="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.css">

    <style>
        .certificate-template-container {
            width: {{ \App\Models\CertificateTemplate::$templateWidth }}px;
            height: {{ \App\Models\CertificateTemplate::$templateHeight }}px;
            position: relative;
            border: 2px solid #000;
            background-repeat: no-repeat;
            background-size: contain;
        }

        .certificate-template-container .draggable-element {
            position: absolute !important;
            display: inline-block;
            white-space: pre-wrap;
        }

        .certificate-template-container .draggable-element[data-name="qr_code"] {
            border: 1px solid #0b2e13;
            display: flex;
            align-items: center;
            justify-content: center;
        }

    </style>
@endpush


@section('content')

    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.new_template') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.new_template') }}</div>
            </div>
        </div>


        <div class="row mt-3">
            {{-- Inputs --}}
            <div class="col-12 col-lg-3">
                <form action="{{ getAdminPanelUrl("/certificates/templates/").(!empty($template) ? ($template->id.'/update') : 'store') }}" method="post">
                    {{ csrf_field() }}

                    @include('admin.certificates.create_template.template-form')
                </form>
            </div>

            {{-- Certificate Container --}}
            <div class="col-12 col-lg-9">
                @include('admin.certificates.create_template.draggable-section')


                <section class="card">
                    <div class="card-body">
                        <div class="section-title ml-0 mt-0 mb-3"><h4>{{trans('admin/main.hints')}}</h4></div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="media-body">
                                    <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('update.certificate_template_hint_title_1')}}</div>
                                    <div class=" text-small font-600-bold">{{trans('update.certificate_template_hint_description_1')}}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="media-body">
                                    <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('update.certificate_template_hint_title_2')}}</div>
                                    <div class=" text-small font-600-bold">{{trans('update.certificate_template_hint_description_2')}}</div>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="media-body">
                                    <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('update.certificate_template_hint_title_3')}}</div>
                                    <div class="text-small font-600-bold">{{trans('update.certificate_template_hint_description_3')}}</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

            </div>

        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
    <script src="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

    <script src="/assets/default/js/admin/create_certificate_template.min.js"></script>

@endpush
