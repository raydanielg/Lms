@push('styles_top')
    <link rel="stylesheet" href="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.css">
@endpush

<div class=" mt-3">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="name" value="theme_fonts">
                <input type="hidden" name="page" value="personalization">


                @foreach(['main','rtl'] as $fontType)

                    <div class="mt-2">
                        <strong class="font-16 mb-2 text-dark d-block">{{ trans('update.'.$fontType.'_font') }}</strong>

                        <div class="pl-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('update.regular') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="input-group-text admin-file-manager" data-input="{{ $fontType }}FontRegular" data-preview="holder">
                                            <i class="fa fa-chevron-up"></i>
                                        </button>
                                    </div>
                                    <input type="text" name="value[{{ $fontType }}][regular]" id="{{ $fontType }}FontRegular" value="{{ (!empty($itemValue) and !empty($itemValue[$fontType]) and !empty($itemValue[$fontType]['regular'])) ? $itemValue[$fontType]['regular'] : '' }}" class="form-control"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="input-label">{{ trans('update.bold') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="input-group-text admin-file-manager" data-input="{{ $fontType }}FontBold" data-preview="holder">
                                            <i class="fa fa-chevron-up"></i>
                                        </button>
                                    </div>
                                    <input type="text" name="value[{{ $fontType }}][bold]" id="{{ $fontType }}FontBold" value="{{ (!empty($itemValue) and !empty($itemValue[$fontType]) and !empty($itemValue[$fontType]['bold'])) ? $itemValue[$fontType]['bold'] : '' }}" class="form-control"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="input-label">{{ trans('update.medium') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="input-group-text admin-file-manager" data-input="{{ $fontType }}FontMedium" data-preview="holder">
                                            <i class="fa fa-chevron-up"></i>
                                        </button>
                                    </div>
                                    <input type="text" name="value[{{ $fontType }}][medium]" id="{{ $fontType }}FontMedium" value="{{ (!empty($itemValue) and !empty($itemValue[$fontType]) and !empty($itemValue[$fontType]['medium'])) ? $itemValue[$fontType]['medium'] : '' }}" class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>

@push('scripts_bottom')
    <script src="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
@endpush
