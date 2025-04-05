<?php

namespace AKCybex\JazzCash\Facades;

use Illuminate\Support\Facades\Facade;

class JazzCash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'jazzcash';
    }
}
