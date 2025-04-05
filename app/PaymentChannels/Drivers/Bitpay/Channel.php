<?php

namespace App\PaymentChannels\Drivers\Bitpay;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Vrajroham\LaravelBitpay\LaravelBitpay;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;

    protected $private_key_path;
    protected $public_key_path;
    protected $key_storage_password;
    protected $token;

    protected array $credentialItems = [
        'private_key_path',
        'public_key_path',
        'key_storage_password',
        'token',
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

    private function handleConfigs()
    {
        \Config::set('laravel-bitpay.private_key', $this->private_key_path);
        \Config::set('laravel-bitpay.public_key', $this->public_key_path);
        \Config::set('laravel-bitpay.network', $this->test_mode ? 'testnet' : 'livenet');
        \Config::set('laravel-bitpay.key_storage_password', $this->key_storage_password);
        \Config::set('laravel-bitpay.token', $this->token);
    }


    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $generalSettings = getGeneralSettings();

        $user = $order->user;
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);


        $invoice = LaravelBitpay::Invoice();

        $invoice->setItemDesc($generalSettings['site_name'] . ' payment');
        $invoice->setItemCode($order->id);
        $invoice->setPrice($price);
        $invoice->setOrderId($order->id);

        // Create Buyer Instance
        $buyer = LaravelBitpay::Buyer();
        $buyer->setName($user->full_name);
        $buyer->setEmail($user->email ?? $generalSettings['site_email']);
        $buyer->setAddress1('no Address');
        $buyer->setNotify(true);

        $invoice->setBuyer($buyer);

        // Set currency
        $invoice->setCurrency($this->currency);

        $invoice->setRedirectURL($this->makeCallbackUrl());

        // Create invoice on bitpay server.
        $invoice = LaravelBitpay::createInvoice($invoice);

        $invoiceId = $invoice->getId();

        $order->update([
            'reference_id' => $invoiceId
        ]);

        return $invoice->getUrl();
    }

    private function makeCallbackUrl()
    {
        $callbackUrl = route('payment_verify', [
            'gateway' => 'Bitpay'
        ]);

        return $callbackUrl;
    }

    public function verify(Request $request)
    {
        $this->handleConfigs();

        $data = $request->all();
        $order_id = $data['order_id'];

        $user = auth()->user();

        $order = Order::where('id', $order_id)
            ->where('user_id', $user->id)
            ->with('user')
            ->first();


        if (!empty($order)) {
            $order->update(['status' => Order::$fail]);
        }

        return $order;
    }
}
