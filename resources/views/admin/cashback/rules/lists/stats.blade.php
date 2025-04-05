<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-primary">
                <i class="fas fa-money-bill"></i>
            </div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>{{trans('update.total_rules')}}</h4>
                </div>
                <div class="card-body">
                    {{ $totalRules }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-warning">
                <i class="fas fa-check"></i>
            </div>

            <div class="card-wrap">
                <div class="card-header">
                    <h4>{{trans('update.active_rules')}}</h4>
                </div>
                <div class="card-body">
                    {{ $activeRules }}
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
            <div class="card-icon bg-success">
                <i class="fas fa-times"></i></div>
            <div class="card-wrap">
                <div class="card-header">
                    <h4>{{trans('update.disabled_rules')}}</h4>
                </div>
                <div class="card-body">
                    {{ $disabledRules }}
                </div>
            </div>
        </div>
    </div>

</div>
