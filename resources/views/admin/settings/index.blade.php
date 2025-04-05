@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.settings') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="#">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.settings') }}</div>
            </div>
        </div>

<div class="section-body">
            <h2 class="section-title">Control Everything</h2>
            <p class="section-lead">
                You can change all of the parameters and variables using the following cards.
            </p>

            <div class="row">
                                    <div class="col-lg-6">
                        <div class="card card-large-icons">
                            <div class="card-icon bg-primary text-white">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="card-body">
                                <h4>General</h4>
                                <p>Change website title, logo, language, RTL, social accounts, design styles, preloading.</p>
                                <a href="/admin/settings/general" class="card-cta">Change Settings<i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                
                                    <div class="col-lg-6">
                        <div class="card card-large-icons">
                            <div class="card-icon bg-primary text-white">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="card-body">
                                <h4>Financial</h4>
                                <p>Define comission rates, tax, payout, currency, payment gateways, offline payment</p>
                                <a href="/admin/settings/financial" class="card-cta">Change Settings<i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                
                                    <div class="col-lg-6">
                        <div class="card card-large-icons">
                            <div class="card-icon bg-primary text-white">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <div class="card-body">
                                <h4>Personalization</h4>
                                <p>Change page backgrounds, home sections, hero styles, images &amp; texts.</p>
                                <a href="/admin/settings/personalization/page_background" class="card-cta">Change Settings<i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                
                                    <div class="col-lg-6">
                        <div class="card card-large-icons">
                            <div class="card-icon bg-primary text-white">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="card-body">
                                <h4>Notifications</h4>
                                <p>Assign notification templates to processes.</p>
                                <a href="/admin/settings/notifications" class="card-cta">Change Settings<i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                
                                    <div class="col-lg-6">
                        <div class="card card-large-icons">
                            <div class="card-icon bg-primary text-white">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="card-body">
                                <h4>SEO</h4>
                                <p>Define SEO title, meta description, and search engine crawl access for each page.</p>
                                <a href="/admin/settings/seo" class="card-cta">Change Settings<i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                
                                    <div class="col-lg-6">
                        <div class="card card-large-icons">
                            <div class="card-icon bg-primary text-white">
                                <i class="fas fa-list-alt"></i>
                            </div>
                            <div class="card-body">
                                <h4>Customization</h4>
                                <p>Define additional CSS &amp; JS codes.</p>
                                <a href="/admin/settings/customization" class="card-cta text-primary">Change Settings<i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                
                                    <div class="col-lg-6">
                        <div class="card card-large-icons">
                            <div class="card-icon bg-primary text-white">
                                <i class="fas fa-list-alt"></i>
                            </div>
                            <div class="card-body">
                                <h4>Update App</h4>
                                <p>Update your platform to the latest version easily (disabled)</p>
                                <a href="/admin/settings/update-app" class="card-cta text-primary">Change Settings<i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                            </div>
        </div>
    </section>
@endsection
