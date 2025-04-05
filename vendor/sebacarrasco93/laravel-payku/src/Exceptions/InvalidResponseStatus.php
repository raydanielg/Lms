<?php

namespace SebaCarrasco93\LaravelPayku\Exceptions;

use Exception;

class InvalidResponseStatus extends Exception
{
    public $message;
    
    public function __construct($response)
    {
        $this->message = "Invalid response status: " . $response['status'];
    }
}
