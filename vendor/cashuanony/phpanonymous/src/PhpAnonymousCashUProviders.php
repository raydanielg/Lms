<?php
namespace CashUAony\Phpanonymous;

use Illuminate\Support\ServiceProvider;
use CashUAony\Phpanonymous\CashU;
use Config;

class PhpAnonymousCashUProviders extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if(!file_exists(base_path('config').'/cashu.php'))
        {
          $this->publishes([
            __DIR__.'/config' => base_path('config'),
          ]);
        }
     }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
             $this->app['Cashu'] = $this->app->share(function($app)
            {
                return new Cashu();
            });

    }
}


 