<?php

namespace App\PaymentChannels\Drivers\Payfort;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $command;
    protected $access_code;
    protected $merchant_identifier;
    protected $merchant_reference;
    protected $signature;
    protected $test_mode;

    protected array $credentialItems = [
        'command',
        'access_code',
        'merchant_identifier',
        'merchant_reference',
        'signature',
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

    public function paymentRequest(Order $order)
    {
        $user = $order->user;
        $price = $this->makeAmountByCurrency($order->total_amount, $this->currency);
        $generalSettings = getGeneralSettings();
        $currency = currency();

        $requestParams = array(
            'command' => $this->command,
            'access_code' => $this->access_code,
            'merchant_identifier' => $this->merchant_identifier,
            'merchant_reference' => $this->merchant_reference,
            'amount' => $price,
            'currency' => $currency,
            'language' => 'en',
            'customer_email' => $user->email ?? $generalSettings['site_email'],
            'signature' => $this->signature,
            'order_description' => $generalSettings['site_name'] . ' payment',
        );

        $redirectUrl = 'https://sbcheckout.payfort.com/FortAPI/paymentPage';
        echo "<html xmlns='https://www.w3.org/1999/xhtml'>\n<head></head>\n<body>\n";
        echo "<form action='$redirectUrl' method='post' name='frm'>\n";
        foreach ($requestParams as $a => $b) {
            echo "\t<input type='hidden' name='" . htmlentities($a) . "' value='" . htmlentities($b) . "'>\n";
        }
        echo "\t<script type='text/javascript'>\n";
        echo "\t\tdocument.frm.submit();\n";
        echo "\t</script>\n";
        echo "</form>\n</body>\n</html>";
    }

    private function makeCallbackUrl($order, $status)
    {

    }

    public function verify(Request $request)
    {
        dd(2);
        $order = null;

        if (!empty($order)) {
            $order->update(['status' => Order::$fail]);
        }

        return $order;
    }
}
