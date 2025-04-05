<?php

namespace Tests\Fakes;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Paylink\Client;

class ClientSuccessfulAuthorizeFake extends Client
{
    protected function createHttpClient()
    {
        $mock = new MockHandler([new Response('200', [], '{"id_token": "fake-token"}')]);

        $handlerStack = HandlerStack::create($mock);

        return  new HttpClient(['handler' => $handlerStack]);
    }
}
