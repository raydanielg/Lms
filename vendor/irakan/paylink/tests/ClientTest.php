<?php

namespace Tests;

use Paylink\Client;

class ClientTest extends TestCase
{
    /** @test */
    public function vendor_id_can_be_set()
    {
        $client = new Client();

        $client->setVendorId('4444');

        $this->assertSame('4444', $client->getVendorId());
    }

    /** @test */
    public function vendor_secret_can_be_set()
    {
        $client = new Client();

        $client->setVendorSecret('ABC');

        $this->assertSame('ABC', $client->getVendorSecret());
    }

    /** @test */
    public function paramters_can_be_set_using_constuctor()
    {
        $client = new Client([
            'vendorId' => '4444',
            'vendorSecret' => 'ABC',
            'persistToken' => true,
            'environment' => 'testing',
        ]);

        $this->assertSame('4444', $client->getVendorId());
        $this->assertSame('ABC', $client->getVendorSecret());
        $this->assertTrue($client->getPersistToken());
        $this->assertSame('testing', $client->getEnvironment());
    }

    /** @test */
    public function persist_token_is_set_to_false_by_default()
    {
        $client = new Client(['vendorId' => '4444', 'vendorSecret' => 'ABC']);

        $this->assertFalse($client->getPersistToken());
    }

    /** @test */
    public function environment_is_set_to_testing_by_default()
    {
        $client = new Client();

        $this->assertSame('testing', $client->getEnvironment());
    }

    /** @test */
    public function environment_can_be_set()
    {
        $client = new Client();

        $client->setEnvironment('testing');

        $this->assertSame('testing', $client->getEnvironment());
    }

    /** @test */
    public function testing_endpoint_will_be_given_based_on_env()
    {
        $client = new Client();

        $this->assertSame('https://restpilot.paylink.sa/api/', $client->getEndPoint());
    }

    /** @test */
    public function live_endpoint_will_be_given_based_on_env()
    {
        $client = new Client();
        $client->setEnvironment('live');

        $this->assertSame('https://restapi.paylink.sa/api/', $client->getEndPoint());
    }
}
