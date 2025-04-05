<?php

namespace Omnipay\Yandex\Message;

use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Omnipay\Yandex\Helpers\ParametersTrait;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use YandexCheckout\Client;

/**
 * Class AbstractRequest
 * @package Omnipay\Yandex\Message
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    use ParametersTrait;

    /**
     * @var \YandexCheckout\Client
     */
    protected $yandex;

    /**
     * AbstractRequest constructor.
     *
     * @param \Omnipay\Common\Http\ClientInterface      $httpClient
     * @param \Symfony\Component\HttpFoundation\Request $httpRequest
     * @param \YandexCheckout\Client                    $yandex
     */
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest, Client $yandex)
    {
        $this->yandex = $yandex;

        parent::__construct($httpClient, $httpRequest);
    }
}