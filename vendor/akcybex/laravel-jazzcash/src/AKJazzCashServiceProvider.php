<?php

namespace AKCybex\JazzCash;

use Illuminate\Support\ServiceProvider;

class AKJazzCashServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadHelpers();

        $this->registerConfigs();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
        }

        $this->app->singleton('jazzcash', function () {
            return new JazzCash();
        });
    }

    /**
     * Load helpers.
     */
    protected function loadHelpers()
    {
        foreach (glob(__DIR__ . '/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    public function registerConfigs()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/publishable/config/jazzcash.php',
            'jazzcash'
        );
    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $publishablePath = dirname(__DIR__) . '/publishable';

        $publishable = [
            'config' => [
                "{$publishablePath}/config/jazzcash.php" => config_path('jazzcash.php'),
            ],

        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(realpath(__DIR__ . '/../migrations'));
    }
}
