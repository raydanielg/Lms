<?php

namespace Omnipay\Yandex\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Yandex\Helpers\ParametersTrait;
use YandexCheckout\Model\PaymentStatus;

/**
 * Class PurchaseResponse
 * @package Omnipay\Yandex\Message
 */
class PurchaseResponse extends AbstractResponse
{
    use ParametersTrait;

    /**
     * Get amount
     * @return mixed
     */
    public function getAmount()
    {
        return $this->data->getAmount()->getValue();
    }

    /**
     * Get transaction reference
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->data->getId();
    }

    /**
     * Get transaction data
     * @return mixed|object
     */
    public function getData()
    {
        return (object) ['transaction' => $this->data];
    }

    /**
     * Get successful status
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->data->getStatus() == PaymentStatus::SUCCEEDED;
    }
}