<?php

namespace Omnipay\Paysera\Message;

use Omnipay\Paysera\Common\Purchase;
use Omnipay\Paysera\Common\Signature;

class PurchaseRequest extends AbstractRequest
{
    /**
     * {@inheritdoc}
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('projectId', 'password');

        return [
            'data' => $data = Purchase::generate($this),
            'sign' => Signature::generate($data, $this->getPassword()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $this->response = new PurchaseResponse($this, $data);

        return $this->response;
    }

    /**
     * Get the API version.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->getParameter('version');
    }

    /**
     * Set the API version.
     *
     * @param  string  $value
     * @return $this
     */
    public function setVersion($value)
    {
        return $this->setParameter('version', $value);
    }

    /**
     * Get the language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * Set the language.
     *
     * @param  string  $value
     * @return $this
     */
    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }
}
