<?php

namespace Tests\Fakes;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Paylink\Client;

class ClientBadRequestFake extends Client
{
    protected function createHttpClient()
    {
        $mock = new MockHandler([
            new RequestException('Error Communicating with Server', new Request('GET', 'test'), new Response('400', [], 'fake bad request')),
        ]);

        $handlerStack = HandlerStack::create($mock);

        return  new HttpClient(['handler' => $handlerStack]);
    }
}
