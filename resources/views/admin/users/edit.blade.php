@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('/admin/main.edit') }} {{ trans('admin/main.user') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}/users">{{ trans('admin/main.users') }}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('/admin/main.edit') }}</div>
            </div>
        </div>

        @if(!empty(session()->has('msg')))
            <div class="alert alert-success my-25">
                {{ session()->get('msg') }}
            </div>
        @endif


        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link @if(empty($becomeInstructor) and empty(request()->get('tab'))) active @endif" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">{{ trans('admin/main.main_general') }}</a>
                                </li>

                                @if(!empty($formFieldsHtml))
                                    <li class="nav-item">
                                        <a class="nav-link" id="extra_information-tab" data-toggle="tab" href="#extra_information" role="tab" aria-controls="extra_information" aria-selected="true">{{ trans('site.extra_information') }}</a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link" id="images-tab" data-toggle="tab" href="#images" role="tab" aria-controls="images" aria-selected="true">{{ trans('auth.images') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="financial-tab" data-toggle="tab" href="#financial" role="tab" aria-controls="financial" aria-selected="true">{{ trans('admin/main.financial') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="occupations-tab" data-toggle="tab" href="#occupations" role="tab" aria-controls="occupations" aria-selected="true">{{ trans('site.occupations') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="badges-tab" data-toggle="tab" href="#badges" role="tab" aria-controls="badges" aria-selected="true">{{ trans('admin/main.badges') }}</a>
                                </li>

                                @if(!empty($user) and ($user->isOrganization() or $user->isTeacher()))
                                    @can('admin_update_user_registration_package')
                                        <li class="nav-item">
                                            <a class="nav-link" id="registrationPackage-tab" data-toggle="tab" href="#registrationPackage" role="tab" aria-controls="registrationPackage" aria-selected="true">{{ trans('update.registration_package') }}</a>
                                        </li>
                                    @endcan
                                @endif

                                @if(!empty($user) and ($user->isOrganization() or $user->isTeacher()))
                                    @can('admin_update_user_meeting_settings')
                                        <li class="nav-item">
                                            <a class="nav-link" id="meetingSettings-tab" data-toggle="tab" href="#meetingSettings" role="tab" aria-controls="meetingSettings" aria-selected="true">{{ trans('update.meeting_settings') }}</a>
                                        </li>
                                    @endcan
                                @endif

                                @if(!empty($becomeInstructor))
                                    <li class="nav-item">
                                        <a class="nav-link @if(!empty($becomeInstructor)) active @endif" id="become_instructor-tab" data-toggle="tab" href="#become_instructor" role="tab" aria-controls="become_instructor" aria-selected="true">{{ trans('admin/main.become_instructor_info') }}</a>
                                    </li>
                                @endif


                                <li class="nav-item">
                                    <a class="nav-link" id="purchased_courses-tab" data-toggle="tab" href="#purchased_courses" role="tab" aria-controls="purchased_courses" aria-selected="true">{{ trans('update.purchased_courses') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="purchased_bundles-tab" data-toggle="tab" href="#purchased_bundles" role="tab" aria-controls="purchased_bundles" aria-selected="true">{{ trans('update.purchased_bundles') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="purchased_products-tab" data-toggle="tab" href="#purchased_products" role="tab" aria-controls="purchased_products" aria-selected="true">{{ trans('update.purchased_products') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="topics-tab" data-toggle="tab" href="#topics" role="tab" aria-controls="topics" aria-selected="true">{{ trans('update.forum_topics') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ (request()->get('tab') == "loginHistory") ? 'active' : '' }}" href="{{ getAdminPanelUrl("/users/{$user->id}/edit?tab=loginHistory") }}" role="tab" aria-controls="loginHistory" aria-selected="true">{{ trans('update.login_history') }}</a>
                                </li>

                            </ul>

                            <div class="tab-content" id="myTabContent2">

                                @include('admin.users.editTabs.general')

                                @if(!empty($formFieldsHtml))
                                    @include('admin.users.editTabs.extra_information')
                                @endif

                                @include('admin.users.editTabs.images')

                                @include('admin.users.editTabs.financial')

                                @include('admin.users.editTabs.occupations')

                                @include('admin.users.editTabs.badges')

                                @if(!empty($user) and ($user->isOrganization() or $user->isTeacher()))
                                    @can('admin_update_user_registration_package')
                                        @include('admin.users.editTabs.registration_package')
                                    @endcan
                                @endif

                                @if(!empty($user) and ($user->isOrganization() or $user->isTeacher()))
                                    @can('admin_update_user_meeting_settings')
                                        @include('admin.users.editTabs.meeting_settings')
                                    @endcan
                                @endif

                                @if(!empty($becomeInstructor))
                                    @include('admin.users.editTabs.become_instructor')
                                @endif

                                @include('admin.users.editTabs.purchased_courses')

                                @include('admin.users.editTabs.purchased_bundles')

                                @include('admin.users.editTabs.purchased_products')

                                @include('admin.users.editTabs.topics')

                                @include('admin.users.editTabs.login_history')

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>


    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
    </script>

    <script src="/assets/default/js/admin/webinar_students.min.js"></script>
    <script src="/assets/default/js/admin/user_edit.min.js"></script>
@endpush
