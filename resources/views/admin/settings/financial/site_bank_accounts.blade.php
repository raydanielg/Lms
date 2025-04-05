@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp



<div class="tab-pane mt-3 fade" id="offline_banks_credits" role="tabpanel" aria-labelledby="offline_banks_credits-tab">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{ getAdminPanelUrl() }}/settings/site_bank_accounts" method="post">
                        {{ csrf_field() }}

                        <input type="hidden" name="page" value="financial">

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div id="addAccountTypes">

                                    <button type="button" class="btn btn-success add-btn mb-4 fa fa-plus"></button>

                                    <div class="form-group d-flex align-items-start main-row">
                                        <div class="px-2 py-1 border flex-grow-1">

                                            <div class="form-group mt-3">
                                                <label>{{ trans('admin/main.title') }}</label>
                                                <input type="text" name="value[record][title]"
                                                       class="form-control"
                                                />
                                            </div>

                                            <div class="form-group">
                                                <label>{{ trans('admin/main.logo') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <button type="button" class="input-group-text admin-file-manager" data-input="image_record" data-preview="holder">
                                                            <i class="fa fa-upload"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" name="value[record][image]" id="image_record" value="" class="form-control"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ trans('financial.card_id') }}</label>
                                                <input type="text" name="value[record][card_id]"
                                                       class="form-control"
                                                />
                                            </div>

                                            <div class="form-group">
                                                <label>{{ trans('financial.account_id') }}</label>
                                                <input type="text" name="value[record][account_id]"
                                                       class="form-control"
                                                />
                                            </div>

                                            <div class="form-group">
                                                <label>{{ trans('financial.iban') }}</label>
                                                <input type="text" name="value[record][iban]"
                                                       class="form-control"
                                                />
                                            </div>
                                        </div>
                                        <button type="button" class="fas fa-times btn ml-2 remove-btn btn-danger d-none"></button>
                                    </div>

                                    @if(!empty($itemValue))

                                        @if(!empty($itemValue) and is_array($itemValue))
                                            @foreach($itemValue as $key => $item)
                                                <div class="form-group d-flex align-items-start">
                                                    <div class="px-2 py-1 border flex-grow-1">

                                                        <div class="form-group mt-3">
                                                            <label>{{ trans('admin/main.title') }}</label>
                                                            <input type="text" name="value[{{ $key }}][title]"
                                                                   class="form-control"
                                                                   value="{{ $item['title'] ?? '' }}"
                                                            />
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ trans('admin/main.logo') }}</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <button type="button" class="input-group-text admin-file-manager" data-input="image_record" data-preview="holder">
                                                                        <i class="fa fa-upload"></i>
                                                                    </button>
                                                                </div>
                                                                <input type="text" name="value[{{ $key }}][image]" id="image_{{ $key }}" value="{{ $item['image'] ?? '' }}" class="form-control"/>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ trans('financial.card_id') }}</label>
                                                            <input type="text" name="value[{{ $key }}][card_id]"
                                                                   class="form-control"
                                                                   value="{{ $item['card_id'] ?? '' }}"
                                                            />
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ trans('financial.account_id') }}</label>
                                                            <input type="text" name="value[{{ $key }}][account_id]"
                                                                   class="form-control"
                                                                   value="{{ $item['account_id'] ?? '' }}"
                                                            />
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ trans('financial.iban') }}</label>
                                                            <input type="text" name="value[{{ $key }}][iban]"
                                                                   class="form-control"
                                                                   value="{{ $item['iban'] ?? '' }}"
                                                            />
                                                        </div>
                                                    </div>
                                                    <button type="button" class="fas fa-times btn ml-2 remove-btn btn-danger"></button>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">{{ trans('admin/main.save_change') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts_bottom')
    <script src="/assets/default/js/admin/settings/site_bank_accounts.min.js"></script>
@endpush
