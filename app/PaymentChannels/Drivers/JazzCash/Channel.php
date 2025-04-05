<?php

namespace App\PaymentChannels\Drivers\JazzCash;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $merchant_id;
    protected $password;
    protected $integerity_salt;
    protected $endpoint;
    protected $return_url;


    protected array $credentialItems = [
        "merchant_id",
        "password",
        "integerity_salt",
        "endpoint",
    ];


    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->return_url = url("/payments/verify/JazzCash");
        $this->setCredentialItems($paymentChannel);
    }

    private function handleConfigs()
    {
        $mode = $this->test_mode ? 'sandbox' : 'live';

        \Config::set("jazzcash.environment", $mode);
        \Config::set("jazzcash.{$mode}.merchant_id", $this->merchant_id);
        \Config::set("jazzcash.{$mode}.password", $this->password);
        \Config::set("jazzcash.{$mode}.integerity_salt", $this->integerity_salt);
        \Config::set("jazzcash.{$mode}.return_url", $this->return_url);
        \Config::set("jazzcash.{$mode}.endpoint", $this->endpoint);

    }

    /**
     * @throws \Exception
     */
    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        // Send purchase request
        try {

            $data = \AKCybex\JazzCash\Facades\JazzCash::request()
                ->setAmount($this->makeAmountByCurrency($order->total_amount, $this->currency))
                ->toArray();

        } catch (\Exception $exception) {
            dd($exception);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }

        $data['ppmpf_1'] = $order->id;
        $data['ppmpf_2'] = $order->user_id;

        return view('web.default.cart.channels.jazzCash', ['data' => $data]);
    }

    private function makeCallbackUrl($order, $status)
    {
        return url("/payments/verify/JazzCash?status=$status&order_id=$order->id");
    }

    public function verify(Request $request)
    {
        $this->handleConfigs();

        try {

            $orderId = $request->get('ppmpf_1');
            $buyerId = $request->get('ppmpf_2');

            $order = Order::where('id', $orderId)
                ->where('user_id', $buyerId)
                ->first();

            if (!empty($order)) {
                $orderStatus = Order::$fail;

                Auth::loginUsingId($buyerId);

                $jazzcash = \AKCybex\JazzCash\Facades\JazzCash::response();

                if ($jazzcash->code() == 000) {
                    $orderStatus = Order::$paying;
                }

                $order->update([
                    'status' => $orderStatus,
                ]);
            }

            return $order;

        } catch (\Exception $exception) {
            //dd($exception);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
