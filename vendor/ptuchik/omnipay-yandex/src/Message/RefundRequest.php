<?php

namespace Omnipay\Yandex\Message;

use YandexCheckout\Model\PaymentStatus;

/**
 * Class RefundRequest
 * @package Omnipay\Yandex\Message
 */
class RefundRequest extends AbstractRequest
{
    public function getData()
    {
        return [
            'payment_id'  => $this->getTransactionReference(),
            'amount'      => [
                'value'    => $this->getAmount(),
                'currency' => $this->getCurrency()
            ],
            'description' => $this->getDescription()
        ];
    }

    public function sendData($data)
    {
        $payment = $this->yandex->createRefund($data);

        if ($payment->getStatus() == PaymentStatus::WAITING_FOR_CAPTURE) {
            $response = $this->yandex->capturePayment(['amount' => array_get($data, 'amount', [])], $payment->getId());
        } else {
            $response = $payment;
        }

        return $this->response = new RefundResponse($this, $response);
    }
}