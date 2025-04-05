<div class="tab-pane mt-3 fade" id="database" role="tabpanel" aria-labelledby="database-tab">
    <div class="row">
        <div class="col-12 col-md-6">
            <form action="{{ getAdminPanelUrl("/settings/update-app/database") }}" method="post">
                {{ csrf_field() }}

                <p class="text-muted font-12">{{ trans('update.database_update_hint') }}</p>

                <div class="js-database-update-message my-25 text-success"></div>


                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="database_update_confirm" class="js-ajax-database_update_confirm custom-control-input" tabindex="3" id="databaseUpdateConfirm">
                        <label class="custom-control-label" for="databaseUpdateConfirm">{{ trans('update.database_update_confirm') }}</label>
                        <div class="invalid-feedback custom-inv-fck"></div>
                    </div>
                </div>


                <button type="button" class="js-database-update-btn btn btn-primary">{{ trans('update.run_update') }}</button>
            </form>
        </div>

    </div>
</div>
