<?php

namespace SebaCarrasco93\LaravelPayku\Exceptions;

use Illuminate\Support\Collection;
use Exception;

class CantCreateTransactionId extends Exception
{
    public $message;
    
    public function __construct(Collection $response)
    {
        $this->message = "Can't create your transaction: {$response->implode(', ')}";
    }
}
