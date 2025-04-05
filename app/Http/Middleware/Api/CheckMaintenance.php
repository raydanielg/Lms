<?php

namespace App\Http\Middleware\Api;

use Closure;

class CheckMaintenance
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!empty(getFeaturesSettings('maintenance_status')) and getFeaturesSettings('maintenance_status')) {
            $maintenanceAccessKey = getFeaturesSettings('maintenance_access_key');

            if (!empty($maintenanceAccessKey) and !empty($request->header($maintenanceAccessKey))) {
                return $next($request);
            }
            return apiResponse2(0,'maintenance',trans('maintenance'),getMaintenanceSettings()) ;
        }
        return $next($request);
    }
}
