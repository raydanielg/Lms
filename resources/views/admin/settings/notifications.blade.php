@extends('admin.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.notifications') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}/settings">{{ trans('admin/main.settings') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.notifications') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                @foreach(\App\Models\NotificationTemplate::$notificationTemplateAssignSetting as $section => $v)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $loop->iteration == 1 ? ' active' : '' }}" id="{{ $section }}-tab" data-toggle="tab" href="#{{ $section }}" role="tab" aria-controls="{{ $section }}" aria-selected="true">{{ trans('admin/main.notification_'.$section) }}</a>
                                    </li>
                                @endforeach
                            </ul>

                            @php
                                $itemValue = (!empty($settings) and !empty($settings['notifications'])) ? $settings['notifications']->value : '';

                                if (!empty($itemValue) and !is_array($itemValue)) {
                                    $itemValue = json_decode($itemValue, true);
                                }
                            @endphp

                            <div class="tab-content" id="myTabContent2">

                                @foreach(\App\Models\NotificationTemplate::$notificationTemplateAssignSetting as $tab => $items)
                                    <div class="tab-pane mt-3 fade {{ $loop->iteration == 1 ? ' show active' : '' }}" id="{{ $tab }}" role="tabpanel" aria-labelledby="{{ $tab }}-tab">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <form action="{{ getAdminPanelUrl() }}/settings/notifications/store" method="post">
                                                    {{ csrf_field() }}

                                                    @foreach($items as $item)
                                                        <div class="form-group">
                                                            <label class="input-label">{{ trans('admin/main.notification_'.$item) }}</label>
                                                            <select name="value[{{ $item }}]" class="form-control">
                                                                <option value="" selected disabled></option>

                                                                @foreach($notificationTemplates as $template)
                                                                    <option value="{{ $template->id }}" @if(!empty($itemValue) and  !empty($itemValue[$item]) and $itemValue[$item] == $template->id) selected @endif>{{ $template->title }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endforeach

                                                    <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
