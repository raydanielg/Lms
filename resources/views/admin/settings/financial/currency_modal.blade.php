<div id="multiCurrencyModal" class="{{ empty($editCurrency) ? 'd-none' : ''}}">
    <div class="custom-modal-body">
        <h2 class="section-title after-line">{{ trans('update.multi_currency') }}</h2>

        <div class="currency-form" data-action="{{ getAdminPanelUrl("/settings/financial/currency") }}">
            @if(!empty($editCurrency))
                <input type="hidden" name="item_id" value="{{ $editCurrency->id }}">
            @endif

            <div class="form-group">
                <label class="input-label d-block">{{ trans('admin/main.currency') }}</label>
                <select name="currency" class="js-ajax-currency form-control js-select2" data-placeholder="{{ trans('admin/main.currency') }}">
                    <option value=""></option>
                    @foreach(currenciesLists() as $key => $currencyListItem)
                        <option value="{{ $key }}" @if(!empty($editCurrency) and $editCurrency->currency == $key) selected @endif >{{ $currencyListItem }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label class="input-label d-block">{{ trans('update.currency_position') }}</label>
                <select name="currency_position" class="js-ajax-currency_position form-control">
                    @foreach(\App\Models\Currency::$currencyPositions as $position)
                        <option value="{{ $position }}" @if(!empty($editCurrency) and $editCurrency->currency_position == $position) selected @endif >{{ trans('update.currency_position_'.$position) }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label class="input-label d-block">{{ trans('update.currency_separator') }}</label>
                <select name="currency_separator" class="js-ajax-currency_separator form-control">
                    <option value="dot" @if(!empty($editCurrency) and $editCurrency->currency_separator == 'dot') selected @endif >{{ trans('update.currency_separator_dot') }}</option>
                    <option value="comma" @if(!empty($editCurrency) and $editCurrency->currency_separator == 'comma') selected @endif >{{ trans('update.currency_separator_comma') }}</option>
                </select>

                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label class="input-label d-block">{{ trans('update.currency_decimal') }}</label>
                <input type="number" name="currency_decimal" class="js-ajax-currency_decimal form-control" min="0" max="3" value="{{ (!empty($editCurrency) and !empty($editCurrency->currency_decimal)) ? $editCurrency->currency_decimal : 0 }}">

                <div class="invalid-feedback"></div>
            </div>

            <div class="form-group">
                <label class="input-label d-block">{{ trans('update.exchange_rate') }}</label>
                <input type="number" name="exchange_rate" class="js-ajax-exchange_rate form-control" value="{{ (!empty($editCurrency) and !empty($editCurrency->exchange_rate)) ? $editCurrency->exchange_rate : 0 }}">
                <div class="invalid-feedback"></div>

                <p class="mt-1 text-muted font-12">{{ trans('update.insert_the_selected_currency_exchange_rate_to_the_default_currency',['sign' => $currency]) }}</p>
            </div>

            <div class="d-flex align-items-center justify-content-end mt-3">
                <button type="button" class="save-currency btn btn-sm btn-primary">{{ trans('public.save') }}</button>
                <button type="button" class="close-swl btn btn-sm btn-danger ml-2">{{ trans('public.close') }}</button>
            </div>

        </div>
    </div>
</div>
