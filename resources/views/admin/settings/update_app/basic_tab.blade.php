<div class="tab-pane mt-3 fade show active" id="basic" role="tabpanel" aria-labelledby="basic-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl("/settings/update-app/basic") }}" method="post">
                {{ csrf_field() }}

                <div class="form-group">
                    <label class="">{{ trans('update.basic_update_zip_file') }}</label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="file" class="js-ajax-file custom-file-input cursor-pointer" id="basicZip">
                            <label class="custom-file-label cursor-pointer" for="basicZip">{{ trans('update.choose_file') }}</label>
                            <div class="invalid-feedback custom-inv-fck"></div>
                        </div>
                    </div>

                    <p class="text-muted mt-3">{{ trans('update.basic_update_zip_file_hint') }}</p>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="basic_update_confirm" class="js-ajax-basic_update_confirm custom-control-input" tabindex="3" id="basicUpdateConfirm">
                        <label class="custom-control-label" for="basicUpdateConfirm">{{ trans('update.basic_update_confirm') }}</label>
                        <div class="invalid-feedback custom-inv-fck"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="progress d-none">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                    </div>
                </div>

                <button type="button" class="js-update-btn btn btn-primary">{{ trans('update.update') }}</button>
            </form>
        </div>

        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl("/settings/update-app/custom-update") }}" method="post">
                {{ csrf_field() }}

                <div class="form-group">
                    <label class="">{{ trans('update.update_with_json') }}</label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text">
                                <i class="fa fa-upload"></i>
                            </button>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="file" class="js-ajax-file custom-file-input cursor-pointer" id="basicZip">
                            <label class="custom-file-label cursor-pointer" for="basicZip">{{ trans('update.choose_file') }}</label>
                            <div class="invalid-feedback custom-inv-fck"></div>
                        </div>
                    </div>

                    <p class="text-muted mt-3">{{ trans('update.update_with_json_hint') }}</p>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="custom_update_confirm" class="js-ajax-custom_update_confirm custom-control-input" tabindex="3" id="customUpdateConfirm">
                        <label class="custom-control-label" for="customUpdateConfirm">{{ trans('update.custom_json_update_confirm') }}</label>
                        <div class="invalid-feedback custom-inv-fck"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="progress d-none">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                    </div>
                </div>

                <button type="button" class="js-update-btn btn btn-primary">{{ trans('update.update') }}</button>
            </form>
        </div>

    </div>
</div>
