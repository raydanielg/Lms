@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }

@endphp

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
    <link rel="stylesheet" href="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.css">
@endpush

<div class="mt-3" id="others_personalization">

    <form action="{{ getAdminPanelUrl("/settings/personalization/statistics") }}" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="page" value="personalization">
        <input type="hidden" name="statistics" value="statistics">

        <div class="form-group custom-switches-stacked">
            <label class="custom-switch pl-0">
                <input type="hidden" name="value[enable_statistics]" value="0">
                <input type="checkbox" name="value[enable_statistics]" id="enableStatisticsSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['enable_statistics']) and $itemValue['enable_statistics']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                <span class="custom-switch-indicator"></span>
                <label class="custom-switch-description mb-0 cursor-pointer" for="enableStatisticsSwitch">{{ trans('update.enable_statistics') }}</label>
            </label>
        </div>

        <div class="form-group custom-switches-stacked">
            <label class="custom-switch pl-0">
                <input type="hidden" name="value[display_default_statistics]" value="0">
                <input type="checkbox" name="value[display_default_statistics]" id="display_default_statisticsSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['display_default_statistics']) and $itemValue['display_default_statistics']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                <span class="custom-switch-indicator"></span>
                <label class="custom-switch-description mb-0 cursor-pointer" for="display_default_statisticsSwitch">{{ trans('update.display_default_statistics') }}</label>
            </label>
        </div>

        <div class="js-custom-statistics mb-4 {{ (!empty($itemValue) and !empty($itemValue['display_default_statistics'])) ? 'd-none' : '' }}">
            <button type="button" class="js-add-statistics btn btn-primary" data-path="{{ getAdminPanelUrl("/settings/personalization/statistics/get-form") }}">{{ trans('update.add_statistics') }}</button>

            <div class="row mt-3 bg-whitesmoke py-2 rounded-sm">
                <div class="col-1">{{ trans('admin/main.icon') }}</div>
                <div class="col-3">{{ trans('admin/main.title') }}</div>
                <div class="col-3">{{ trans('admin/main.description') }}</div>
                <div class="col-1">{{ trans('admin/main.count') }}</div>
                <div class="col-2">{{ trans('admin/main.background') }}</div>
                <div class="col-2 text-right">{{ trans('admin/main.actions') }}</div>
            </div>

            <ul class="draggable-lists list-group">
                @foreach($statistics as $statistic)
                    <li data-id="{{ $statistic->id }}" class="row align-items-center mt-2">
                        <div class="col-1">
                            <img src="{{ $statistic->icon }}" alt="" class="img-fluid" width="40" height="40">
                        </div>
                        <div class="col-3">{{ $statistic->title }}</div>
                        <div class="col-3">{{ truncate($statistic->description, 200) }}</div>
                        <div class="col-1">{{ $statistic->count }}</div>
                        <div class="col-2">{{ $statistic->color }} <span class="ml-1" style="width: 10px; height: 10px; display: inline-block; background-color: {{ $statistic->color }}"></span></div>
                        <div class="col-2 text-right">
                            <div class="d-flex align-items-center justify-content-end">

                                <button type="button" class="js-edit-statistic btn-transparent text-primary mr-2" data-path="{{ getAdminPanelUrl("/settings/personalization/statistics/{$statistic->id}/editItem") }}"><i class="fa fa-edit"></i></button>

                                @include('admin.includes.delete_button',['url' => getAdminPanelUrl("/settings/personalization/statistics/{$statistic->id}/deleteItem"),'btnClass' => 'text-danger mr-2 font-16'])

                                <div class="cursor-pointer move-icon font-16 mr-1">
                                    <i class="fa fa-arrows-alt"></i>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
    </form>

</div>


@push('scripts_bottom')
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
    <script src="/assets/admin/vendor/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>

    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
    </script>

    <script src="/assets/default/js/admin/settings/statistics.min.js"></script>
@endpush
