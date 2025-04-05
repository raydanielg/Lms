<?php

namespace App\PaymentChannels\Drivers\Paysera;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Omnipay\Omnipay;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $api_key;
    protected $api_secret;
    protected $test_mode;

    protected array $credentialItems = [
        'api_key',
        'api_secret',
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

    public function paymentRequest(Order $order)
    {
        $generalSettings = getGeneralSettings();
        $user = $order->user;

        $gateway = Omnipay::create('Paysera');
        $gateway->setProjectId($this->api_key);
        $gateway->setPassword($this->api_secret);


        // Example card (actually customer) data
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
                'language' => 'ENG',
                'transactionId' => $order->id,
                'paymentMethod' => 'hanzaee',
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

        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => '',
            'status' => 'error'
        ];
        return redirect()->back()->with(['toast' => $toastData])->withInput();
    }

    private function makeCallbackUrl($order, $status)
    {
        return url("/payments/verify/Paysera?status=$status&order_id=$order->id");
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
        $gateway = Omnipay::create('Paysera');
        $gateway->setProjectId($this->api_key);
        $gateway->setPassword($this->api_secret);

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
