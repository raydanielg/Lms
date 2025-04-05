<?php

return [
    'environment' => env('JAZZCASH_ENVIRONMENT', 'sandbox'),
    'sandbox'     => [
        'merchant_id'     => env('SANDBOX_JAZZCASH_MERCHANT_ID'),
        'password'        => env('SANDBOX_JAZZCASH_PASSWORD'),
        'integerity_salt' => env('SANDBOX_JAZZCASH_INTEGERITY_SALT'),
        'return_url'      => env('SANDBOX_JAZZCASH_RETURN_URL'),
        'endpoint'        => env('SANDBOX_JAZZCASH_ENDPOINT'),
    ],
    'live'        => [
        'merchant_id'     => env('JAZZCASH_MERCHANT_ID'),
        'password'        => env('JAZZCASH_PASSWORD'),
        'integerity_salt' => env('JAZZCASH_INTEGERITY_SALT'),
        'return_url'      => env('JAZZCASH_RETURN_URL'),
        'endpoint'        => env('JAZZCASH_ENDPOINT'),
    ],
];
