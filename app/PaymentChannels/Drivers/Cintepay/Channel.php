<?php

namespace App\PaymentChannels\Drivers\Cintepay;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Guysolamour\Cinetpay\Cinetpay;


class Channel extends BasePaymentChannel implements IChannel
{

    protected $order_session_key;
    protected $currency;
    protected $test_mode;
    protected $api_key;
    protected $site_id;
    protected $secret_key;

    protected array $credentialItems = [
        'api_key',
        'site_id',
        'secret_key',
    ];

    // https://github.com/guysolamour/laravel-cinetpay

    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->order_session_key = 'cintepay.payments.order_id';
        $this->currency = 'XOF';//currency(); // TODO:: return this after test
        $this->setCredentialItems($paymentChannel);
    }

    private function handleConfigs()
    {
        \Config::set('cinetpay.api_key', $this->api_key);
        \Config::set('cinetpay.site_id', $this->site_id);
        \Config::set('cinetpay.urls.notify', $this->makeCallbackUrl("notify"));
        \Config::set('cinetpay.urls.return', $this->makeCallbackUrl("return"));
        \Config::set('cinetpay.urls.cancel', $this->makeCallbackUrl("cancel"));
    }


    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $generalSettings = getGeneralSettings();
        $user = $order->user;
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);

        try {
            $transactionId = \Guysolamour\Cinetpay\Cinetpay::generateTransId();

            $cinetpay = Cinetpay::init()
                ->setTransactionId($transactionId)
                ->setAmount($price)
                ->setCurrency($this->currency)
                ->setDesignation("{$generalSettings['site_name']} payment")
                ->setBuyerIdentifiant($user->id)
                ->setBuyerIdentifiant($user->email);

            session()->put($this->order_session_key, $order->id);

            $data = [
                'cinetpay' => $cinetpay,
            ];

            return view('web.default.cart.channels.cinetpay', $data);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

    }

    private function makeCallbackUrl($status)
    {
        return url("/payments/verify/Cintepay?status=$status");
    }

    public function verify(Request $request)
    {
        $this->handleConfigs();

        $data = $request->all();
        dd($data);

        $user = auth()->user();

        $order = session()->get($this->order_session_key, null);
        session()->forget($this->order_session_key);

        /*$order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!empty($order)) {
            $orderStatus = Order::$fail;
            Auth::loginUsingId($userId);

            if ($response->isSuccess()) {
                $orderStatus = Order::$paying;
            }

            $order->update([
                'status' => $orderStatus,
            ]);
        }

        return $order;*/
    }

}
