<?php

namespace App\Http\Middleware\Api;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Closure;

class SetLocale
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

        $userLanguages = $generalSettings['user_languages'] ?? [];
        $locale = $defaultLocale;


        if (auth('api')->check() and !$request->header('x-locale')) {
            $user = auth('api')->user();
            $locale = !empty($user->language) ? $user->language : $defaultLocale;
        } elseif ($request->header('x-locale')) {
            $locale = $request->header('x-locale');
        }

        $locale = strtoupper($locale);

        if (!in_array($locale, $userLanguages)) {
            $locale = strtoupper($defaultLocale);
        }

        if (auth('api')->check()) {
            $user = auth('api')->user();

            $user->update([
                'language' => $locale
            ]);
        }

       // $locale='EN' ;
        App::setLocale(strtolower($locale));

      /*  if ($request->header('x-locale')) {

            $locale = $request->header('x-locale');
            $locale = localeToCountryCode(mb_strtoupper($locale), true);
            $generalSettings = getGeneralSettings();
            $userLanguages = $generalSettings['user_languages'];

            if (in_array($locale, $userLanguages)) {
                if (auth()->check()) {
                    $user = auth()->user();
                    $user->update([
                        'language' => $locale
                    ]);
                } else {
                    //   Cookie::queue('user_locale', $locale, 30 * 24 * 60);
                }
            } else {
                $locale = getDefaultLocale();
            }
            App::setLocale($locale);

        }*/

        return $next($request);

    }

}
