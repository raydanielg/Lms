<?php

namespace App\PaymentChannels\Drivers\Sslcommerz;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $store_id;
    protected $store_pass;

    protected array $credentialItems = [
        'store_id',
        'store_pass',
    ];


    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency(); // "BDT"
        $this->setCredentialItems($paymentChannel);
    }

    public function paymentRequest(Order $order)
    {
        $user = $order->user;

        $postData = [];

        $postData['total_amount'] = $this->makeAmountByCurrency($order->total_amount, $this->currency); # You cant not pay less than 10
        $postData['currency'] = $this->currency;
        $postData['tran_id'] = substr(md5($order->id), 0, 10); // tran_id must be unique

        $postData['value_a'] = $postData['tran_id'];
        $postData['value_b'] = $order->id;
        $postData['value_c'] = $order->user_id;

        # CUSTOMER INFORMATION
        $postData['cus_name'] = $user->full_name;
        $postData['cus_add1'] = $user->address;
        $postData['cus_city'] = $user->getRegionByTypeId($user->city_id);
        $postData['cus_postcode'] = 123;
        $postData['cus_country'] = $user->getRegionByTypeId($user->country_id);
        $postData['cus_phone'] = $user->mobile;
        $postData['cus_email'] = $user->email;

        $postData['success_url'] = url("/payments/verify/Sslcommerz");
        $postData['fail_url'] = url("/payments/verify/Sslcommerz");
        $postData['cancel_url'] = url("/payments/verify/Sslcommerz");


        $sslc = new SSLCommerz($this->store_id, $this->store_pass, $this->test_mode);

        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->initiate($postData, false);
        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }

    public function verify(Request $request)
    {
        $status = $request->get('status');
        $orderId = $request->get('value_b');
        $userId = $request->get('value_c');

        if (!empty($userId) and !empty($orderId)) {
            $user = User::query()->findOrFail($userId);

            Auth::loginUsingId($userId);

            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->with('user')
                ->first();

            if (!empty($order)) {
                $orderStatus = Order::$fail;

                if ($status == 'VALID') {
                    $orderStatus = Order::$paying;
                }

                $order->update([
                    'status' => $orderStatus,
                ]);
            }

            return $order;
        }

        return null;
    }
}
