<?php

namespace Omnipay\Paysera;

use Omnipay\Common\AbstractGateway;
use Omnipay\Paysera\Message\PurchaseRequest;
use Omnipay\Paysera\Message\AcceptNotificationRequest;

class Gateway extends AbstractGateway
{
    /**
     * Version of API.
     */
    const VERSION = '1.6';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Paysera';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return [
            'testMode' => true,
            'version' => self::VERSION,
        ];
    }

    /**
     * Get the Project ID.
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->getParameter('projectId');
    }

    /**
     * Set the Project ID.
     *
     * @param  string  $value
     * @return $this
     */
    public function setProjectId($value)
    {
        return $this->setParameter('projectId', $value);
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * Set the password.
     *
     * @param  string  $value
     * @return $this
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
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
     * {@inheritdoc}
     */
    public function purchase(array $options = [])
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function acceptNotification(array $options = [])
    {
        return $this->createRequest(AcceptNotificationRequest::class, $options);
    }
}
