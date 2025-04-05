@push('styles_top')
    <link rel="stylesheet" href="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.css">
@endpush

<div class=" mt-3">
    <div class="row">
        <div class="col-12 col-md-6">
            <h5 class="d-block mt-1 mb-3 text-dark">{{ trans("update.front_template") }}</h5>
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="name" value="theme_colors">
                <input type="hidden" name="page" value="personalization">

                @foreach(\App\Models\Setting::$rootColors as $color)
                    @if(strpos($color,'shadow'))
                        <div class="form-group">
                            <label>{{ trans('update.theme_color_'.$color) }}</label>
                            <input type="text" name="value[{{ $color }}]" class="form-control" value="{{ (!empty($itemValue) and !empty($itemValue[$color])) ? $itemValue[$color] : '' }}">
                            <p class="font-12 mb-0">{{ trans("update.theme_color_{$color}_hint") }}</p>
                        </div>
                    @else
                        <div class="form-group">
                            <label>{{ trans('update.theme_color_'.$color) }}</label>
                            <div class="input-group colorpickerinput">
                                <input type="text" name="value[{{ $color }}]" class="form-control" value="{{ (!empty($itemValue) and !empty($itemValue[$color])) ? $itemValue[$color] : '' }}">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <i class="fas fa-fill-drip"></i>
                                    </div>
                                </div>
                            </div>

                            <p class="font-12 mb-0">{{ trans("update.theme_color_{$color}_hint") }}</p>
                        </div>
                    @endif
                @endforeach

                <div class="form-group">
                    <label>{{ trans('update.front_body_background') }}</label>
                    <div class="input-group colorpickerinput">
                        <input type="text" name="value[front_body_background]" class="form-control" value="{{ (!empty($itemValue) and !empty($itemValue['front_body_background'])) ? $itemValue['front_body_background'] : '' }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <i class="fas fa-fill-drip"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="d-block mt-4 text-dark">{{ trans("update.admin_template") }}</h5>

                @foreach(\App\Models\Setting::$rootAdminColors as $color)
                    <div class="form-group">
                        <label>{{ trans('update.theme_color_'.$color) }}</label>
                        <div class="input-group colorpickerinput">
                            <input type="text" name="value[admin_{{ $color }}]" class="form-control" value="{{ (!empty($itemValue) and !empty($itemValue['admin_'.$color])) ? $itemValue['admin_'.$color] : '' }}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-fill-drip"></i>
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
