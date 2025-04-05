<?php
return [
    'client_id'                 => env('PAYPAL_CLIENT_ID',null),
    'secret'                    => env('PAYPAL_SECRET',null),
    'currency'                  => env('PAYPAL_CURRENCY','USD'),
    'settings'                  => array(
        'mode'                  => env('PAYPAL_MODE','sandbox'),
        'http.ConnectionTimeOut'=> 30,
        'log.LogEnabled'        => true,
        'log.FileName'          => storage_path() . '/logs/paypal.log',
        'log.LogLevel'          => 'ERROR'
    ),
];
