<?php

namespace SebaCarrasco93\LaravelPayku\Traits;

use SebaCarrasco93\LaravelPayku\Models\PaykuTransaction;
use SebaCarrasco93\LaravelPayku\Payments\Payment;
use SebaCarrasco93\LaravelPayku\Payments\Webpay;

trait PrepareOrders
{
    public function prepareOrder(string $order, string $subject, int $amount, string $email, ?Payment $payment = null)
    {
        if (! $payment) {
            $payment = new Webpay;
        }

        return [
            'email' => $email,
            'order' => $order, 
            'subject' => $subject,
            'amount' => $amount,
            'payment' => $payment,
            'urlreturn' => route('payku.return', $order),
            'urlnotify' => route('payku.notify', $order),
        ];
    }
}
