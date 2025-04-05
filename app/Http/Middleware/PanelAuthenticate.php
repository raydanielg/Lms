<?php

namespace App\Http\Middleware;

use App\Models\AiContentTemplate;
use Closure;
use Illuminate\Support\Facades\Auth;

class PanelAuthenticate
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
        if (!auth()->user() and !empty(apiAuth())) {
            auth()->setUser(apiAuth());
        }

        if (auth()->check() and !auth()->user()->isAdmin()) {

            $referralSettings = getReferralSettings();
            view()->share('referralSettings', $referralSettings);

            $aiContentTemplates = AiContentTemplate::query()->where('enable', true)->get();
            view()->share('aiContentTemplates', $aiContentTemplates);

            return $next($request);
        }

        return redirect('/login');
    }
}
