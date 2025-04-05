<div class="tab-pane mt-3 fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/users/{{ $user->id .'/financialUpdate' }}" method="Post">
                {{ csrf_field() }}

                <div class="form-group">
                    <label>{{ trans('financial.account_type') }}</label>

                    <select name="bank_id" class="js-user-bank-input form-control @error('bank_id')  is-invalid @enderror">
                        <option selected disabled>{{ trans('financial.select_account_type') }}</option>

                        @foreach($userBanks as $userBank)
                            <option value="{{ $userBank->id }}" @if(!empty($user) and !empty($user->selectedBank) and $user->selectedBank->user_bank_id == $userBank->id) selected="selected" @endif data-specifications="{{ json_encode($userBank->specifications->pluck('name','id')->toArray()) }}">{{ $userBank->title }}</option>
                        @endforeach
                    </select>

                    @error('bank_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="js-bank-specifications-card">
                    @if(!empty($user) and !empty($user->selectedBank) and !empty($user->selectedBank->bank))
                        @foreach($user->selectedBank->bank->specifications as $specification)
                            @php
                                $selectedBankSpecification = $user->selectedBank->specifications->where('user_selected_bank_id', $user->selectedBank->id)->where('user_bank_specification_id', $specification->id)->first();
                            @endphp
                            <div class="form-group">
                                <label class="font-weight-500 text-dark-blue">{{ $specification->name }}</label>
                                <input type="text" name="bank_specifications[{{ $specification->id }}]" value="{{ (!empty($selectedBankSpecification)) ? $selectedBankSpecification->value : '' }}" class="form-control"/>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="form-group mt-15">
                    <label class="input-label">{{ trans('financial.identity_scan') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text admin-file-manager" data-input="identity_scan" data-preview="holder">
                                <i class="fa fa-chevron-up"></i>
                            </button>
                        </div>
                        <input type="text" name="identity_scan" id="identity_scan" value="{{ !empty($user->identity_scan) ? $user->identity_scan : old('identity_scan') }}" class="form-control"/>
                        <div class="input-group-append">
                            <button type="button" class="input-group-text admin-file-view" data-input="identity_scan">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ trans('financial.address') }}</label>
                    <input type="text" name="address"
                           class="form-control "
                           value="{{ !empty($user) ? $user->address : old('address') }}"
                           placeholder="{{ trans('financial.address') }}"/>
                </div>

                <div class="form-group mb-0 d-flex align-items-center">
                    <div class="custom-control custom-switch d-block">
                        <input type="checkbox" name="financial_approval" class="custom-control-input" id="verifySwitch" {{ (($user->financial_approval) or (old('financial_approval') == 'on')) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="verifySwitch"></label>
                    </div>
                    <label for="verifySwitch">{{ trans('admin/main.financial_approval') }}</label>
                </div>

                <div class="form-group mb-0 d-flex align-items-center">
                    <div class="custom-control custom-switch d-block">
                        <input type="checkbox" name="enable_installments" class="custom-control-input" id="enableInstallmentsSwitch" {{ (($user->enable_installments) or (old('enable_installments') == 'on')) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="enableInstallmentsSwitch"></label>
                    </div>
                    <label for="enableInstallmentsSwitch">{{ trans('update.enable_installments') }}</label>
                </div>

                <div class="form-group mb-0 d-flex align-items-center">
                    <div class="custom-control custom-switch d-block">
                        <input type="checkbox" name="installment_approval" class="custom-control-input" id="installmentApprovalSwitch" {{ (($user->installment_approval) or (old('installment_approval') == 'on')) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="installmentApprovalSwitch"></label>
                    </div>
                    <label for="installmentApprovalSwitch">{{ trans('update.installment_approval') }}</label>
                </div>

                <div class="form-group mb-0 d-flex align-items-center">
                    <div class="custom-control custom-switch d-block">
                        <input type="checkbox" name="disable_cashback" class="custom-control-input" id="disableCashbackSwitch" {{ (($user->disable_cashback) or (old('disable_cashback') == 'on')) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="disableCashbackSwitch"></label>
                    </div>
                    <label for="disableCashbackSwitch">{{ trans('update.disable_cashback') }}</label>
                </div>

                <div class="form-group mb-0 d-flex align-items-center">
                    <div class="custom-control custom-switch d-block">
                        <input type="checkbox" name="enable_registration_bonus" class="custom-control-input" id="enable_registration_bonusSwitch" {{ ($user->enable_registration_bonus) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="enable_registration_bonusSwitch"></label>
                    </div>
                    <label for="enable_registration_bonusSwitch">{{ trans('update.enable_registration_bonus') }}</label>
                </div>

                <div class="js-registration-bonus-field form-group {{ $user->enable_registration_bonus ? '' : 'd-none' }}">
                    <label>{{ trans('update.registration_bonus_amount') }}</label>
                    <input type="text" name="registration_bonus_amount"
                           class="form-control "
                           value="{{ !empty($user) ? $user->registration_bonus_amount : old('registration_bonus_amount') }}"
                           placeholder="{{ trans('update.user_registration_bonus_amount_placeholder') }}"/>
                </div>


                @if(!$user->isUser())
                    @php
                        $commissions = $user->commissions;
                    @endphp

                    <h5 class="mb-2 font-16 text-dark">{{ trans('update.commissions') }}</h5>

                    @foreach(\App\Models\UserCommission::$sources as $commissionSource)
                        @php
                            $commission = $commissions->where('source', $commissionSource)->first();
                            $commissionValue = null;

                            if (!empty($commission)) {
                                $commissionValue = $commission->value;

                                if ($commission->type == "fixed_amount") {
                                    $commissionValue = convertPriceToUserCurrency($commissionValue);
                                }
                            }
                        @endphp

                        <div class="form-group">
                            <label class="mb-0">{{ trans("update.{$commissionSource}_commission") }}</label>

                            <div class="row">
                                <div class="col-6">
                                    <label class="">{{ trans("admin/main.type") }}</label>
                                    <select name="commissions[{{ $commissionSource }}][type]" class="js-commission-type-input form-control" data-currency="{{ $currency }}">
                                        <option value="percent" {{ (!empty($commission) and $commission->type == "percent") ? 'selected' : '' }}>{{ trans('update.percent') }}</option>
                                        <option value="fixed_amount" {{ (!empty($commission) and $commission->type == "fixed_amount") ? 'selected' : '' }}>{{ trans('update.fixed_amount') }}</option>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <div class="">
                                        <label class="">
                                            {{ trans("update.value") }}

                                            <span class="ml-1 js-commission-value-span">({{ !empty($commission) ? (($commission->type == "percent") ? '%' : $currency) : '%' }})</span>
                                        </label>

                                        <input type="number" name="commissions[{{ $commissionSource }}][value]" value="{{ (!empty($commissionValue)) ? $commissionValue : '' }}" class="js-commission-value-input form-control text-center" {{ (!empty($commission) and $commission->type == "percent") ? 'maxlength="3" min="0" max="100"' : '' }}/>
                                    </div>
                                </div>
                            </div>

                            <div class="text-muted text-small mt-1">{{ trans("update.{$commissionSource}_commission_hint") }}</div>
                        </div>
                    @endforeach
                @endif

                <div class=" mt-4">
                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
