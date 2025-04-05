<?php

namespace App\PaymentChannels\Drivers\MercadoPago;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Omnipay\Omnipay;

class Channel1 extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $public_key;
    protected $access_token;
    protected $client_id;
    protected $client_secret;
    protected $order_session_key;
    protected $test_mode;

    protected array $credentialItems = [
        'public_key',
        'access_token',
        'client_id',
        'client_secret',
    ];

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->order_session_key = 'mercado.payments.order_id';
        $this->setCredentialItems($paymentChannel);
    }

    protected function makeGateway()
    {
        $gateway = Omnipay::create('MercadoPago');

        $gateway->setClientId($this->client_id);
        $gateway->setClientSecret($this->client_secret);
        $gateway->setAccessToken($this->access_token);

        return $gateway;
    }


    public function paymentRequest(Order $order)
    {
        $generalSettings = getGeneralSettings();
        $user = $order->user;

        try {

            $gateway = $this->makeGateway();

            $card = [
                'email' => $user->email ?? $generalSettings['site_email'],
                'billingFirstName' => $user->full_name,
                'billingLastName' => '',
                'billingPhone' => $user->mobile,
                'billingCompany' => $generalSettings['site_name'],
                'billingAddress1' => '',
                'billingCity' => '',
                'billingPostcode' => '',
                'billingCountry' => '',
            ];

            // Send purchase request
            $response = $gateway->purchase(
                [
                    'transactionId' => $order->id,
                    'amount' => $this->makeAmountByCurrency($order->total_amount, $this->currency),
                    'currency' => $this->currency,
                    'testMode' => $this->test_mode,
                    'returnUrl' => $this->makeCallbackUrl($order, 'success'),
                    'cancelUrl' => $this->makeCallbackUrl($order, 'cancel'),
                    'notifyUrl' => $this->makeCallbackUrl($order, 'notify'),
                    'card' => $card,
                ]
            )->send();

            if ($response->isRedirect()) {
                return $response->redirect();
            }

        } catch (\Exception $exception) {
//            dd($exception);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }


        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => '',
            'status' => 'error'
        ];

        return redirect()->back()->with(['toast' => $toastData])->withInput();
    }

    private function makeCallbackUrl($order, $status)
    {
        return url("/payments/verify/MercadoPago?status=$status&order_id=$order->id");
    }

    public function verify(Request $request)
    {
        $data = $request->all();
        $order_id = $data['order_id'];

        $user = auth()->user();

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->first();

        // Setup payment gateway
        $gateway = $this->makeGateway();

        // Accept the notification
        $response = $gateway->acceptNotification()->send();

        if ($response->isSuccessful() and !empty($order)) {
            // Mark the order as paid

            $order->update([
                'status' => Order::$paying
            ]);

            return $order;
        }

        if (!empty($order)) {
            $order->update([
                'status' => Order::$fail
            ]);
        }

        return $order;
    }
}
