<?php

namespace App\PaymentChannels\Drivers\Paytabs;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Paytabscom\Laravel_paytabs\Facades\paypage;


class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $profile_id;
    protected $server_key;
    protected $region;

    protected array $credentialItems = [
        'profile_id',
        'server_key',
        'region' => ['ARE', 'EGY', 'SAU', 'OMN', 'JOR', 'GLOBAL'],
    ];

    // https://github.com/paytabscom/paytabs-php-laravel-package

    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency(); // ['AED','EGP','SAR','OMR','JOD','US']
        //$this->region = "JOR"; //['ARE','EGY','SAU','OMN','JOR','GLOBAL']

        $this->setCredentialItems($paymentChannel);

        if (empty($this->region)) {
            $this->region = "JOR";
        }
    }

    private function handleConfigs()
    {
        \Config::set('paytabs.profile_id', $this->profile_id);
        \Config::set('paytabs.server_key', $this->server_key);
        \Config::set('paytabs.currency', $this->currency);
        \Config::set('paytabs.region', $this->region);
        \Config::set('paytabs.callback', $this->makeCallbackUrl());
    }


    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $generalSettings = getGeneralSettings();
        $user = $order->user;
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);
        $userDefined = [
            'id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'mobile' => $user->mobile,
        ];

        try {
            $pay = paypage::sendPaymentCode('all')
                ->sendTransaction('sale', 'ecom')
                ->sendCart($order->id, $price, $generalSettings['site_name'] . ' payment')
                ->sendCustomerDetails($user->full_name, $user->email, $user->mobile, '', '', '', '', '', '')
                ->sendUserDefined($userDefined)
                ->sendShippingDetails($generalSettings['site_name'], $generalSettings['site_email'] ?? '', $generalSettings['site_phone'] ?? '', '', '', '', '', '', '')
                ->sendURLs($this->makeCallbackUrl(), $this->makeCallbackUrl())
                ->sendLanguage('en')
                ->create_pay_page();

            return $pay;
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

        $order = null;

        if (!empty($data) and !empty($data['tranRef'])) {
            $transaction = Paypage::queryTransaction($data['tranRef']);

            if (!empty($transaction)) {
                $userDefined = json_decode($transaction->user_defined->udf1);

                if (!empty($userDefined)) {
                    $userId = $userDefined->id;
                    $orderId = $transaction->cart_id;

                    $order = Order::where('id', $orderId)
                        ->where('user_id', $userId)
                        ->first();

                    if (!empty($order)) {
                        Auth::loginUsingId($userId);

                        $orderStatus = Order::$fail;

                        if ($transaction->success) {
                            $orderStatus = Order::$paying;
                        }

                        $order->update([
                            'status' => $orderStatus,
                            'payment_data' => json_encode($transaction),
                        ]);
                    }
                }
            }
        }

        return $order;
    }

}
