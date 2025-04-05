@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('update.advertising_modal') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('update.advertising_modal') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ getAdminPanelUrl() }}/advertising_modal" method="post">
                                {{ csrf_field() }}

                                <div class="row">

                                    <div class="col-12 col-md-6">

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.image') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text admin-file-manager" data-input="image" data-preview="holder">
                                                        <i class="fa fa-chevron-up"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="value[image]" id="image" value="{{ (!empty($value) and !empty($value['image'])) ? $value['image'] : old('image') }}" class="form-control"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('admin/main.title') }}</label>
                                            <input type="text" name="value[title]" value="{{ (!empty($value) and !empty($value['title'])) ? $value['title'] : old('title') }}" class="form-control "/>
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('public.description') }}</label>
                                            <textarea type="text" name="value[description]" rows="5" class="form-control ">{{ (!empty($value) and !empty($value['description'])) ? $value['description'] : old('description') }}</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('update.button') }} 1</label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>{{ trans('admin/main.title') }}</label>
                                                    <input type="text" name="value[button1][title]" value="{{ (!empty($value) and !empty($value['button1'])) ? $value['button1']['title'] : '' }}" class="form-control "/>
                                                </div>
                                                <div class="col-6">
                                                    <label>{{ trans('admin/main.link') }}</label>
                                                    <input type="text" name="value[button1][link]" value="{{ (!empty($value) and !empty($value['button1'])) ? $value['button1']['link'] : '' }}" class="form-control "/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('update.button') }} 2</label>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>{{ trans('admin/main.title') }}</label>
                                                    <input type="text" name="value[button2][title]" value="{{ (!empty($value) and !empty($value['button2'])) ? $value['button2']['title'] : '' }}" class="form-control "/>
                                                </div>
                                                <div class="col-6">
                                                    <label>{{ trans('admin/main.link') }}</label>
                                                    <input type="text" name="value[button2][link]" value="{{ (!empty($value) and !empty($value['button2'])) ? $value['button2']['link'] : '' }}" class="form-control "/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group custom-switches-stacked">
                                            <label class="custom-switch pl-0 d-flex align-items-center">
                                                <input type="hidden" name="value[status]" value="0">
                                                <input type="checkbox" name="value[status]" id="advertiseModalStatusSwitch" value="1" {{ (!empty($value) and !empty($value['status']) and $value['status']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                                <span class="custom-switch-indicator"></span>
                                                <label class="custom-switch-description mb-0 cursor-pointer" for="advertiseModalStatusSwitch">{{ trans('admin/main.active') }}</label>
                                            </label>
                                            <div class="text-muted text-small mt-1">{{ trans('update.advertising_modal_status_hint') }}</div>
                                        </div>

                                    </div>
                                </div>

                                <div class="">
                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
                                    <button type="button" class="js-preview-modal btn btn-warning ml-2">{{ trans('update.preview') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/js/admin/advertising_modal.min.js"></script>
@endpush
