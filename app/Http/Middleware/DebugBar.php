<?php namespace App\Http\Middleware;

use Closure;

class DebugBar
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app('debugbar')->disable();

        if (!empty(getGeneralSettings('app_debugbar'))) {
            app('debugbar')->enable();
        }

        return $next($request);
    }
}
