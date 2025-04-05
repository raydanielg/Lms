<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/payments/verify/Iyzipay',
        '/payments/verify/Paytm',
        '/payments/verify/JazzCash',
        '/payments/verify/Izipay',
        '/payments/verify/Sslcommerz',
        '/payments/verify/Ipay88',
        '/payments/verify/TapPayment',
        '/payments/verify/Paytr',
        '/payments/verify/Paytabs',
    ];
}
