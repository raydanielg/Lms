<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Web\CartManagerController;
use App\Mixins\Financial\MultiCurrency;
use App\Models\Cart;
use App\Models\Currency;
use App\Models\FloatingBar;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class AdminLocale
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
        // locale config
        if (!Session::has('locale')) {
            Session::put('locale', mb_strtolower(getDefaultLocale()));
        }
        App::setLocale(session('locale'));

        $generalSettings = getGeneralSettings();
        view()->share('generalSettings', $generalSettings);

        $defaultLocale = getDefaultLocale();
        view()->share('defaultLocale', $defaultLocale);

        return $next($request);
    }
}
