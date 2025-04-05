@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp

<div class="tab-pane mt-3 fade " id="referral" role="tabpanel" aria-labelledby="referral-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/main" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="financial">
                <input type="hidden" name="name" value="referral">


                <div class="form-group mt-1 custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="value[content_translate]" value="0">
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="contentTranslate">{{ trans('admin/main.active') }}</label>
                    </label>
                    <div class="text-muted text-small mt-1">Paid Plugin</div>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0 d-flex align-items-center">
                        <input type="hidden" name="value[users_affiliate_status]" value="0">
                        <input type="checkbox" name="value[users_affiliate_status]" id="userReferralStatusSwitch" value="1" {{ (!empty($itemValue) and !empty($itemValue['users_affiliate_status']) and $itemValue['users_affiliate_status']) ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="userReferralStatusSwitch">{{ trans('admin/main.active_users_affiliate_when_registration') }}                       </label>
                    </label>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.active_referral_new_users_hint') }}</div>
                </div>


                <div class="form-group">
                    <label>{{ trans('admin/main.affiliate_user_commission') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <input type="number" name="value[affiliate_user_commission]" value="{{ (!empty($itemValue) and !empty($itemValue['affiliate_user_commission'])) ? $itemValue['affiliate_user_commission'] : old('affiliate_user_commission') }}" class="form-control text-center @error('affiliate_user_commission') is-invalid @enderror" maxlength="3" min="0" max="100"/>

                        @error('affiliate_user_commission')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="text-muted text-small mt-1">{{ trans('admin/main.affiliate_user_commission_hint') }}</div>
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.store_affiliate_user_commission') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <input type="number" name="value[store_affiliate_user_commission]" value="{{ (!empty($itemValue) and !empty($itemValue['store_affiliate_user_commission'])) ? $itemValue['store_affiliate_user_commission'] : old('store_affiliate_user_commission') }}" class="form-control text-center @error('store_affiliate_user_commission') is-invalid @enderror" maxlength="3" min="0" max="100"/>

                        @error('store_affiliate_user_commission')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="text-muted text-small mt-1">{{ trans('admin/main.store_affiliate_user_commission_hint') }}</div>
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.affiliate_user_amount') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <input type="number" name="value[affiliate_user_amount]" value="{{ (!empty($itemValue) and !empty($itemValue['affiliate_user_amount'])) ? $itemValue['affiliate_user_amount'] : old('affiliate_user_amount') }}" class="form-control text-center" maxlength="8" min="0" />
                    </div>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.affiliate_user_amount_hint') }}</div>
                </div>


                <div class="form-group">
                    <label>{{ trans('admin/main.referred_user_amount') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <input type="number" name="value[referred_user_amount]" value="{{ (!empty($itemValue) and !empty($itemValue['referred_user_amount'])) ? $itemValue['referred_user_amount'] : old('referred_user_amount') }}" class="form-control text-center" maxlength="8" min="0" />
                    </div>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.referred_user_amount_hint') }}</div>
                </div>

                <div class="form-group">
                    <label>{{ trans('admin/main.referral_description') }}</label>
                    <textarea name="value[referral_description]" class="form-control" rows="6" placeholder="">{{ (!empty($itemValue) and !empty($itemValue['referral_description'])) ? $itemValue['referral_description'] : old('referral_description') }}</textarea>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.referral_description_hint') }}</div>
                </div>

                <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>
