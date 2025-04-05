@php
    if (!empty($itemValue) and !is_array($itemValue)) {
        $itemValue = json_decode($itemValue, true);
    }
@endphp

<div class="tab-pane mt-3 fade" id="sms_channels" role="tabpanel" aria-labelledby="sms_channels-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl() }}/settings/sms_channels" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="page" value="general">
                <input type="hidden" name="sms_channels" value="sms_channels">

                <div class="mb-5">
                    <h5>{{ trans('update.sms_channel') }}</h5>

                    <div class="form-group">
                        <label class="input-label">{{ trans('update.sms_sending_channel') }}</label>
                        <select name="value[sms_sending_channel]" class="form-control">
                        <option>Paid Plugin</option>
                        </select>
                    </div>
                </div>


                <div class="mb-5">
                    <h5>{{ trans('update.twilio_api_settings') }}</h5>

                    @foreach(['twilio_sid', 'twilio_auth_token', 'twilio_number']  as $twilioConf)
                        <div class="form-group">
                            <label>{{ trans("update.{$twilioConf}") }}</label>
                            <input type="text" name="value[{{ $twilioConf }}]" value="{{ (!empty($itemValue) and !empty($itemValue["{$twilioConf}"])) ? $itemValue["{$twilioConf}"] : old("{$twilioConf}") }}" class="form-control "/>
                        </div>
                    @endforeach

                </div>

                <div class="mb-5">
                    <h5>{{ trans('update.msegat_settings') }}</h5>

                    @foreach(['msegat_username', 'msegat_user_sender', 'msegat_api_key']  as $msegatConf)
                        <div class="form-group">
                            <label>{{ trans("update.{$msegatConf}") }}</label>
                            <input type="text" name="value[{{ $msegatConf }}]" value="{{ (!empty($itemValue) and !empty($itemValue["{$msegatConf}"])) ? $itemValue["{$msegatConf}"] : old("{$msegatConf}") }}" class="form-control "/>
                        </div>
                    @endforeach
                </div>

                <div class="mb-5">
                    <h5>{{ trans('update.vonage_settings') }}</h5>

                    @foreach(['vonage_number', 'vonage_key', 'vonage_secret', 'vonage_application_id', 'vonage_private_key']  as $vonageConf)
                        <div class="form-group">
                            <label>{{ trans("update.{$vonageConf}") }}</label>
                            <input type="text" name="value[{{ $vonageConf }}]" value="{{ (!empty($itemValue) and !empty($itemValue["{$vonageConf}"])) ? $itemValue["{$vonageConf}"] : old("{$vonageConf}") }}" class="form-control "/>
                        </div>
                    @endforeach
                </div>

                <div class="mb-5">
                    <h5>{{ trans('update.msg91_settings') }}</h5>

                    @foreach(['msg91_key', "msg91_flow_id"]  as $msg91Conf)
                        <div class="form-group">
                            <label>{{ trans("update.{$msg91Conf}") }}</label>
                            <input type="text" name="value[{{ $msg91Conf }}]" value="{{ (!empty($itemValue) and !empty($itemValue["{$msg91Conf}"])) ? $itemValue["{$msg91Conf}"] : old("{$msg91Conf}") }}" class="form-control "/>
                        </div>
                    @endforeach
                </div>

                <div class="mb-5">
                    <h5>{{ trans('update.2factor_settings') }}</h5>

                    @foreach(['2factor_api_key']  as $factorConf)
                        <div class="form-group">
                            <label>{{ trans("update.{$factorConf}") }}</label>
                            <input type="text" name="value[{{ $factorConf }}]" value="{{ (!empty($itemValue) and !empty($itemValue["{$factorConf}"])) ? $itemValue["{$factorConf}"] : old("{$factorConf}") }}" class="form-control "/>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
            </form>
        </div>
    </div>
</div>
