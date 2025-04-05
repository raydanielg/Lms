<?php

namespace Omnipay\Yandex;

use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\AbstractGateway;
use Omnipay\Yandex\Helpers\ParametersTrait;
use Omnipay\Yandex\Message\PurchaseRequest;
use Omnipay\Yandex\Message\RefundRequest;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use YandexCheckout\Client;

/**
 * Braintree Gateway
 */
class Gateway extends AbstractGateway
{
    use ParametersTrait;

    /**
     * @var \YandexCheckout\Client
     */
    protected $yandex;

    /**
     * Gateway constructor.
     *
     * @param \Omnipay\Common\Http\ClientInterface|null      $httpClient
     * @param \Symfony\Component\HttpFoundation\Request|null $httpRequest
     */
    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        $this->yandex = new Client();

        parent::__construct($httpClient, $httpRequest);
    }

    /**
     * Create request
     *
     * @param string $class
     * @param array  $parameters
     *
     * @return mixed|\Omnipay\Common\Message\AbstractRequest
     */
    public function createRequest($class, array $parameters)
    {
        $this->yandex->setAuth($this->getShopId(), $this->getSecretKey());

        $obj = new $class($this->httpClient, $this->httpRequest, $this->yandex);

        return $obj->initialize(array_replace($this->getParameters(), $parameters));
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Yandex';
    }

    /**
     * Get default parameters
     * @return array|\Illuminate\Config\Repository|mixed
     */
    public function getDefaultParameters()
    {
        return [
            'shopId'    => '',
            'secretKey' => '',
        ];
    }

    /**
     * Get the request return URL.
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * Sets the request return URL.
     *
     * @param string $value
     * @return $this
     */
    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * Create a purchase request
     *
     * @param array $options
     *
     * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    public function purchase(array $options = array())
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * Create a refund request
     *
     * @param array $options
     *
     * @return mixed|\Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    public function refund(array $options = array())
    {
        return $this->createRequest(RefundRequest::class, $options);
    }
}
