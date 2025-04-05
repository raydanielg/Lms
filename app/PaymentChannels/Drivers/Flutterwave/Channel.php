<?php

namespace App\PaymentChannels\Drivers\Flutterwave;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $publicKey;
    protected $secretKey;
    protected $secretHash;

    protected array $credentialItems = [
        'publicKey',
        'secretKey',
        'secretHash',
    ];

    /**
     * Channel constructor.
     * @param PaymentChannel $paymentChannel
     */
    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = currency(); // This Gateway just Support => NGN, GBP or EUR
        $this->setCredentialItems($paymentChannel);
    }

    public function paymentRequest(Order $order)
    {
        $user = $order->user;
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);
        $generalSettings = getGeneralSettings();

        $paymentId = sha1($order->id);

        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $price,
            'email' => $user->email ?? $generalSettings['site_email'],
            'tx_ref' => $paymentId,
            'currency' => $this->currency,
            'redirect_url' => $this->makeCallbackUrl($order),
            'customer' => [
                'email' => $user->email ?? $generalSettings['site_email'],
                "phonenumber" => $user->mobile,
                "name" => $user->full_name
            ],
            "customizations" => [
                "title" => $generalSettings['site_name'] . ' payment',
                "description" => $generalSettings['site_name'] . ' payment'
            ]
        ];

        try {
            $payment = Http::withToken($this->secretKey)->post(
                'https://api.flutterwave.com/v3/payments',
                $data
            )->json();

            if ($payment['status'] == 'success') {
                return $payment['data']['link'];
            }
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }

        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => '',
            'status' => 'error'
        ];

        return redirect()->back()->with(['toast' => $toastData])->withInput();
    }

    private function makeCallbackUrl($order)
    {
        $callbackUrl = route('payment_verify', [
            'gateway' => 'Flutterwave',
            'order_id' => $order->id
        ]);

        return $callbackUrl;
    }

    public function verify(Request $request)
    {
        $order_id = $request->get('order_id');
        $tx_ref = $request->get('tx_ref');
        $user = auth()->user();

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->with('user')
            ->first();

        if (!empty($order) and !empty($tx_ref) and $tx_ref == sha1($order->id)) {
            $payment = Http::withToken($this->secretKey)
                ->get("https://api.flutterwave.com/v3/transactions/?tx_ref=" . $tx_ref)
                ->json();

            if ($payment['status'] == 'success' and isset($payment['data'][0]) and $payment['data'][0]['status'] == 'successful') {
                $order->update([
                    'status' => Order::$paying
                ]);

                return $order;
            }
        }


        if (!empty($order)) {
            $order->update(['status' => Order::$fail]);
        }

        return $order;
    }
}
