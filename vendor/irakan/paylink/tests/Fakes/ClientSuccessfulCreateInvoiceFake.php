<?php

namespace Tests\Fakes;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Paylink\Client;

class ClientSuccessfulCreateInvoiceFake extends Client
{
    protected function createHttpClient()
    {
        $json = '{"gatewayOrderRequest":{"amount":5,"orderNumber":"MERCHsANT-ANY-UNtIQUE-ORDER-NUMBER-123123123","callBackUrl":"https:\/\/www.example.com","clientEmail":"myclient@email.com","clientName":"Zaid Matooq","clientMobile":"0509200900","note":"This invoice is for VIP client.","products":[{"title":"Hand bag","price":150,"qty":1,"description":"Brown Hand bag","imageSrc":"http:\/\/merchantwebsite.com\/img\/img1.jpg"}]},"amount":5,"transactionNo":"1610407200131","orderStatus":"CREATED","paymentErrors":null,"url":"https:\/\/pilot.paylink.sa\/pay\/info\/1610407200131","success":true}';

        $mock = new MockHandler([
            new Response('200', [], '{"id_token": "fake-token"}'),
            new Response('200', [], $json),
        ]);

        $handlerStack = HandlerStack::create($mock);

        return  new HttpClient(['handler' => $handlerStack]);
    }
}
