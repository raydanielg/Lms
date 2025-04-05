@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp


<div class="tab-pane mt-3 fade  @if(request()->get('tab') == "user_banks") active show @endif" id="user_banks" role="tabpanel" aria-labelledby="user_banks-tab">
    <div class="row">
        <div class="col-12 col-md-6">

        </div>
    </div>

    <section class="mt-3">
        <div class="d-flex justify-content-between align-items-center pb-2">
            <h2 class="section-title after-line">{{ trans('update.user_banks_credits') }}</h2>

            <button type="button" data-path="{{ getAdminPanelUrl("/settings/financial/user_banks/get-form") }}" class="js-add-user-banks btn btn-primary btn-sm ml-2">{{ trans('update.add_bank') }}</button>
        </div>

        @if(!empty($userBanks))
            <div class="table-responsive">
                <table class="table table-striped font-14">
                    <tr>
                        <th class="text-left">{{ trans('admin/main.logo') }}</th>
                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                        <th class="text-center">{{ trans('update.specifications') }}</th>
                        <th class="text-right">{{ trans('admin/main.actions') }}</th>
                    </tr>

                    @foreach($userBanks as $userBank)
                        <tr>
                            <td class="text-left">
                                <img src="{{ $userBank->logo }}" alt="" width="48">
                            </td>

                            <td class="text-left">{{ $userBank->title }}</td>

                            <td class="text-center">{{ $userBank->specifications->count() }}</td>

                            <td class="text-right">
                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="button" data-path="{{ getAdminPanelUrl("/settings/financial/user_banks/{$userBank->id}/edit") }}" class="js-edit-user-banks font-14 btn-transparent text-primary">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    @include('admin.includes.delete_button',[
                                      'url' => getAdminPanelUrl("/settings/financial/user_banks/{$userBank->id}/delete"),
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
    <script src="/assets/default/js/admin/settings/user_banks.min.js"></script>
@endpush
