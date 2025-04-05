<div class="row mt-15">
    <div class="col-12 col-md-4">
        <div class="form-group ">
            <label class="input-label">{{ trans('admin/main.amount') }}</label>
            <input type="number" name="amount" value="{{ !empty($rule) ? $rule->amount : old('amount') }}" class="form-control @error('amount')  is-invalid @enderror"/>
            @error('amount')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="form-group ">
            <label class="input-label">{{ trans('update.amount_type') }}</label>
            <select name="amount_type" class="js-amount-type-select form-control">
                <option value="fixed_amount" {{ (!empty($rule) and $rule->amount_type == 'fixed_amount') ? 'selected' : '' }}>{{ trans('update.fixed_amount') }}</option>
                <option value="percent" {{ (!empty($rule) and $rule->amount_type == 'percent') ? 'selected' : '' }}>{{ trans('update.percent') }}</option>
            </select>
            @error('amount_type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-6">

        <div class="js-apply-cashback-per-item form-group {{ (empty($rule) or $rule->amount_type == 'fixed_amount') ? '' : 'd-none' }}">
            <div class="d-flex align-items-center">
                <label class="" for="perItemSwitch">{{ trans('update.apply_cashback_per_item') }}</label>
                <div class="custom-control custom-switch ml-3">
                    <input type="checkbox" name="apply_cashback_per_item" class="custom-control-input" id="perItemSwitch" {{ (!empty($rule) && $rule->apply_cashback_per_item) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="perItemSwitch"></label>
                </div>
            </div>
            <p class="font-12 text-muted mt-1">{{ trans('update.apply_cashback_per_item_hint') }}</p>
        </div>


        <div class="js-max-amount-field form-group {{ (!empty($rule) and $rule->amount_type == 'percent') ? '' : 'd-none' }}">
            <label class="input-label">{{ trans('admin/main.max_amount') }}</label>
            <input type="number" name="max_amount" value="{{ !empty($rule) ? $rule->max_amount : old('max_amount') }}" class="form-control @error('max_amount')  is-invalid @enderror"/>
            <div class="text-muted text-small mt-1">{{ trans('update.cashback_max_amount_hint') }}</div>
            @error('max_amount')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <label class="input-label">{{ trans('admin/main.min_amount') }}</label>
            <input type="number" name="min_amount" value="{{ !empty($rule) ? $rule->min_amount : old('min_amount') }}" class="form-control @error('min_amount')  is-invalid @enderror"/>
            <div class="text-muted text-small mt-1">{{ trans('update.cashback_min_amount_hint') }}</div>
            @error('min_amount')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

    </div>
</div>

