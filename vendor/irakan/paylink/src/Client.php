<?php

namespace Paylink;

use Paylink\Traits\API;
use phpDocumentor\Reflection\Types\Boolean;

class Client
{
    use API;

    private $vendorId;
    private $vendorSecret;
    private $persistToken;
    private $environment;
    private $accessToken;

    public function __construct(array $options = [])
    {
        $this->vendorId = $options['vendorId'] ?? null;
        $this->vendorSecret = $options['vendorSecret'] ?? null;
        $this->persistToken = $options['persistToken'] ?? false;
        $this->environment = $options['environment'] ?? 'testing';

        $this->setHttpClient();
    }

    public function setVendorId(string $vendorId)
    {
        $this->vendorId = $vendorId;
    }

    public function getVendorId()
    {
        return $this->vendorId;
    }

    public function setVendorSecret(string $vendorSecret)
    {
        $this->vendorSecret = $vendorSecret;
    }

    public function getVendorSecret()
    {
        return $this->vendorSecret;
    }

    public function setPersistToken(Boolean $persistToken)
    {
        $this->persistToken = $persistToken;
    }

    public function getPersistToken()
    {
        return $this->persistToken;
    }

    public function setEnvironment(string $environment)
    {
        $this->environment = $environment;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    protected function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    protected function getAccessToken()
    {
        return $this->accessToken;
    }
}
