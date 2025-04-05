@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp


<div class="tab-pane mt-3 fade  @if(request()->get('tab') == "currency") active show @endif" id="currency" role="tabpanel" aria-labelledby="currency-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="financial">
                <input type="hidden" name="name" value="{{ \App\Models\Setting::$currencySettingsName }}">


                <div class="form-group">
                    <label class="input-label d-block">{{ trans('update.default_currency') }}</label>
                    <select name="value[currency]" class="form-control select2" data-placeholder="{{ trans('admin/main.currency') }}">
                        <option value=""></option>
                        @foreach(currenciesLists() as $key => $currencyListItem)
                            <option value="{{ $key }}" @if((!empty($itemValue) and !empty($itemValue['currency'])) and $itemValue['currency'] == $key) selected @endif >{{ $currencyListItem }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="form-group">
                    <label class="input-label d-block">{{ trans('update.currency_position') }}</label>
                    <select name="value[currency_position]" class="form-control">
                        @foreach(\App\Models\Currency::$currencyPositions as $position)
                            <option value="{{ $position }}" @if((!empty($itemValue) and !empty($itemValue['currency_position'])) and $itemValue['currency_position'] == $position) selected @endif >{{ trans('update.currency_position_'.$position) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('update.currency_separator') }}</label>
                    <select name="value[currency_separator]" class="form-control">
                        <option value="dot" @if((!empty($itemValue) and !empty($itemValue['currency_separator'])) and $itemValue['currency_separator'] == 'dot') selected @endif >{{ trans('update.currency_separator_dot') }}</option>
                        <option value="comma" @if((!empty($itemValue) and !empty($itemValue['currency_separator'])) and $itemValue['currency_separator'] == 'comma') selected @endif >{{ trans('update.currency_separator_comma') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="input-label d-block">{{ trans('update.currency_decimal') }}</label>
                    <input type="number" name="value[currency_decimal]" class="form-control" min="0" max="3" value="{{ (!empty($itemValue) and !empty($itemValue['currency_decimal'])) ? $itemValue['currency_decimal'] : 0 }}">
                </div>


                <div class="form-group mt-3 custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[content_translate]" value="0">
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="contentTranslate">{{ trans('update.enable_multi_currency') }}</label>
                    </label>
                    <div class="text-muted text-small mt-1">Paid Plugin</div>
                </div>

                <section class="js-multi-currency-section mt-3 {{ (!empty($itemValue) and !empty($itemValue['multi_currency'])) ? : "d-none" }}">
                    <div class="d-flex justify-content-between align-items-center pb-2">
                        <h2 class="section-title after-line">{{ trans('update.currencies') }}</h2>

                        <button id="add_multi_currency" type="button" class="btn btn-primary btn-sm ml-2">{{ trans('update.add_currency') }}</button>
                    </div>

                    <ul class="draggable-currency-lists mb-5" data-order-table="currencies">

                        @foreach($currencies as $currencyItem)
                            <li data-id="{{ $currencyItem->id }}" class="quiz-question-card d-flex align-items-center mt-4">
                                <div class="flex-grow-1">
                                    <h4 class="font-16 text-black font-weight-500 text-dark mb-0">{{ currenciesLists($currencyItem->currency) }}</h4>
                                    <div class="font-12 mt-1 question-infos">{{ trans('update.exchange_rate') }}: {{ $currencyItem->exchange_rate }}</div>
                                </div>

                                <div class="move-icon mr-10 cursor-pointer">
                                    <i class="fa fa-arrows-alt" height="25"></i>
                                </div>

                                <div class="btn-group dropdown table-actions">
                                    <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>


                                    <div class="dropdown-menu text-left">
                                        <button type="button" data-path="{{ getAdminPanelUrl("/settings/financial/currency/{$currencyItem->id}/edit") }}" class="js-edit-currency font-14 btn-transparent d-block">{{ trans('public.edit') }}</button>

                                        @include('admin.includes.delete_button',[
                                          'url' => getAdminPanelUrl("/settings/financial/currency/{$currencyItem->id}/delete"),
                                          'btnClass' => 'btn-sm btn-transparent d-block text-danger mt-1' ,
                                          'btnText' => trans('public.delete')
                                          ])
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>


@include('admin.settings.financial.currency_modal')


@push('scripts_bottom')
    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
    </script>
    <script src="/assets/default/js/admin/settings/currencies.min.js"></script>
@endpush
