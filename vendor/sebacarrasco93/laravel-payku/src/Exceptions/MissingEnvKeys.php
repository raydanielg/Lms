<?php

namespace SebaCarrasco93\LaravelPayku\Exceptions;

use Exception;

class MissingEnvKeys extends Exception
{
    public $message;
    
    public function __construct()
    {
        $this->message = 'Please set all Payku keys in your .env file';
    }
}
