<?php

namespace App\PaymentChannels\Drivers\IPay88;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Support\Facades\Auth;
use IPay88\Requests\RequestBuilder as IPay88RequestBuilder;
use Ipay88\Responses\Response as IPay88Response;
use Illuminate\Http\Request;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $merchant_key;
    protected $merchant_code;


    protected array $credentialItems = [
        'merchant_key',
        'merchant_code',
    ];

    // https://github.com/Kyrax324/laravel-ipay88

    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency(); // MYR
        $this->setCredentialItems($paymentChannel);
    }

    private function handleConfigs()
    {
        \Config::set('iPay88.merchantCode', $this->merchant_code);
        \Config::set('iPay88.merchantKey', $this->merchant_key);
    }


    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $generalSettings = getGeneralSettings();
        $user = $order->user;
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);

        $builder = new IPay88RequestBuilder();
        $builder->setRefNo($order->id);
        $builder->setPaymentID($user->id); // use in verify method
        $builder->setAmount($price);
        $builder->setCurrency($this->currency);
        $builder->setProdDesc($generalSettings['site_name'] . ' payment');
        $builder->setUserName($user->full_name);
        $builder->setUserEmail($user->email);
        $builder->setUserContact($user->mobile);
        $builder->setResponseURL($this->makeCallbackUrl());
        $builder->setBackendURL($this->makeCallbackUrl());

        return $builder->loadPaymentFormView();
    }

    private function makeCallbackUrl()
    {
        $callbackUrl = route('payment_verify', [
            'gateway' => 'Ipay88'
        ]);

        return $callbackUrl;
    }

    public function verify(Request $request)
    {
        $this->handleConfigs();

        $data = $request->all();
        $response = new IPay88Response($data);

        $orderId = $data['RefNo'];
        $userId = $data['PaymentId'];

        $order = Order::where('id', $orderId)
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

        return $order;
    }

}
