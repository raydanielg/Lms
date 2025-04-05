<?php

namespace Tests\API;

use Tests\Fakes\ClientSuccessfulCreateInvoiceFake;
use Tests\TestCase;

class CreateInvoiceRequestTest extends TestCase
{
    /** @test */
    public function an_invoice_can_be_created()
    {
        $client = new ClientSuccessfulCreateInvoiceFake(['vendorId' => '4444', 'vendorSecret' => 'ABC']);

        $response = $client->createInvoice([
            'amount' => 5,
            'callBackUrl' => 'https://www.example.com',
            'clientEmail' => 'myclient@email.com',
            'clientMobile' => '0509200900',
            'clientName' => 'Zaid Matooq',
            'note' => 'This invoice is for VIP client.',
            'orderNumber' => 'MERCHsANT-ANY-UNtIQUE-ORDER-NUMBER-123123123',
            'products' => [
                [
                    'description' => 'Brown Hand bag',
                    'imageSrc' => 'http://merchantwebsite.com/img/img1.jpg',
                    'price' => 150,
                    'qty' => 1,
                    'title' => 'Hand bag',
                ],
            ],
        ]);

        $this->assertSame(5, $response['gatewayOrderRequest']['amount']);
        $this->assertSame('MERCHsANT-ANY-UNtIQUE-ORDER-NUMBER-123123123', $response['gatewayOrderRequest']['orderNumber']);
        $this->assertSame('https://www.example.com', $response['gatewayOrderRequest']['callBackUrl']);
        $this->assertSame('myclient@email.com', $response['gatewayOrderRequest']['clientEmail']);
        $this->assertSame('Zaid Matooq', $response['gatewayOrderRequest']['clientName']);
        $this->assertSame('0509200900', $response['gatewayOrderRequest']['clientMobile']);
        $this->assertSame('This invoice is for VIP client.', $response['gatewayOrderRequest']['note']);
        $this->assertSame('Hand bag', $response['gatewayOrderRequest']['products'][0]['title']);
        $this->assertSame(150, $response['gatewayOrderRequest']['products'][0]['price']);
        $this->assertSame(1, $response['gatewayOrderRequest']['products'][0]['qty']);
        $this->assertSame('Brown Hand bag', $response['gatewayOrderRequest']['products'][0]['description']);
        $this->assertSame('http://merchantwebsite.com/img/img1.jpg', $response['gatewayOrderRequest']['products'][0]['imageSrc']);

        $this->assertSame(5, $response['amount']);
        $this->assertSame('1610407200131', $response['transactionNo']);
        $this->assertSame('CREATED', $response['orderStatus']);
        $this->assertNull($response['paymentErrors']);
        $this->assertSame('https://pilot.paylink.sa/pay/info/1610407200131', $response['url']);
        $this->assertTrue($response['success']);
    }

    /** @test */
    public function dont_re_call_authorize_if_access_token_is_already_saved_on_the_client_object()
    {
        $mock = $this->getMockBuilder(ClientSuccessfulCreateInvoiceFake::class)
            ->setConstructorArgs([['vendorId' => '4444', 'vendorSecret' => 'ABC']])
            ->onlyMethods(['getAccessToken', 'authorize'])
            ->getMock();

        $mock->expects($this->exactly(1))->method('getAccessToken')->willReturn('secret-token');
        $mock->expects($this->exactly(0))->method('authorize');

        $mock->createInvoice([]);
    }

    /** @test */
    public function call_authorize_if_access_token_doesnt_have_a_value_on_the_client_object()
    {
        $mock = $this->getMockBuilder(ClientSuccessfulCreateInvoiceFake::class)
            ->setConstructorArgs([['vendorId' => '4444', 'vendorSecret' => 'ABC']])
            ->onlyMethods(['getAccessToken', 'authorize'])
            ->getMock();

        $mock->expects($this->exactly(1))->method('getAccessToken')->willReturn(null);
        $mock->expects($this->exactly(1))->method('authorize');

        $mock->createInvoice([]);
    }
}
