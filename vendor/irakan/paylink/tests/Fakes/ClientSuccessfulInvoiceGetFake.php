<?php

namespace Tests\Fakes;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Paylink\Client;

class ClientSuccessfulInvoiceGetFake extends Client
{
    protected function createHttpClient()
    {
        $json = '{"gatewayOrderRequest":{"amount":5,"orderNumber":"19b4cec4-16c5-4ea3-8e8e-74bbc83e6c0d","callBackUrl":"https:\/\/www.example.com","clientEmail":"","clientName":"Robbie Hoppe","clientMobile":"","note":"","products":[{"title":"Gateway Unknown Products","price":977,"qty":1,"description":"Gateway Unknown Products","imageSrc":null}]},"amount":977,"transactionNo":"1610456769967","orderStatus":"Pending","paymentErrors":null,"url":"https:\/\/pilot.paylink.sa\/pay\/info\/1610456769967","success":true}';

        $mock = new MockHandler([
            new Response('200', [], '{"id_token": "fake-token"}'),
            new Response('200', [], $json),
        ]);

        $handlerStack = HandlerStack::create($mock);

        return  new HttpClient(['handler' => $handlerStack]);
    }
}
