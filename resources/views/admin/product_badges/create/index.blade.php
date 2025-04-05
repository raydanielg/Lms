@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('update.product_badges') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('update.product_badges') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ getAdminPanelUrl('/product-badges/').(!empty($badge) ? $badge->id.'/update' : 'store') }}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="row">

                                    <div class="col-12 col-md-6">

                                        @if(!empty(getGeneralSettings('content_translate')))
                                            <div class="form-group">
                                                <label class="input-label">{{ trans('auth.language') }}</label>
                                                <select name="locale" class="form-control {{ !empty($badge) ? 'js-edit-content-locale' : '' }}">
                                                    @foreach($userLanguages as $lang => $language)
                                                        <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }} {{ (!empty($definedLanguage) and is_array($definedLanguage) and in_array(mb_strtolower($lang), $definedLanguage)) ? '('. trans('panel.content_defined') .')' : '' }}</option>
                                                    @endforeach
                                                </select>
                                                @error('locale')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        @else
                                            <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
                                        @endif

                                        <div class="form-group">
                                            <label>{{ trans('admin/main.title') }}</label>
                                            <input type="text" name="title" value="{{ !empty($badge) ? $badge->title : old('title') }}" class="form-control "/>
                                            @error('title')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.icon') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text admin-file-manager" data-input="icon" data-preview="holder">
                                                        <i class="fa fa-chevron-up"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="icon" id="icon" value="{{ (!empty($badge)) ? $badge->icon : old('icon') }}" class="form-control"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('admin/main.text_color') }}</label>

                                            <div class="input-group colorpickerinput">
                                                <input type="text" name="color"
                                                       autocomplete="off"
                                                       class="form-control  @error('color') is-invalid @enderror"
                                                       value="{{ !empty($badge) ? $badge->color : old('color') }}"
                                                       placeholder="{{ trans('admin/main.trend_color_placeholder') }}"
                                                />

                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-fill-drip"></i>
                                                    </div>
                                                </div>

                                                @error('color')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>{{ trans('admin/main.background') }}</label>

                                            <div class="input-group colorpickerinput" data-format="rgb">
                                                <input type="text" name="background"
                                                       autocomplete="off"
                                                       class="form-control  @error('background') is-invalid @enderror"
                                                       value="{{ !empty($badge) ? $badge->background : old('background') }}"
                                                       placeholder="{{ trans('admin/main.trend_color_placeholder') }}"
                                                />

                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-fill-drip"></i>
                                                    </div>
                                                </div>

                                                @error('background')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.start_date') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="dateRangeLabel">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>

                                                <input type="text" name="start_at" class="form-control text-center datetimepicker"
                                                       aria-describedby="dateRangeLabel" autocomplete="off"
                                                       value="{{ (!empty($badge) and !empty($badge->start_at)) ? dateTimeFormat($badge->start_at, 'Y-m-d H:i', false) : old('start_at') }}"/>
                                                @error('start_at')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                                @enderror

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.end_date') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="dateRangeLabel">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>

                                                <input type="text" name="end_at" class="form-control text-center datetimepicker"
                                                       aria-describedby="dateRangeLabel" autocomplete="off"
                                                       value="{{ (!empty($badge) and !empty($badge->end_at)) ? dateTimeFormat($badge->end_at, 'Y-m-d H:i', false) : old('end_at') }}"/>
                                                @error('end_at')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group custom-switches-stacked">
                                            <label class="custom-switch pl-0 d-flex align-items-center">
                                                <input type="hidden" name="enable" value="0">
                                                <input type="checkbox" name="enable" id="enableSwitch" value="on" {{ (!empty($badge) and $badge->enable) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                                                <span class="custom-switch-indicator"></span>
                                                <label class="custom-switch-description mb-0 cursor-pointer" for="enableSwitch">{{ trans('update.enable') }}</label>
                                            </label>
                                        </div>

                                    </div>
                                </div>


                                @if(!empty($badge))
                                    <div class="d-flex align-items-center justify-content-between mt-20 mb-20">
                                        <h5 class="section-title after-line font-16 font-weight-bold my-0">{{ trans('update.contents') }}</h5>

                                        <button type="button" class="js-add-content btn btn-primary btn-sm ml-15" data-path="{{ getAdminPanelUrl("/product-badges/{$badge->id}/contents/get-form") }}">{{ trans('admin/main.add_new') }}</button>
                                    </div>

                                    <div class="table-responsive mt-15">
                                        <table class="table table-striped font-14">
                                            <tr>
                                                <th>{{ trans('admin/main.type') }}</th>
                                                <th class="text-left">{{ trans('update.content') }}</th>
                                                <th width="120">{{ trans('admin/main.actions') }}</th>
                                            </tr>

                                            @foreach($badge->contents as $content)
                                                <tr>
                                                    <td>{{ trans("update.{$content->getContentType()}") }}</td>

                                                    <td class="text-left">
                                                        {{ $content->getContentItem()->title }}
                                                    </td>

                                                    <td>
                                                        @include('admin.includes.delete_button',[
                                                            'url' => getAdminPanelUrl("/product-badges/{$badge->id}/contents/{$content->id}/delete"),
                                                           ])
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="alert alert-warning">{{ trans('update.after_saving_the_badge_you_can_add_contents') }}</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="">
                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
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
    <script src="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>

    <script src="/assets/default/js/admin/product_badges.min.js"></script>
@endpush
