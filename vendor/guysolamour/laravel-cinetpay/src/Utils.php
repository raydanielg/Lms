<?php

namespace Guysolamour\Cinetpay;


class Utils
{
    public function routes()
    {
        $router = app()->make('router');

        $routes = $this->getRoutePaths();

        foreach ($routes as $route){
            $verb = $route['verb'];
            $router
                ->$verb($route['path'], [config('cinetpay.controller'), $route['method']])
                ->name("cinetpay.{$route['method']}")
                ->withoutMiddleware('web')
                ->middleware('api')
                ;
        }
    }

    private function getRoutePaths() :array
    {
        $routes = [];

        foreach (config('cinetpay.urls', []) as $key => $value) {
            if (filter_var($value, FILTER_VALIDATE_URL)){
                continue;
            }

            $routes[] = [
                'method'   => $key,
                'path'     => $value,
                'verb'     => $key == 'cancel' ? 'get' : 'get',
            ];
        }

       return $routes;
    }
}

