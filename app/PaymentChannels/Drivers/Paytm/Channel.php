<?php

namespace App\PaymentChannels\Drivers\Paytm;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $merchant_id;
    protected $merchant_key;
    protected $merchant_website;
    protected $channel;
    protected $industry_type;

    protected array $credentialItems = [
        'merchant_id',
        'merchant_key',
        'merchant_website',
        'channel',
        'industry_type',
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
        \Config::set('services.paytm-wallet.env', $this->test_mode ? 'local' : 'production');
        \Config::set('services.paytm-wallet.merchant_id', $this->merchant_id);
        \Config::set('services.paytm-wallet.merchant_key', $this->merchant_key);
        \Config::set('services.paytm-wallet.merchant_website', $this->merchant_website);
        \Config::set('services.paytm-wallet.channel', $this->channel);
    }


    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $payment = PaytmWallet::with('receive');

        $payment->prepare([
            'order' => $order->id,
            'user' => $order->user_id,
            'email' => $order->user->email,
            'mobile_number' => $order->user->mobile,
            'amount' => $this->makeAmountByCurrency($order->total_amount, $this->currency),
            'callback_url' => $this->makeCallbackUrl($order)
        ]);

        return $payment->receive();
    }

    private function makeCallbackUrl(Order $order)
    {
        $callbackUrl = route('payment_verify_post', [
            'gateway' => 'Paytm'
        ]);

        return $callbackUrl;
    }

    public function verify(Request $request)
    {
        $this->handleConfigs();

        $paytmWallet = PaytmWallet::with('receive');

        $order = Order::find($paytmWallet->getOrderId());

        if ($paytmWallet->isSuccessful()) {
            $order->update(['status' => Order::$paying]);
        } else if ($paytmWallet->isFailed()) {
            $order->update(['status' => Order::$fail]);
        }

        return $order;
    }
}
