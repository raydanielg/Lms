<?php

namespace AKCybex\JazzCash;

use AKCybex\JazzCash\JazzCashModes\Http\JazzCashRedirectRequest;
use AKCybex\JazzCash\JazzCashModes\Http\JazzCashRedirectResponse;

class JazzCash
{
    public function request()
    {
        return new JazzCashRedirectRequest();
    }

    public function response()
    {
        return new JazzCashRedirectResponse();
    }
}
