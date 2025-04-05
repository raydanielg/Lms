<?php

namespace Omnipay\Paysera\Common;

class Encoder
{
    /**
     * Encode the input.
     *
     * @param  string  $input
     * @return string
     */
    public static function encode($input)
    {
        return strtr(base64_encode($input), ['+' => '-', '/' => '_']);
    }

    /**
     * Decode the input.
     *
     * @param  string  $input
     * @return string
     */
    public static function decode($input)
    {
        return base64_decode(strtr($input, ['-' => '+', '_' => '/']));
    }
}
