<?php

namespace Guysolamour\Cinetpay;

use Guysolamour\Cinetpay\Utils;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/cinetpay.php';

    public function boot()
    {
        $this->app->bind('cinetpay-utils', function () {
            return new Utils;
        });

        $this->app->bind(Cinetpay::class, function () {
            return Cinetpay::getTransactionById();
        });

        $this->publishes([
            self::CONFIG_PATH => config_path('cinetpay.php'),
        ], 'config');

        $this->loadTranslationsFrom(static::packagePath('resources/lang'), 'cinetpay');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'cinetpay'
        );
    }


    public static function packagePath(string $path = ''): string
    {
        return  __DIR__ . '/../' . $path;
    }
}
