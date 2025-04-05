<?php

namespace Omnipay\Paysera\Message;

use Omnipay\Paysera\Common\Encoder;
use Omnipay\Paysera\Common\Signature;
use Omnipay\Common\Exception\InvalidRequestException;

class AcceptNotificationRequest extends AbstractRequest
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'data' => $this->httpRequest->get('data'),
            'ss1' => $this->httpRequest->get('ss1'),
            'ss2' => $this->httpRequest->get('ss2'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \Omnipay\Paysera\Message\AcceptNotificationResponse
     *
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function sendData($data)
    {
        if (! Signature::isValid($data, $this->getPassword(), $this->httpClient)) {
            throw new InvalidRequestException('The signature is invalid.');
        }

        $this->response = new AcceptNotificationResponse($this, $this->parseData($data['data']));

        return $this->response;
    }

    /**
     * Parse the data.
     *
     * @param  string  $data
     * @return array
     */
    protected function parseData($data)
    {
        $parameters = [];

        parse_str(Encoder::decode($data), $parameters);

        return ! is_null($parameters) ? $parameters : [];
    }
}
