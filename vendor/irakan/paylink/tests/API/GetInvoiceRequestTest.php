<?php

namespace Tests\API;

use Tests\Fakes\ClientSuccessfulInvoiceGetFake;
use Tests\TestCase;

class GetInvoiceRequestTest extends TestCase
{
    /** @test */
    public function an_invoice_can_be_created()
    {
        $client = new ClientSuccessfulInvoiceGetFake(['vendorId' => '4444', 'vendorSecret' => 'abc']);

        $response = $client->getInvoice('1610456769967');

        $this->assertSame(5, $response['gatewayOrderRequest']['amount']);
        $this->assertSame('Pending', $response['orderStatus']);
    }

    /** @test */
    public function dont_re_call_authorize_if_access_token_is_already_saved_on_the_client_object()
    {
        $mock = $this->getMockBuilder(ClientSuccessfulInvoiceGetFake::class)
            ->setConstructorArgs([['vendorId' => '4444', 'vendorSecret' => 'ABC']])
            ->onlyMethods(['getAccessToken', 'authorize'])
            ->getMock();

        $mock->expects($this->exactly(1))->method('getAccessToken')->willReturn('secret-token');
        $mock->expects($this->exactly(0))->method('authorize');

        $mock->getInvoice('123');
    }

    /** @test */
    public function call_authorize_if_access_token_doesnt_have_a_value_on_the_client_object()
    {
        $mock = $this->getMockBuilder(ClientSuccessfulInvoiceGetFake::class)
            ->setConstructorArgs([['vendorId' => '4444', 'vendorSecret' => 'ABC']])
            ->onlyMethods(['getAccessToken', 'authorize'])
            ->getMock();

        $mock->expects($this->exactly(1))->method('getAccessToken')->willReturn(null);
        $mock->expects($this->exactly(1))->method('authorize');

        $mock->getInvoice('123');
    }
}
