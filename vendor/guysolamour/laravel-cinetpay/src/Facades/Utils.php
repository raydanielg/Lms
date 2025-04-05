<?php

namespace Guysolamour\Cinetpay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Utils
 *
 * @method static void routes()
 *
 * @see \Guysolamour\Cinetpay\Utils
 */
 class Utils extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cinetpay-utils';
    }
}
