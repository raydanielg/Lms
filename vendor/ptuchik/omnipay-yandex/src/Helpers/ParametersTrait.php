<?php

namespace Omnipay\Yandex\Helpers;

/**
 * Trait ParametersTrait
 * @package Omnipay\Yandex
 */
trait ParametersTrait
{
    /**
     * Sets the request shop ID.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setShopId($value)
    {
        return $this->setParameter('shopId', $value);
    }

    /**
     * Get the request shop ID.
     * @return $this
     */
    public function getShopId()
    {
        return $this->getParameter('shopId');
    }

    /**
     * Sets the request secret key.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setSecretKey($value)
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * Get the request secret key.
     * @return $this
     */
    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    /**
     * Sets the request confirmation type.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setConfirmationType($value)
    {
        return $this->setParameter('confirmationType', $value);
    }

    /**
     * Get the request confirmation type.
     * @return $this
     */
    public function getConfirmationType()
    {
        return $this->getParameter('confirmationType');
    }

    /**
     * Sets the request confirm payment.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setConfirmPayment($value)
    {
        return $this->setParameter('confirmPayment', !empty($value));
    }

    /**
     * Get the request confirm payment.
     * @return $this
     */
    public function getConfirmPayment()
    {
        return $this->getParameter('confirmPayment');
    }

    /**
     * Sets the request save payment method.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setSavePaymentMethod($value)
    {
        return $this->setParameter('savePaymentMethod', !empty($value));
    }

    /**
     * Get the request save payment method.
     * @return $this
     */
    public function getSavePaymentMethod()
    {
        return $this->getParameter('savePaymentMethod');
    }

    /**
     * Sets the request capture.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setCapture($value)
    {
        return $this->setParameter('capture', !empty($value));
    }

    /**
     * Get the request capture.
     * @return $this
     */
    public function getCapture()
    {
        return $this->getParameter('capture');
    }

    /**
     * Sets the request tax system code.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setTaxSystemCode($value)
    {
        return $this->setParameter('taxSystemCode', $value);
    }

    /**
     * Get the request tax system code.
     * @return $this
     */
    public function getTaxSystemCode()
    {
        return $this->getParameter('taxSystemCode');
    }

    /**
     * Sets the request phone.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setPhone($value)
    {
        return $this->setParameter('phone', $value);
    }

    /**
     * Get the request phone.
     * @return $this
     */
    public function getPhone()
    {
        return $this->getParameter('phone');
    }

    /**
     * Sets the request phone.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * Get the request phone.
     * @return $this
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Sets the request gateway ID.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setGatewayId($value)
    {
        return $this->setParameter('gatewayId', $value);
    }

    /**
     * Get the request gateway ID.
     * @return $this
     */
    public function getGatewayId()
    {
        return $this->getParameter('gatewayId');
    }

    /**
     * Sets the request payment method ID.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setPaymentMethodId($value)
    {
        return $this->setParameter('paymentMethodId', $value);
    }

    /**
     * Get the request payment method ID.
     * @return $this
     */
    public function getPaymentMethodId()
    {
        return $this->getParameter('paymentMethodId');
    }

    /**
     * Sets the request metadata.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setMetadata($value)
    {
        return $this->setParameter('metadata', $value);
    }

    /**
     * Get the request metadata.
     * @return $this
     */
    public function getMetadata()
    {
        return $this->getParameter('metadata');
    }
}