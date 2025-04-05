<?php

namespace Paylink\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Paylink\Exceptions\ApiException;

trait HttpClient
{
    private $liveEndpoint = 'https://restapi.paylink.sa/api/';
    private $testEndpoint = 'https://restpilot.paylink.sa/api/';

    private Client $client;

    protected function setHttpClient()
    {
        $this->client = $this->createHttpClient();
    }

    protected function createHttpClient()
    {
        return new Client([
            'base_uri' => $this->getEndpoint(),
            'headers' => [
                'content-type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    protected function sendRequest(string $method, string $path, array $data, $token = null)
    {
        if (! $this->vendorId) {
            throw new InvalidArgumentException('VendorId is empty');
        }

        if (! $this->vendorSecret) {
            throw new InvalidArgumentException('vendorSecret is empty');
        }

        $headers = $token ? ['Authorization' => "Bearer {$token}"] : [];

        try {
            $response = $this->client->request($method, $path, [
                'json' => $data,
                'headers' => $headers,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $exception) {
            if ($exception->getResponse() !== null) {
                throw new ApiException('Api error: '.$exception->getResponse()->getBody()->getContents(), $exception->getCode(), $exception);
            }

            throw $exception;
        }
    }

    public function getEndpoint()
    {
        return $this->getEnvironment() === 'testing' ? $this->testEndpoint : $this->liveEndpoint;
    }
}
