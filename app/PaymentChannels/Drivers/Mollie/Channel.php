<?php

namespace App\PaymentChannels\Drivers\Mollie;

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
    protected $test_mode;

    protected array $credentialItems = [
        'api_key',
    ];

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->setCredentialItems($paymentChannel);
        $this->order_session_key = 'mollie.payments.order_id';

        // $this->api_key = "test_guGAKGxPk8yr28dWMEM23RFvAB4h6M";
    }

    protected function makeGateway()
    {
        $gateway = Omnipay::create('Mollie');

        $gateway->setApiKey($this->api_key);

        return $gateway;
    }

    /**
     * @throws \Exception
     */
    public function paymentRequest(Order $order)
    {
        // Send purchase request
        try {
            session()->put($this->order_session_key, $order->id);

            $gateway = $this->makeGateway();
            $response = $gateway->purchase($this->createPaymentData($order))->send();
            dd($response);
            if ($response->isRedirect()) {
                return $response->redirect();
            }

        } catch (\Exception $exception) {
            dd($exception);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
        dd(1);
        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => '',
            'status' => 'error'
        ];
        return redirect()->back()->with(['toast' => $toastData])->withInput();
    }

    private function createPaymentData($order)
    {
        $generalSettings = getGeneralSettings();
        $user = $order->user;

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

        $lines = [];

        foreach ($order->orderItems as $orderItem) {
            $lines[] = [
                'type' => 'physical',
                'sku' => "order-{$orderItem->id}",
                'name' => "item {$orderItem->id}",
                'productUrl' => '',
                'imageUrl' => '',
                'quantity' => 1,
                'vatRate' => $orderItem->tax ?? 1,
                'unitPrice' => $orderItem->amount,
                'totalAmount' => $orderItem->total_amount,
                'discountAmount' => $orderItem->discount,
                'vatAmount' => $orderItem->tax_price,
            ];
        }

        return [
            "amount" => $this->makeAmountByCurrency($order->total_amount, $this->currency),
            "currency" => $this->currency,
            'orderNumber' => $order->id,
            "description" => "Pay Cart Items",
            'paymentMethod' => 'klarnapaylater',
            "returnUrl" => $this->makeCallbackUrl($order, 'return'),
            'card' => $card,
            'locale' => 'nl_NL',
            'lines' => $lines,
        ];
    }

    private function makeCallbackUrl($order, $status)
    {
        return url("/payments/verify/Mollie?status=$status&order_id=$order->id");
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
        try {
            $gateway = $this->makeGateway();
            $response = $gateway->purchase($this->createPaymentData($order))->send();

        } catch (\Exception $exception) {
            //dd($exception);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }

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
