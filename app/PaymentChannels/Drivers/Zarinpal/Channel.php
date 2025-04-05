<?php

namespace App\PaymentChannels\Drivers\Zarinpal;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment as Paymenter;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $merchant_id;

    protected array $credentialItems = [
        'merchant_id',
    ];

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->setCredentialItems($paymentChannel);
    }

    private function handleConfigs()
    {
        \Config::set('payment.drivers.zarinpal.mode', $this->test_mode ? 'sandbox' : 'normal'); // can be normal, sandbox, zaringate
        \Config::set('payment.drivers.zarinpal.merchantId', $this->merchant_id);
    }

    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $invoice = (new Invoice)->amount($this->makeAmountByCurrency($order->total_amount, $this->currency))
            ->detail([
                'description' => trans('public.paid_form_online_payment'),
                'email' => $order->user->email,
                'mobile' => $order->user->mobile,
            ]);

        return Paymenter::callbackUrl(url('/payments/verify/Zarinpal'))
            ->purchase(
                $invoice,
                function ($driver, $transactionId) use ($order) {
                    session()->put('zarinpal.payments.transaction_id', $transactionId);
                    session()->put('zarinpal.payments.order_id', $order->id);
                }
            )->pay()->render();
    }

    public function verify(Request $request)
    {
        $this->handleConfigs();
        $user = auth()->user();

        $transactionId = session()->get('zarinpal.payments.transaction_id', null);
        $orderId = session()->get('zarinpal.payments.order_id', null);

        session()->forget('zarinpal.payments.transaction_id');
        session()->forget('zarinpal.payments.order_id');

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!empty($order)) {
            try {
                $receipt = Paymenter::amount($this->makeAmountByCurrency($order->total_amount, $this->currency))->transactionId($transactionId)->verify();
                $referenceId = $receipt->getReferenceId();

                if ($referenceId) {
                    $order->update(['status' => Order::$paying]);
                } else {
                    $order->update(['status' => Order::$fail]);
                }

                return $order;
            } catch (InvalidPaymentException $exception) {
                echo $exception->getMessage();
            }
        }
    }
}
