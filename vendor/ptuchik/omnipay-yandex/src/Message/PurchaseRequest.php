<?php

namespace Omnipay\Yandex\Message;

use YandexCheckout\Model\PaymentStatus;

/**
 * Class PurchaseRequest
 * @package Omnipay\Yandex\Message
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * Get gateway client
     * @return \YandexCheckout\Client
     */
    public function getClient()
    {
        return $this->yandex;
    }

    /**
     * Prepare data to send
     * @return array|mixed
     */
    public function getData()
    {
        $data = [
            'amount'      => [
                'value'    => $this->getAmount(),
                'currency' => $this->getCurrency()
            ],
            'client_ip'   => $this->getClientIp(),
            'description' => $this->getDescription()
        ];

        if ($this->getItems()) {
            $data['receipt']['items'] = $this->getItems();

            if ($this->getTaxSystemCode()) {
                $data['receipt']['tax_system_code'] = $this->getTaxSystemCode();
            }

            if ($this->getPhone()) {
                $data['receipt']['phone'] = $this->getPhone();
            } elseif ($this->getEmail()) {
                $data['receipt']['email'] = $this->getEmail();
            }
        }

        if ($this->getConfirmationType()) {
            $data['confirmation']['type'] = $this->getConfirmationType();
            $data['confirmation']['enforce'] = $this->getConfirmPayment() ?? false;
            $data['confirmation']['return_url'] = $this->getReturnUrl();
        }

        if ($this->getGatewayId()) {
            $data['recipient']['gateway_id'] = $this->getGatewayId();
        }

        if ($this->getToken()) {
            $data['payment_token'] = $this->getToken();
        }

        if ($this->getPaymentMethodId()) {
            $data['payment_method_id'] = $this->getPaymentMethodId();
        } elseif ($this->getPaymentMethod()) {
            $data['payment_method_data']['type'] = $this->getPaymentMethod();
        }

        if ($this->getSavePaymentMethod()) {
            $data['save_payment_method'] = $this->getSavePaymentMethod();
        }

        if ($this->getCapture()) {
            $data['capture'] = $this->getCapture();
        }

        if ($this->getMetadata()) {
            $data['metadata'] = $this->getMetadata();
        }

        return $data;
    }

    /**
     * Send data and return response instance
     *
     * @param mixed $data
     *
     * @return \Omnipay\Common\Message\ResponseInterface|\Omnipay\Idram\Message\PurchaseResponse
     */
    public function sendData($data)
    {
        $payment = $this->yandex->createPayment($data);

        if ($payment->getStatus() == PaymentStatus::WAITING_FOR_CAPTURE) {
            $response = $this->yandex->capturePayment(['amount' => array_get($data, 'amount', [])], $payment->getId());
        } else {
            $response = $payment;
        }

        return $this->response = new PurchaseResponse($this, $response);
    }
}