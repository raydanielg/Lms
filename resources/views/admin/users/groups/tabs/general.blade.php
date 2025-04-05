<div class="tab-pane mt-3 fade active show" id="general" role="tabpanel" aria-labelledby="general-tab">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <form action="{{ getAdminPanelUrl() }}/users/groups/{{ !empty($group) ? $group->id.'/update' : 'store' }}" method="Post">
                {{ csrf_field() }}

                <div class="form-group">
                    <label>{{ trans('admin/main.name') }}</label>
                    <input type="text" name="name"
                           class="form-control  @error('name') is-invalid @enderror"
                           value="{{ !empty($group) ? $group->name : old('name') }}"/>
                    @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group ">
                    <label>{{ trans('admin/main.user_group_discount_rate') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                        <input type="number"
                               name="discount"
                               class="form-control spinner-input text-center @error('discount') is-invalid @enderror"
                               value="{{ !empty($group) ? $group->discount : old('discount') }}"
                               placeholder="{{ trans('admin/main.user_group_discount_rate_placeholder') }}" maxlength="3" min="0" max="100">
                        @error('discount')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.user_group_discount_rate_hint') }}</div>
                </div>


                <div class="form-group">
                    <label class="input-label d-block">{{ trans('admin/main.users') }}</label>
                    <select name="users[]" multiple="multiple" class="form-control search-user-select2"
                            data-search-option="for_user_group"
                            data-placeholder="{{ trans('public.search_user') }}">

                        @if(!empty($userGroups) and $userGroups->count() > 0)
                            @foreach($userGroups as $userGroup)
                                <option value="{{ $userGroup->user_id }}" selected>{{ $userGroup->user->full_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group custom-switches-stacked">
                    <label class="custom-switch pl-0">
                        <input type="hidden" name="status" value="inactive">
                        <input type="checkbox" name="status" id="preloadingSwitch" value="active" {{ (!empty($group) and $group->status == 'active') ? 'checked="checked"' : '' }} class="custom-switch-input"/>
                        <span class="custom-switch-indicator"></span>
                        <label class="custom-switch-description mb-0 cursor-pointer" for="preloadingSwitch">{{ trans('admin/main.active') }}</label>
                    </label>
                </div>


                @php
                    $commissions = !empty($group) ? $group->commissions : null;
                @endphp

                <div class="mb-2">
                    <h5 class="font-16 text-dark">{{ trans('admin/main.user_group_commission_rate') }}</h5>
                    <div class="text-muted text-small mt-1">{{ trans('admin/main.user_group_commission_rate_hint') }}</div>
                </div>

                @foreach(\App\Models\UserCommission::$sources as $commissionSource)
                    @php
                        $commission = !empty($commissions) ? $commissions->where('source', $commissionSource)->first() : null;
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

                <div class=" mt-4">
                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
