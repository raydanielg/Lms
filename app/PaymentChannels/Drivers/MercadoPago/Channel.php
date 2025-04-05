<?php

namespace App\PaymentChannels\Drivers\MercadoPago;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $order_session_key;
    protected $test_mode;
    protected $access_token;

    protected array $credentialItems = [
        'access_token',
    ];

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency(); // BRL
        $this->order_session_key = 'mercado.payments.order_id';
        $this->setCredentialItems($paymentChannel);
    }

    public function paymentRequest(Order $order)
    {
        //$generalSettings = getGeneralSettings();
        $user = $order->user;

        try {
            $payer = [
                'first_name' => $user->full_name,
                'last_name' => null,
                'email' => $user->email,
            ];

            $items = [];
            foreach ($order->orderItems as $orderItem) {
                $items[] = [
                    'title' => "item " . $orderItem->id,
                    'quantity' => 1,
                    'unit_price' => $this->makeAmountByCurrency($orderItem->total_amount, $this->currency),
                ];
            }

            $preferenceData = [
                'payer' => $payer,
                'items' => $items,
                'back_urls' => [
                    'success' => $this->makeCallbackUrl("success"),
                    'pending' => $this->makeCallbackUrl("pending"),
                    'failure' => $this->makeCallbackUrl("failure"),
                ],
            ];

            // Convert data to JSON
            $createPaymentUrl = 'https://api.mercadopago.com/checkout/preferences';

            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->access_token,
            ];

            $response = Http::withHeaders($headers)
                ->post($createPaymentUrl, $preferenceData);

            $preference = $response->json();

            session()->put($this->order_session_key, $order->id);

            return $preference['init_point'];
        } catch (\Exception $exception) {
            // dd($exception);
        }

        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => '',
            'status' => 'error'
        ];

        return redirect()->back()->with(['toast' => $toastData])->withInput();
    }

    private function makeCallbackUrl($status)
    {
        return url("/payments/verify/MercadoPago");
    }

    public function verify(Request $request)
    {
        $data = $request->all();
        $paymentId = $data['payment_id'];

        $user = auth()->user();

        $order_id = session()->get($this->order_session_key, null);
        session()->forget($this->order_session_key);

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!empty($order)) {
            $orderStatus = Order::$fail;

            try {
                $apiUrl = "https://api.mercadopago.com/v1/payments/$paymentId?access_token={$this->access_token}";

                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                $paymentData = json_decode($response, true);

                // Close cURL session
                curl_close($ch);

                // Check payment status
                if ($paymentData and isset($paymentData['status'])) {
                    $status = $paymentData['status'];

                    // Process payment status
                    if ($status === 'approved') {
                        $orderStatus = Order::$paying;
                    }
                }

            } catch (\Exception $exception) {
                // dd($exception);
            }


            $order->update([
                'status' => $orderStatus,
            ]);
        }


        return $order;
    }
}
