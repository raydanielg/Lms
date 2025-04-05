<?php

namespace Omnipay\Paysera\Message;

use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;

abstract class AbstractRequest extends OmnipayAbstractRequest
{
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
}
