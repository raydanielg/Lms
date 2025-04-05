<?php

namespace Tests\API;

use InvalidArgumentException;
use Paylink\Client;
use Paylink\Exceptions\ApiException;
use Tests\Fakes\ClientBadRequestFake;
use Tests\Fakes\ClientSuccessfulAuthorizeFake;
use Tests\TestCase;

class AuthroizeRequestTest extends TestCase
{
    /** @test */
    public function thorw_expction_if_vendor_id_is_not_set()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('VendorId is empty');

        $client = new Client();
        $client->setVendorSecret('ABC');

        $client->authorize();
    }

    /** @test */
    public function thorw_expction_if_vendor_secret_is_not_set()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('vendorSecret is empty');

        $client = new Client();
        $client->setVendorId('ABC');

        $client->authorize();
    }

    /**
     * @test
     */
    public function thorw_invalid_response_expction_if_a_bad_request_happens()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('fake bad request');

        $client = new ClientBadRequestFake(['vendorId' => '4444', 'vendorSecret' => 'ABC']);

        $client->authorize();
    }

    /** @test */
    public function when_request_is_successful_an_id_token_will_be_returned()
    {
        $client = new ClientSuccessfulAuthorizeFake(['vendorId' => '4444', 'vendorSecret' => 'ABC']);

        $token = $client->authorize();

        $this->assertSame('fake-token', $token);
    }

    /** @test */
    public function token_can_be_saved_on_the_object()
    {
        $mock = $this->getMockBuilder(ClientSuccessfulAuthorizeFake::class)
            ->setConstructorArgs([['vendorId' => '4444', 'vendorSecret' => 'ABC']])
            ->onlyMethods(['setAccessToken'])
            ->getMock();

        $mock->expects($this->exactly(1))->method('setAccessToken')->with('fake-token');

        $mock->authorize();
    }
}
