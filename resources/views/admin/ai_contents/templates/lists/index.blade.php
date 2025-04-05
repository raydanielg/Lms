@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('update.service_templates') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('update.service_templates') }}</div>
            </div>
        </div>

        <div class="section-body">

<div class="row">
    <div class="col-12 col-md-12">
        <div class="card">
            <div class="card-body">



                <div class="empty-state mx-auto d-block"  data-width="900" >
                    <img class="img-fluid col-md-6" src="/assets/default/img/plugin.svg" alt="image">
                    <h3 class="mt-3">This is a paid plugin!</h3>
                    <h5 class="lead">
                        You can purchase it by <strong><a href="https://codecanyon.net/item/universal-plugins-bundle-for-rocket-lms/33297004">this link</a></strong> on Codecanyon.
                    </h5>             
                  </div>


                
            </div>

          

        </div>
    </div>
</div>
</div>


    </section>
@endsection

@push('scripts_bottom')

@endpush
