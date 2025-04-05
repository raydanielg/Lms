@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp


<div class="tab-pane mt-3 fade  @if(request()->get('tab') == "offline_banks") active show @endif" id="offline_banks" role="tabpanel" aria-labelledby="offline_banks-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="financial">
                <input type="hidden" name="name" value="{{ \App\Models\Setting::$offlineBanksName }}">


                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0 d-flex align-items-center">
                        <input type="hidden" name="value[offline_banks_status]" value="0">
                        <input type="checkbox" name="value[offline_banks_status]" id="offline_banks_statusSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['offline_banks_status']) and $itemValue['offline_banks_status']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="offline_banks_statusSwitch">{{ trans('update.offline_banks_status') }}</label>
                    </label>
                </div>

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>

    <section class="mt-3">
        <div class="d-flex justify-content-between align-items-center pb-2">
            <h2 class="section-title after-line">{{ trans('update.offline_banks_credits') }}</h2>

            <button type="button" data-path="{{ getAdminPanelUrl("/settings/financial/offline_banks/get-form") }}" class="js-add-offline-banks btn btn-primary btn-sm ml-2">{{ trans('update.add_bank') }}</button>
        </div>

        @if(!empty($offlineBanks))
            <div class="table-responsive">
                <table class="table table-striped font-14">
                    <tr>
                        <th class="text-left">{{ trans('admin/main.logo') }}</th>
                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                        <th class="text-center">{{ trans('update.specifications') }}</th>
                        <th class="text-right">{{ trans('admin/main.actions') }}</th>
                    </tr>

                    @foreach($offlineBanks as $offlineBank)
                        <tr>
                            <td class="text-left">
                                <img src="{{ $offlineBank->logo }}" alt="" width="48">
                            </td>

                            <td class="text-left">{{ $offlineBank->title }}</td>

                            <td class="text-center">{{ $offlineBank->specifications->count() }}</td>

                            <td class="text-right">
                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="button" data-path="{{ getAdminPanelUrl("/settings/financial/offline_banks/{$offlineBank->id}/edit") }}" class="js-edit-offline-banks font-14 btn-transparent text-primary">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    @include('admin.includes.delete_button',[
                                      'url' => getAdminPanelUrl("/settings/financial/offline_banks/{$offlineBank->id}/delete"),
                                      'btnClass' => 'btn-sm btn-transparent d-block text-danger ml-2' ,
                                      'btnText' => ''
                                      ])
                                </div>
                            </td>
                        </tr>
                    @endforeach

                </table>
            </div>
        @endif
    </section>

</div>


@push('scripts_bottom')
    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var specificationLang = '{{ trans('update.specification') }}';
        var valueLang = '{{ trans('update.value') }}';
    </script>
    <script src="/assets/default/js/admin/settings/offline_banks_credits.min.js"></script>
@endpush
