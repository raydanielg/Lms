<?php

namespace App\Http;

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\AdminLocale;
use App\Http\Middleware\Api\CheckRestrictionAPI;
use App\Http\Middleware\CheckMaintenance;
use App\Http\Middleware\CheckMobileApp;
use App\Http\Middleware\CheckRestriction;
use App\Http\Middleware\Impersonate;
use App\Http\Middleware\PanelAuthenticate;
use App\Http\Middleware\Share;
use App\Http\Middleware\UserNotAccess;
use App\Http\Middleware\WebAuthenticate;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\UserLocale::class,
            \App\Http\Middleware\DebugBar::class
        ],
        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\Api\CheckApiKey::class,
            \App\Http\Middleware\Api\SetLocale::class,
            \App\Http\Middleware\Api\CheckMaintenance::class,
            \App\Http\Middleware\Api\CheckRestrictionAPI::class,
        ],

    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin' => AdminAuthenticate::class,
        'panel' => PanelAuthenticate::class,
        'user.not.access' => UserNotAccess::class,
        'web.auth' => WebAuthenticate::class,
        'impersonate' => Impersonate::class,
        'share' => Share::class,
        'admin_locale' => AdminLocale::class,
        'check_mobile_app' => CheckMobileApp::class,
        'check_maintenance' => CheckMaintenance::class,
        'check_restriction' => CheckRestriction::class,
        // api
        'api.auth' => \App\Http\Middleware\Api\Authenticate::class,
        'api.guest' => \App\Http\Middleware\Api\RedirectIfAuthenticated::class,
        'api.request.type' => \App\Http\Middleware\Api\RequestType::class,
        'api.identify' => \App\Http\Middleware\Api\CheckApiKey::class,
        'api.level-access' => \App\Http\Middleware\Api\LevelAccess::class,
        'check_restriction_api' => CheckRestrictionAPI::class,

    ];
}
