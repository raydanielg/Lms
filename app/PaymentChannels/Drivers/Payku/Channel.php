<?php

namespace App\PaymentChannels\Drivers\Payku;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Instamojo\Instamojo;
use SebaCarrasco93\LaravelPayku\Facades\LaravelPayku;
use SebaCarrasco93\LaravelPayku\Models\PaykuTransaction;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $order_session_key;
    protected $test_mode;
    protected $base_url;
    protected $public_token;
    protected $private_token;
    protected $finish_route_name;


    protected array $credentialItems = [
        'base_url',
        'public_token',
        'private_token',
    ];

    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency();
        $this->order_session_key = 'payku.payments.order_id';
        $this->finish_route_name = "payku.result";

        $this->setCredentialItems($paymentChannel);
    }


    private function handleConfigs()
    {
        \Config::set('laravel-payku.base_url', $this->base_url);
        \Config::set('laravel-payku.public_token', $this->public_token);
        \Config::set('laravel-payku.private_token', $this->private_token);
        \Config::set('laravel-payku.route_finish_name', $this->finish_route_name);
    }

    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $user = $order->user;
        session()->put($this->order_session_key, $order->id);

        $data = [
            'order' => rand(0000000, 11111111) . date('is'),
            'subject' => 'Order Payment',
            'amount' => $this->makeAmountByCurrency($order->total_amount, $this->currency),
            'email' => $user->email
        ];

        return LaravelPayku::create($data['order'], $data['subject'], $data['amount'], $data['email']);
    }

    private function makeCallbackUrl($status)
    {
        return url("/payments/verify/Payku?status=$status");
    }

    public function verify(Request $request)
    {
        $this->handleConfigs();

        $data = $request->all();

        try {
            $order_id = session()->get($this->order_session_key, null);
            session()->forget($this->order_session_key);

            $user = auth()->user();

            $order = Order::where('id', $order_id)
                ->where('user_id', $user->id)
                ->first();

            if (!empty($order)) {
                $orderStatus = Order::$fail;

                if (!empty($data['transaction_id'])) {
                    $paykuTransaction = PaykuTransaction::find($data['transaction_id']);

                    if (!empty($paykuTransaction) and $paykuTransaction->status == 'success') {
                        $orderStatus = Order::$paying;
                    }
                }

                $order->update([
                    'status' => $orderStatus,
                ]);
            }

            return $order;
        } catch (\Exception $e) {
            print('Error: ' . $e->getMessage());
        }
    }
}
