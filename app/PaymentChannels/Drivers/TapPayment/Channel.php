<?php

namespace App\PaymentChannels\Drivers\TapPayment;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Essam\TapPayment\Payment;
use Illuminate\Http\Request;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $secret_api_Key;
    protected $merchant_id;
    protected $order_session_key;

    public $show_test_mode_toggle = false;

    protected array $credentialItems = [
        'secret_api_Key',
        'merchant_id',
    ];

    /**
     * https://github.com/sfwanessam/laravel-tap-payment
     *
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->order_session_key = 'tap.payments.order_id';
        $this->setCredentialItems($paymentChannel);
    }

    private function getTapPay()
    {
        return (new Payment(['secret_api_Key' => $this->secret_api_Key]));
    }

    public function paymentRequest(Order $order)
    {
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);
        $generalSettings = getGeneralSettings();
        $currency = currency();
        $user = auth()->user();


        try {

            $tapPay = $this->getTapPay();

            $response = $tapPay->charge([
                'amount' => $price,
                'currency' => $currency,
                'threeDSecure' => 'true',
                'description' => $generalSettings['site_name'] . ' payment',
                'statement_descriptor' => 'sample',
                'customer' => [
                    'first_name' => $user->full_name,
                    'email' => $user->email,
                ],
                'source' => [
                    'id' => 'src_card'
                ],
                'post' => [
                    'url' => null
                ],
                'merchant' => [
                    'id' => $this->merchant_id  //Include this when you are going to live
                ],
                'redirect' => [
                    'url' => $this->makeCallbackUrl()
                ]
            ]);

            session()->put($this->order_session_key, $order->id);

            return $response->getTargetUrl();
        } catch (\Exception $exception) {
            dd($exception);
        }

    }

    private function makeCallbackUrl()
    {
        return url("/payments/verify/TapPayment");
    }

    public function verify(Request $request)
    {
        $user = auth()->user();
        $tapId = $request->get("tap_id");
        $order_id = session()->get($this->order_session_key, null);
        session()->forget($this->order_session_key);

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!empty($order)) {
            $tapPay = $this->getTapPay();
            $charge = $tapPay->getCharge($tapId);

            $orderStatus = Order::$fail; //  status == "DECLINED"

            //dd($charge);
            if ($charge != null && isset($charge->object) && $charge->object == "charge" && isset($charge->status) && $charge->status == "CAPTURED") {
                $orderStatus = Order::$paying;
            }

            $order->update([
                'status' => $orderStatus,
            ]);
        }

        return $order;
    }
}
