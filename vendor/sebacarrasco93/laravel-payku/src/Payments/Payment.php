<?php

namespace SebaCarrasco93\LaravelPayku\Payments;

abstract class Payment
{
    public static $code;

    public static function getNumber(): string
    {
        return self::$code;
    }
}
