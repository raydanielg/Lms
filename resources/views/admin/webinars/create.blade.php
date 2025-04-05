@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/bootstrap-timepicker/bootstrap-timepicker.min.css">

    <link rel="stylesheet" href="/assets/default/vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.css">
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
    <style>
        .bootstrap-timepicker-widget table td input {
            width: 35px !important;
        }

        .select2-container {
            z-index: 1212 !important;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{!empty($webinar) ?trans('/admin/main.edit'): trans('admin/main.new') }} {{ trans('admin/main.class') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active">
                    <a href="{{ getAdminPanelUrl() }}/webinars">{{ trans('admin/main.classes') }}</a>
                </div>
                <div class="breadcrumb-item">{{!empty($webinar) ?trans('/admin/main.edit'): trans('admin/main.new') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 ">
                    <div class="card">
                        <div class="card-body">

                            <form method="post" action="{{ getAdminPanelUrl() }}/webinars/{{ !empty($webinar) ? $webinar->id.'/update' : 'store' }}" id="webinarForm" class="webinar-form" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <section>
                                    <h2 class="section-title after-line">{{ trans('public.basic_information') }}</h2>

                                    <div class="row">
                                        <div class="col-12 col-md-5">

                                            @if(!empty(getGeneralSettings('content_translate')))
                                                <div class="form-group">
                                                    <label class="input-label">{{ trans('auth.language') }}</label>
                                                    <select name="locale" class="form-control {{ !empty($webinar) ? 'js-edit-content-locale' : '' }}">
                                                        @foreach($userLanguages as $lang => $language)
                                                            <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('locale')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            @else
                                                <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
                                            @endif

                                            <div class="form-group mt-15 ">
                                                <label class="input-label d-block">{{ trans('panel.course_type') }}</label>

                                                <select name="type" class="custom-select @error('type')  is-invalid @enderror">
                                                    <option value="webinar" @if((!empty($webinar) and $webinar->isWebinar()) or old('type') == \App\Models\Webinar::$webinar) selected @endif>{{ trans('webinars.webinar') }}</option>
                                                    <option value="course" @if((!empty($webinar) and $webinar->isCourse()) or old('type') == \App\Models\Webinar::$course) selected @endif>{{ trans('product.video_course') }}</option>
                                                    <option>{{ trans('product.text_course') }} (Paid plugin)</option>
                                                </select>

                                                @error('type')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.title') }}</label>
                                                <input type="text" name="title" value="{{ !empty($webinar) ? $webinar->title : old('title') }}" class="form-control @error('title')  is-invalid @enderror" placeholder=""/>
                                                @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('update.points') }}</label>
                                                <input type="number" name="points" value="{{ !empty($webinar) ? $webinar->points : old('points') }}" class="form-control @error('points')  is-invalid @enderror" placeholder="Empty means inactive this mode"/>
                                                @error('points')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('admin/main.class_url') }}</label>
                                                <input type="text" name="slug" value="{{ !empty($webinar) ? $webinar->slug : old('slug') }}" class="form-control @error('slug')  is-invalid @enderror" placeholder=""/>
                                                <div class="text-muted text-small mt-1">{{ trans('admin/main.class_url_hint') }}</div>
                                                @error('slug')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            @if(!empty($webinar) and $webinar->creator->isOrganization())
                                                <div class="form-group mt-15 ">
                                                    <label class="input-label d-block">{{ trans('admin/main.organization') }}</label>

                                                    <select name="organ_id" data-search-option="just_organization_role" class="form-control search-user-select2" data-placeholder="{{ trans('search_organization') }}">
                                                        <option value="{{ $webinar->creator->id }}" selected>{{ $webinar->creator->full_name }}</option>
                                                    </select>
                                                </div>
                                            @endif


                                            <div class="form-group mt-15 ">
                                                <label class="input-label d-block">{{ trans('admin/main.select_a_instructor') }}</label>

                                                <select name="teacher_id" data-search-option="except_user" class="form-control search-user-select22"
                                                        data-placeholder="{{ trans('public.select_a_teacher') }}"
                                                >
                                                    @if(!empty($webinar))
                                                        <option value="{{ $webinar->teacher->id }}" selected>{{ $webinar->teacher->full_name }}</option>
                                                    @else
                                                        <option selected disabled>{{ trans('public.select_a_teacher') }}</option>
                                                    @endif
                                                </select>

                                                @error('teacher_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>


                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.seo_description') }}</label>
                                                <input type="text" name="seo_description" value="{{ !empty($webinar) ? $webinar->seo_description : old('seo_description') }}" class="form-control @error('seo_description')  is-invalid @enderror"/>
                                                <div class="text-muted text-small mt-1">{{ trans('admin/main.seo_description_hint') }}</div>
                                                @error('seo_description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.thumbnail_image') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <button type="button" class="input-group-text admin-file-manager" data-input="thumbnail" data-preview="holder">
                                                            <i class="fa fa-upload"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" name="thumbnail" id="thumbnail" value="{{ !empty($webinar) ? $webinar->thumbnail : old('thumbnail') }}" class="form-control @error('thumbnail')  is-invalid @enderror"/>
                                                    <div class="input-group-append">
                                                        <button type="button" class="input-group-text admin-file-view" data-input="thumbnail">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
                                                    @error('thumbnail')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>


                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.cover_image') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <button type="button" class="input-group-text admin-file-manager" data-input="cover_image" data-preview="holder">
                                                            <i class="fa fa-upload"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" name="image_cover" id="cover_image" value="{{ !empty($webinar) ? $webinar->image_cover : old('image_cover') }}" class="form-control @error('image_cover')  is-invalid @enderror"/>
                                                    <div class="input-group-append">
                                                        <button type="button" class="input-group-text admin-file-view" data-input="cover_image">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
                                                    @error('image_cover')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group mt-25">
                                                <label class="input-label">{{ trans('public.demo_video') }} ({{ trans('public.optional') }})</label>


                                                <div class="">
                                                    <label class="input-label font-12">{{ trans('public.source') }}</label>
                                                    <select name="video_demo_source"
                                                            class="js-video-demo-source form-control"
                                                    >
                                                        @foreach(\App\Models\Webinar::$videoDemoSource as $source)
                                                            <option value="{{ $source }}" @if(!empty($webinar) and $webinar->video_demo_source == $source) selected @endif>{{ trans('update.file_source_'.$source) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="js-video-demo-other-inputs form-group mt-0 {{ (empty($webinar) or $webinar->video_demo_source != 'secure_host') ? '' : 'd-none' }}">
                                                <label class="input-label font-12">{{ trans('update.path') }}</label>
                                                <div class="input-group js-video-demo-path-input">
                                                    <div class="input-group-prepend">
                                                        <button type="button" class="js-video-demo-path-upload input-group-text admin-file-manager {{ (empty($webinar) or empty($webinar->video_demo_source) or $webinar->video_demo_source == 'upload') ? '' : 'd-none' }}" data-input="demo_video" data-preview="holder">
                                                            <i class="fa fa-upload"></i>
                                                        </button>

                                                        <button type="button" class="js-video-demo-path-links rounded-left input-group-text input-group-text-rounded-left  {{ (empty($webinar) or empty($webinar->video_demo_source) or $webinar->video_demo_source == 'upload') ? 'd-none' : '' }}">
                                                            <i class="fa fa-link"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" name="video_demo" id="demo_video" value="{{ !empty($webinar) ? $webinar->video_demo : old('video_demo') }}" class="form-control @error('video_demo')  is-invalid @enderror"/>
                                                    @error('video_demo')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group js-video-demo-secure-host-input {{ (!empty($webinar) and $webinar->video_demo_source == 'secure_host') ? '' : 'd-none' }}">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <button type="button" class="input-group-text">
                                                            <i class="fa fa-upload"></i>
                                                        </button>
                                                    </div>
                                                    <div class="custom-file js-ajax-s3_file">
                                                        <input type="file" name="video_demo_secure_host_file" class="custom-file-input cursor-pointer" id="video_demo_secure_host_file" accept="video/*">
                                                        <label class="custom-file-label cursor-pointer" for="video_demo_secure_host_file">{{ trans('update.choose_file') }}</label>
                                                    </div>

                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.description') }}</label>
                                                <textarea id="summernote" name="description" class="form-control @error('description')  is-invalid @enderror" placeholder="{{ trans('forms.webinar_description_placeholder') }}">{!! !empty($webinar) ? $webinar->description : old('description')  !!}</textarea>
                                                @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="mt-3">
                                    <h2 class="section-title after-line">{{ trans('public.additional_information') }}</h2>
                                    <div class="row">
                                        <div class="col-12 col-md-6">


                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('update.sales_count_number') }}</label>
                                                <input type="number" name="sales_count_number" value="{{ !empty($webinar) ? $webinar->sales_count_number : old('sales_count_number') }}" class="form-control @error('sales_count_number')  is-invalid @enderror"/>
                                                @error('sales_count_number')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                                <p class="mt-1 text-muted text-gray">{{ trans('update.product_sales_count_number_hint') }}</p>
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.capacity') }}</label>
                                                <input type="number" name="capacity" value="{{ !empty($webinar) ? $webinar->capacity : old('capacity') }}" class="form-control @error('capacity')  is-invalid @enderror"/>
                                                @error('capacity')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="row mt-15">
                                                @if(empty($webinar) or (!empty($webinar) and $webinar->isWebinar()))
                                                    <div class="col-12 col-md-6 js-start_date {{ (!empty(old('type')) and old('type') != \App\Models\Webinar::$webinar) ? 'd-none' : '' }}">
                                                        <div class="form-group">
                                                            <label class="input-label">{{ trans('public.start_date') }}</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="dateInputGroupPrepend">
                                                                        <i class="fa fa-calendar-alt "></i>
                                                                    </span>
                                                                </div>
                                                                <input type="text" name="start_date" value="{{ (!empty($webinar) and $webinar->start_date) ? dateTimeFormat($webinar->start_date, 'Y-m-d H:i', false, false, $webinar->timezone) : old('start_date') }}" class="form-control @error('start_date')  is-invalid @enderror datetimepicker" aria-describedby="dateInputGroupPrepend"/>
                                                                @error('start_date')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label class="input-label">{{ trans('public.duration') }} ({{ trans('public.minutes') }})</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text" id="timeInputGroupPrepend">
                                                                    <i class="fa fa-clock"></i>
                                                                </span>
                                                            </div>


                                                            <input type="number" name="duration" value="{{ !empty($webinar) ? $webinar->duration : old('duration') }}" class="form-control @error('duration')  is-invalid @enderror"/>
                                                            @error('duration')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(getFeaturesSettings('timezone_in_create_webinar'))
                                                @php
                                                    $selectedTimezone = getGeneralSettings('default_time_zone');

                                                    if (!empty($webinar) and !empty($webinar->timezone)) {
                                                        $selectedTimezone = $webinar->timezone;
                                                    }
                                                @endphp

                                                <div class="form-group">
                                                    <label class="input-label">{{ trans('update.timezone') }}</label>
                                                    <select name="timezone" class="form-control select2" data-allow-clear="false">
                                                        @foreach(getListOfTimezones() as $timezone)
                                                            <option value="{{ $timezone }}" @if($selectedTimezone == $timezone) selected @endif>{{ $timezone }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('timezone')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            @endif

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="supportSwitch">{{ trans('panel.support') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="support" class="custom-control-input" id="supportSwitch" {{ !empty($webinar) && $webinar->support ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="supportSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="includeCertificateSwitch">{{ trans('update.include_certificate') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="certificate" class="custom-control-input" id="includeCertificateSwitch" {{ !empty($webinar) && $webinar->certificate ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="includeCertificateSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="cursor-pointer" for="downloadableSwitch">{{ trans('home.downloadable') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="downloadable" class="custom-control-input" id="downloadableSwitch" {{ !empty($webinar) && $webinar->downloadable ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="downloadableSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="partnerInstructorSwitch">{{ trans('public.partner_instructor') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="partner_instructor" class="custom-control-input" id="partnerInstructorSwitch" {{ !empty($webinar) && $webinar->partner_instructor ? 'checked' : ''  }}>
                                                    <label class="custom-control-label" for="partnerInstructorSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="forumSwitch">{{ trans('update.course_forum') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="forum" class="custom-control-input" id="forumSwitch" {{ !empty($webinar) && $webinar->forum ? 'checked' : ''  }}>
                                                    <label class="custom-control-label" for="forumSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="subscribeSwitch">{{ trans('public.subscribe') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="subscribe" class="custom-control-input" id="subscribeSwitch" {{ !empty($webinar) && $webinar->subscribe ? 'checked' : ''  }}>
                                                    <label class="custom-control-label" for="subscribeSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="privateSwitch">{{ trans('webinars.private') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="private" class="custom-control-input" id="privateSwitch" {{ (!empty($webinar) and $webinar->private) ? 'checked' : ''  }}>
                                                    <label class="custom-control-label" for="privateSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-30 d-flex align-items-center justify-content-between">
                                                <label class="" for="enable_waitlistSwitch">{{ trans('update.enable_waitlist') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="enable_waitlist" class="custom-control-input" id="enable_waitlistSwitch" {{ (!empty($webinar) and $webinar->enable_waitlist) ? 'checked' : ''  }}>
                                                    <label class="custom-control-label" for="enable_waitlistSwitch"></label>
                                                </div>
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('update.access_days') }}</label>
                                                <input type="text" name="access_days" value="{{ !empty($webinar) ? $webinar->access_days : old('access_days') }}" class="form-control @error('access_days')  is-invalid @enderror"/>
                                                @error('access_days')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                                <p class="mt-1">- {{ trans('update.access_days_input_hint') }}</p>
                                            </div>

                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.price') }} ({{ $currency }})</label>
                                                <input type="text" name="price" value="{{ (!empty($webinar) and !empty($webinar->price)) ? convertPriceToUserCurrency($webinar->price) : old('price') }}" class="form-control @error('price')  is-invalid @enderror" placeholder="{{ trans('public.0_for_free') }}"/>
                                                @error('price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            @if(!empty($webinar) and $webinar->creator->isOrganization())
                                                <div class="form-group mt-15">
                                                    <label class="input-label">{{ trans('update.organization_price') }} ({{ $currency }})</label>
                                                    <input type="number" name="organization_price" value="{{ (!empty($webinar) and $webinar->organization_price) ? convertPriceToUserCurrency($webinar->organization_price) : old('organization_price') }}" class="form-control @error('organization_price')  is-invalid @enderror" placeholder=""/>
                                                    @error('organization_price')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                    <p class="font-12 text-gray mt-1">- {{ trans('update.organization_price_hint') }}</p>
                                                </div>
                                            @endif

                                            {{-- Product Badges --}}
                                            @if(!empty($webinar))
                                                @include('admin.product_badges.content_include', ['itemTarget' => $webinar])
                                            @endif

                                            <div id="partnerInstructorInput" class="form-group mt-15 {{ (!empty($webinar) && $webinar->partner_instructor) ? '' : 'd-none' }}">
                                                <label class="input-label d-block">{{ trans('public.select_a_partner_teacher') }}</label>

                                                <select name="partners[]" multiple data-search-option="just_teacher_role" class="js-search-partner-user form-control {{ (!empty($webinar) && $webinar->partner_instructor) ? 'search-user-select22' : '' }}"
                                                        data-placeholder="{{ trans('public.search_instructor') }}"
                                                >
                                                    @if(!empty($webinarPartnerTeacher))
                                                        @foreach($webinarPartnerTeacher as $partner)
                                                            @if(!empty($partner) and $partner->teacher)
                                                                <option value="{{ $partner->teacher->id }}" selected>{{ $partner->teacher->full_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>

                                                <div class="text-muted text-small mt-1">{{ trans('admin/main.select_a_partner_hint') }}</div>
                                            </div>


                                            <div class="form-group mt-15">
                                                <label class="input-label d-block">{{ trans('public.tags') }}</label>
                                                <input type="text" name="tags" data-max-tag="5" value="{{ !empty($webinar) ? implode(',',$webinarTags) : '' }}" class="form-control inputtags" placeholder="{{ trans('public.type_tag_name_and_press_enter') }} ({{ trans('admin/main.max') }} : 5)"/>
                                            </div>


                                            <div class="form-group mt-15">
                                                <label class="input-label">{{ trans('public.category') }}</label>

                                                <select id="categories" class="custom-select @error('category_id')  is-invalid @enderror" name="category_id" required>
                                                    <option {{ !empty($webinar) ? '' : 'selected' }} disabled>{{ trans('public.choose_category') }}</option>
                                                    @foreach($categories as $category)
                                                        @if(!empty($category->subCategories) and count($category->subCategories))
                                                            <optgroup label="{{  $category->title }}">
                                                                @foreach($category->subCategories as $subCategory)
                                                                    <option value="{{ $subCategory->id }}" {{ (!empty($webinar) and $webinar->category_id == $subCategory->id) ? 'selected' : '' }}>{{ $subCategory->title }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        @else
                                                            <option value="{{ $category->id }}" {{ (!empty($webinar) and $webinar->category_id == $category->id) ? 'selected' : '' }}>{{ $category->title }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>

                                                @error('category_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group mt-15 {{ (!empty($webinarCategoryFilters) and count($webinarCategoryFilters)) ? '' : 'd-none' }}" id="categoriesFiltersContainer">
                                        <span class="input-label d-block">{{ trans('public.category_filters') }}</span>
                                        <div id="categoriesFiltersCard" class="row mt-3">

                                            @if(!empty($webinarCategoryFilters) and count($webinarCategoryFilters))
                                                @foreach($webinarCategoryFilters as $filter)
                                                    <div class="col-12 col-md-3">
                                                        <div class="webinar-category-filters">
                                                            <strong class="category-filter-title d-block">{{ $filter->title }}</strong>
                                                            <div class="py-10"></div>

                                                            @foreach($filter->options as $option)
                                                                <div class="form-group mt-3 d-flex align-items-center justify-content-between">
                                                                    <label class="text-gray font-14" for="filterOptions{{ $option->id }}">{{ $option->title }}</label>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="filters[]" value="{{ $option->id }}" {{ ((!empty($webinarFilterOptions) && in_array($option->id,$webinarFilterOptions)) ? 'checked' : '') }} class="custom-control-input" id="filterOptions{{ $option->id }}">
                                                                        <label class="custom-control-label" for="filterOptions{{ $option->id }}"></label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                        </div>
                                    </div>
                                </section>

                                @if(!empty($webinar))
                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('admin/main.price_plans') }}</h2>
                                            <button id="webinarAddTicket" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('admin/main.add_price_plan') }}</button>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-12">

                                                @if(!empty($tickets) and !$tickets->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th>{{ trans('public.discount') }}</th>
                                                                <th>{{ trans('public.capacity') }}</th>
                                                                <th>{{ trans('public.date') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($tickets as $ticket)
                                                                <tr>
                                                                    <th scope="row">{{ $ticket->title }}</th>
                                                                    <td>{{ $ticket->discount }}%</td>
                                                                    <td>{{ $ticket->capacity }}</td>
                                                                    <td>{{ dateTimeFormat($ticket->start_date, 'j M Y') }} - {{ dateTimeFormat($ticket->end_date, 'j M Y') }}</td>
                                                                    <td>
                                                                        <button type="button" data-ticket-id="{{ $ticket->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-ticket btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>

                                                                        @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/tickets/'. $ticket->id .'/delete', 'btnClass' => ' mt-1'])
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'ticket.png',
                                                        'title' => trans('public.ticket_no_result'),
                                                        'hint' => trans('public.ticket_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>


                                    @include('admin.webinars.create_includes.contents')


                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('public.prerequisites') }}</h2>
                                            <button id="webinarAddPrerequisites" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_prerequisites') }}</button>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-12">
                                                @if(!empty($prerequisites) and !$prerequisites->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th class="text-left">{{ trans('public.instructor') }}</th>
                                                                <th>{{ trans('public.price') }}</th>
                                                                <th>{{ trans('public.publish_date') }}</th>
                                                                <th>{{ trans('public.forced') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($prerequisites as $prerequisite)
                                                                @if(!empty($prerequisite->prerequisiteWebinar->title))
                                                                    <tr>
                                                                        <th>{{ $prerequisite->prerequisiteWebinar->title }}</th>
                                                                        <td class="text-left">{{ $prerequisite->prerequisiteWebinar->teacher->full_name }}</td>
                                                                        <td>{{  handlePrice($prerequisite->prerequisiteWebinar->price) }}</td>
                                                                        <td>{{ dateTimeFormat($prerequisite->prerequisiteWebinar->created_at,'j F Y | H:i') }}</td>
                                                                        <td>{{ $prerequisite->required ? trans('public.yes') : trans('public.no') }}</td>

                                                                        <td>
                                                                            <button type="button" data-prerequisite-id="{{ $prerequisite->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-prerequisite btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                                <i class="fa fa-edit"></i>
                                                                            </button>

                                                                            @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/prerequisites/'. $prerequisite->id .'/delete', 'btnClass' => ' mt-1'])
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'comment.png',
                                                        'title' => trans('public.prerequisites_no_result'),
                                                        'hint' => trans('public.prerequisites_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>


                                    {{-- Related Course --}}
                                    @include('admin.webinars.relatedCourse.add_related_course', [
                                            'relatedCourseItemId' => $webinar->id,
                                             'relatedCourseItemType' => 'webinar',
                                             'relatedCourses' => $webinar->relatedCourses
                                        ])


                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('public.faq') }}</h2>
                                            <button id="webinarAddFAQ" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_faq') }}</button>
                                        </div>

                                        <div class="row mt-10">
                                            <div class="col-12">
                                                @if(!empty($faqs) and !$faqs->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th>{{ trans('public.answer') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($faqs as $faq)
                                                                <tr>
                                                                    <th>{{ $faq->title }}</th>
                                                                    <td>
                                                                        <button type="button" class="js-get-faq-description btn btn-sm btn-gray200">{{ trans('public.view') }}</button>
                                                                        <input type="hidden" value="{{ $faq->answer }}"/>
                                                                    </td>

                                                                    <td class="text-right">
                                                                        <button type="button" data-faq-id="{{ $faq->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-faq btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>

                                                                        @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/faqs/'. $faq->id .'/delete', 'btnClass' => ' mt-1'])
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'faq.png',
                                                        'title' => trans('public.faq_no_result'),
                                                        'hint' => trans('public.faq_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>

                                    @foreach(\App\Models\WebinarExtraDescription::$types as $webinarExtraDescriptionType)
                                        <section class="mt-30">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h2 class="section-title after-line">{{ trans('update.'.$webinarExtraDescriptionType) }}</h2>
                                                <button id="add_new_{{ $webinarExtraDescriptionType }}" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('update.add_'.$webinarExtraDescriptionType) }}</button>
                                            </div>

                                            @php
                                                $webinarExtraDescriptionValues = $webinar->webinarExtraDescription->where('type',$webinarExtraDescriptionType);
                                            @endphp

                                            <div class="row mt-10">
                                                <div class="col-12">
                                                    @if(!empty($webinarExtraDescriptionValues) and count($webinarExtraDescriptionValues))
                                                        <div class="table-responsive">
                                                            <table class="table table-striped text-center font-14">

                                                                <tr>
                                                                    @if($webinarExtraDescriptionType == \App\Models\WebinarExtraDescription::$COMPANY_LOGOS)
                                                                        <th>{{ trans('admin/main.icon') }}</th>
                                                                    @else
                                                                        <th>{{ trans('public.title') }}</th>
                                                                    @endif
                                                                    <th></th>
                                                                </tr>

                                                                @foreach($webinarExtraDescriptionValues as $extraDescription)
                                                                    <tr>
                                                                        @if($webinarExtraDescriptionType == \App\Models\WebinarExtraDescription::$COMPANY_LOGOS)
                                                                            <td>
                                                                                <img src="{{ $extraDescription->value }}" class="webinar-extra-description-company-logos" alt="">
                                                                            </td>
                                                                        @else
                                                                            <td>{{ $extraDescription->value }}</td>
                                                                        @endif

                                                                        <td class="text-right">
                                                                            <button type="button" data-item-id="{{ $extraDescription->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-extraDescription btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                                <i class="fa fa-edit"></i>
                                                                            </button>

                                                                            @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/webinar-extra-description/'. $extraDescription->id .'/delete', 'btnClass' => ' mt-1'])
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                            </table>
                                                        </div>
                                                    @else
                                                        @include('admin.includes.no-result',[
                                                             'file_name' => 'faq.png',
                                                             'title' => trans("update.{$webinarExtraDescriptionType}_no_result"),
                                                             'hint' => trans("update.{$webinarExtraDescriptionType}_no_result_hint"),
                                                        ])
                                                    @endif
                                                </div>
                                            </div>
                                        </section>
                                    @endforeach

                                    <section class="mt-30">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h2 class="section-title after-line">{{ trans('public.quiz_certificate') }}</h2>
                                            <button id="webinarAddQuiz" type="button" class="btn btn-primary btn-sm mt-3">{{ trans('public.add_quiz') }}</button>
                                        </div>
                                        <div class="row mt-10">
                                            <div class="col-12">
                                                @if(!empty($webinarQuizzes) and !$webinarQuizzes->isEmpty())
                                                    <div class="table-responsive">
                                                        <table class="table table-striped text-center font-14">

                                                            <tr>
                                                                <th>{{ trans('public.title') }}</th>
                                                                <th>{{ trans('public.questions') }}</th>
                                                                <th>{{ trans('public.total_mark') }}</th>
                                                                <th>{{ trans('public.pass_mark') }}</th>
                                                                <th>{{ trans('public.certificate') }}</th>
                                                                <th></th>
                                                            </tr>

                                                            @foreach($webinarQuizzes as $webinarQuiz)
                                                                <tr>
                                                                    <th>{{ $webinarQuiz->title }}</th>
                                                                    <td>{{ $webinarQuiz->quizQuestions->count() }}</td>
                                                                    <td>{{ $webinarQuiz->quizQuestions->sum('grade') }}</td>
                                                                    <td>{{ $webinarQuiz->pass_mark }}</td>
                                                                    <td>{{ $webinarQuiz->certificate ? trans('public.yes') : trans('public.no') }}</td>
                                                                    <td>
                                                                        <button type="button" data-webinar-quiz-id="{{ $webinarQuiz->id }}" data-webinar-id="{{ !empty($webinar) ? $webinar->id : '' }}" class="edit-webinar-quiz btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>

                                                                        @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/webinar-quiz/'. $webinarQuiz->id .'/delete', 'btnClass' => ' mt-1'])
                                                                    </td>
                                                                    @endforeach
                                                                </tr>

                                                        </table>
                                                    </div>
                                                @else
                                                    @include('admin.includes.no-result',[
                                                        'file_name' => 'cert.png',
                                                        'title' => trans('public.quizzes_no_result'),
                                                        'hint' => trans('public.quizzes_no_result_hint'),
                                                    ])
                                                @endif
                                            </div>
                                        </div>
                                    </section>
                                @endif

                                <section class="mt-3">
                                    <h2 class="section-title after-line">{{ trans('public.message_to_reviewer') }}</h2>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group mt-15">
                                                <textarea name="message_for_reviewer" rows="10" class="form-control">{{ (!empty($webinar) && $webinar->message_for_reviewer) ? $webinar->message_for_reviewer : old('message_for_reviewer') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <input type="hidden" name="draft" value="no" id="forDraft"/>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="saveAndPublish" class="btn btn-success">{{ !empty($webinar) ? trans('admin/main.save_and_publish') : trans('admin/main.save_and_continue') }}</button>

                                        @if(!empty($webinar))
                                            <button type="button" id="saveReject" class="btn btn-warning">{{ ($webinar->status == "active") ? trans('update.unpublish') : trans('public.reject') }}</button>

                                            @include('admin.includes.delete_button',[
                                                    'url' => getAdminPanelUrl().'/webinars/'. $webinar->id .'/delete',
                                                    'btnText' => trans('public.delete'),
                                                    'hideDefaultClass' => true,
                                                    'btnClass' => 'btn btn-danger'
                                                    ])
                                        @endif
                                    </div>
                                </div>
                            </form>


                            @include('admin.webinars.modals.prerequisites')
                            @include('admin.webinars.modals.quizzes')
                            @include('admin.webinars.modals.ticket')
                            @include('admin.webinars.modals.chapter')
                            @include('admin.webinars.modals.session')
                            @include('admin.webinars.modals.file')
                            @include('admin.webinars.modals.interactive_file')
                            @include('admin.webinars.modals.faq')
                            @include('admin.webinars.modals.testLesson')
                            @include('admin.webinars.modals.assignment')
                            @include('admin.webinars.modals.extra_description')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var titleLang = '{{ trans('admin/main.title') }}';
        var zoomJwtTokenInvalid = '{{ trans('admin/main.teacher_zoom_jwt_token_invalid') }}';
        var editChapterLang = '{{ trans('public.edit_chapter') }}';
        var requestFailedLang = '{{ trans('public.request_failed') }}';
        var thisLiveHasEndedLang = '{{ trans('update.this_live_has_been_ended') }}';
        var quizzesSectionLang = '{{ trans('quiz.quizzes_section') }}';
        var filePathPlaceHolderBySource = {
            upload: '{{ trans('update.file_source_upload_placeholder') }}',
            youtube: '{{ trans('update.file_source_youtube_placeholder') }}',
            vimeo: '{{ trans('update.file_source_vimeo_placeholder') }}',
            external_link: '{{ trans('update.file_source_external_link_placeholder') }}',
            google_drive: '{{ trans('update.file_source_google_drive_placeholder') }}',
            dropbox: '{{ trans('update.file_source_dropbox_placeholder') }}',
            iframe: '{{ trans('update.file_source_iframe_placeholder') }}',
            s3: '{{ trans('update.file_source_s3_placeholder') }}',
        }
    </script>

    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/feather-icons/dist/feather.min.js"></script>

    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/vendors/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
    <script src="/assets/default/vendors/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>

    <script src="/assets/default/js/admin/quiz.min.js"></script>
    <script src="/assets/admin/js/webinar.min.js"></script>
@endpush
