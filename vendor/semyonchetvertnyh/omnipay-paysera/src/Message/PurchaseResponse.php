<?php

namespace Omnipay\Paysera\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * Get the API endpoint.
     *
     * @return string
     */
    protected function getEndpoint()
    {
        return 'https://www.paysera.com/pay/';
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUrl()
    {
        return $this->getEndpoint();
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectData()
    {
        return $this->getData();
    }
}
