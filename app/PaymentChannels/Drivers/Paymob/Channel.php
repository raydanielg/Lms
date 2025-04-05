<?php

namespace App\PaymentChannels\Drivers\Paymob;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Paytabscom\Laravel_paytabs\Facades\paypage;


class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $order_session_key;
    protected $api_key;
    protected $integration_id;
    protected $identifier;


    protected array $credentialItems = [
        'api_key',
        'integration_id',
        'identifier',
    ];

    // https://github.com/ctf0/laravel-paymob

    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->setCredentialItems($paymentChannel);
        $this->order_session_key = 'paymob.payments.order_id';
    }

    private function handleConfigs()
    {

    }


    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $generalSettings = getGeneralSettings();
        $user = $order->user;

        try {
            $token = $this->getToken();
            $paymobOrder = $this->createPaymobOrder($order, $token);
            $paymentToken = $this->getPaymentToken($order, $paymobOrder, $token);

            $response = Http::post('https://accept.paymobsolutions.com/api/acceptance/payments/pay', [
                'auth_token' => $token,
                'source' => [
                    'identifier' => $this->identifier,
                    'subtype' => 'WALLET'
                ],
                'payment_token' => $paymentToken
            ]);

            dd($response);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

    }

    private function makeCallbackUrl()
    {
        $callbackUrl = route('payment_verify', [
            'gateway' => 'Paytabs'
        ]);

        return $callbackUrl;
    }

    public function verify(Request $request)
    {
        $this->handleConfigs();

        $data = $request->all();
        dd($data);

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

    private function getPrice($order)
    {
        return $this->makeAmountByCurrency($order->total_amount, $this->currency);
    }

    public function getToken() {
        $response = Http::post('https://accept.paymobsolutions.com/api/auth/tokens', [
            'api_key' => $this->api_key
        ]);
        return $response->object()->token;
    }

    private function createPaymobOrder($order, $token)
    {
        $items = [];

        foreach ($order->orderItems as $orderItem) {
            $items[] = [
                'title' => 'Order ' . $orderItem->id,
            ];
        }

        $data = [
            "auth_token" => $token,
            "merchant_order_id" => $order->id,
            "delivery_needed" => "false",
            "amount_cents" => $this->getPrice($order),
            "currency" => $this->currency,
            "items" => $items,
        ];

        $response = Http::post('https://accept.paymobsolutions.com/api/ecommerce/orders', $data);

        return $response->object();
    }

    private function getPaymentToken($order, $paymobOrder, $token)
    {
        $user = $order->user;
        $name = explode(' ', $user->full_name);
        $nameCount = count($name);
        $billingData = [
            "first_name" => $nameCount ? $name[0] : '',
            "last_name" => ($nameCount >= 2) ? $name[$nameCount - 1] : '',
            "email" => $user->email,
            "phone_number" => $user->mobile,
            "apartment" => "NA",
            "floor" => "NA",
            "street" => "NA",
            "building" => "NA",
            "shipping_method" => "NA",
            "postal_code" => "NA",
            "city" => "NA",
            "country" => "NA",
            "state" => "NA"
        ];

        $data = [
            "auth_token" => $token,
            "amount_cents" => $this->getPrice($order),
            "expiration" => 3600,
            "order_id" => $paymobOrder->id,
            "billing_data" => $billingData,
            "currency" => $this->currency,
            "integration_id" => $this->integration_id,
        ];

        $response = Http::post('https://accept.paymobsolutions.com/api/acceptance/payment_keys', $data);

        return $response->object()->token;
    }

}
