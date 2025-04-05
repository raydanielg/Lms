<?php

namespace App\PaymentChannels\Drivers\Xendit;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\InvoiceItem;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $order_session_key;
    protected $currency;
    protected $test_mode;
    protected $api_key;

    protected array $credentialItems = [
        'api_key',
    ];

    // https://github.com/xendit/xendit-php

    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->order_session_key = 'xendit.payments.order_id';
        $this->currency = currency(); // 'IDR'
        $this->setCredentialItems($paymentChannel);
    }

    private function handleConfigs()
    {
        Configuration::setXenditKey($this->api_key);
    }


    public function paymentRequest(Order $order)
    {
        $this->handleConfigs();

        $generalSettings = getGeneralSettings();
        $user = $order->user;
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);

        $items = [];
        foreach ($order->orderItems as $orderItem) {
            $items[] = (new InvoiceItem([
                'name' => "order_item_{$orderItem->id}",
                'price' => $orderItem->total_amount,
                'quantity' => 1,
            ]));
        }


        try {
            $apiInstance = new InvoiceApi();

            $createInvoiceRequest = new CreateInvoiceRequest([
                'amount' => $price,
                'currency' => $this->currency,
                'external_id' => "order_{$order->id}",
                'description' => $generalSettings['site_name'] . ' payment',
                'payer_email' => $user->email,
                'items' => $items,
                'success_redirect_url' => $this->makeCallbackUrl(),
            ]);

            $forUserId = null; // string | Business ID of the sub-account merchant (XP feature)

            $result = $apiInstance->createInvoice($createInvoiceRequest, $forUserId);

            session()->put($this->order_session_key, $result['id']);

            return $result['invoice_url'];
        } catch (\Throwable $e) {
            dd($e->getMessage());
        }

        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => '',
            'status' => 'error'
        ];

        return redirect()->back()->with(['toast' => $toastData])->withInput();
    }

    private function makeCallbackUrl()
    {
        $callbackUrl = route('payment_verify', [
            'gateway' => 'Xendit'
        ]);

        return $callbackUrl;
    }

    public function verify(Request $request)
    {
        $user = auth()->user();

        try {
            $this->handleConfigs();
            $apiInstance = new InvoiceApi();

            $invoiceId = session()->get($this->order_session_key, null);
            session()->forget($this->order_session_key);

            $result = $apiInstance->getInvoiceById($invoiceId, null);

            if ($result) {
                $orderId = str_replace("order_", "", $result['external_id']);

                $order = Order::where('id', $orderId)
                    ->where('user_id', $user->id)
                    ->first();

                if (!empty($order)) {
                    $orderStatus = Order::$fail;

                    if (in_array($result['status'], ["SETTLED", "PAID"])) {
                        $orderStatus = Order::$paying;
                    }

                    $order->update([
                        'status' => $orderStatus,
                    ]);

                    return $order;
                }
            }
        } catch (\Throwable $e) {
            //dd($e->getMessage());
        }

        return null;
    }

}
