<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

class UserLocale
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
        $generalSettings = getGeneralSettings();

        $defaultLocale = getDefaultLocale();

        $locale = $defaultLocale;

        if (auth()->check()) {
            $user = auth()->user();
            $locale = !empty($user->language) ? $user->language : $defaultLocale;
        } else {
            $checkCookie = Cookie::get('user_locale');

            if (!empty($checkCookie)) {
                $locale = $checkCookie;
            }
        }

        $userLanguages = $generalSettings['user_languages'] ?? [];

        if (!in_array($locale, $userLanguages)) {
            $locale = $defaultLocale;
        }

        \Session::put('locale', mb_strtolower($locale));

        return $next($request);
    }
}
