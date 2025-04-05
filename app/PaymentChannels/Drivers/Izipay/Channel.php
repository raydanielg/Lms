<?php

namespace App\PaymentChannels\Drivers\Izipay;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use Illuminate\Http\Request;
use Lyra\Client as LyraClient;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $test_mode;
    protected $client;
    protected $publicKey;
    protected $username;
    protected $password;
    protected $endpoint;
    protected $SHA256Key;

    protected array $credentialItems = [
        'username',
        'password',
        'endpoint',
        'publicKey',
        'SHA256Key',
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

    private function handleClient()
    {
        $clientEndpoint = url('');

        $client = new LyraClient();
        $client->setUsername($this->username);
        $client->setPassword($this->password);
        $client->setEndpoint($this->endpoint);
        $client->setClientEndpoint($clientEndpoint);
        $client->setPublicKey($this->publicKey);
        $client->setSHA256Key($this->SHA256Key);

        $this->client = $client;
    }

    /**
     * @throws \Exception
     */
    public function paymentRequest(Order $order)
    {
        $this->handleClient();

        // Send purchase request
        try {
            $store = [
                "amount" => (int)($this->makeAmountByCurrency($order->total_amount, $this->currency) * 100), // https://docs.lyra.com/en/rest/V4.0/api/playground/Charge/CreatePayment#amount
                "currency" => $this->currency, // worked by this currency => PEN
                "orderId" => $order->id,
                'name' => $order->user->full_name,
                "customer" => [
                    "reference" => $order->user->id,
                    "email" => $order->user->email,
                ]
            ];

            $response = $this->client->post("V4/Charge/CreatePayment", $store);

        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }

        if ($response['status'] != 'SUCCESS') {
            throw new \Exception(trans('cart.gateway_error'));
        }

        $formToken = $response["answer"]["formToken"];

        $data = [
            'publicKey' => $this->publicKey,
            'formToken' => $formToken,
            'successUrl' => $this->makeCallbackUrl($order, 'success'),
        ];

        return view('web.default.cart.channels.izipay', $data);
    }

    private function makeCallbackUrl($order, $status)
    {
        return url("/payments/verify/Izipay?status=$status&order_id=$order->id");
    }

    public function verify(Request $request)
    {
        $this->handleClient();

        try {
            if (!$this->client->checkHash()) {
                //something wrong, probably a fraud ....
                throw new \Exception('invalid signature');
            }

            $rawAnswer = $this->client->getParsedFormAnswer();
            $formAnswer = $rawAnswer['kr-answer'];

            /* Retrieve the transaction id from the IPN data */
            $transaction = $formAnswer['transactions'][0];

            /* get some parameters from the answer */
            $orderStatus = $formAnswer['orderStatus'];
            $orderId = $formAnswer['orderDetails']['orderId'];
            $transactionUuid = $transaction['uuid'];

            $user = auth()->user();

            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->first();

            if (!empty($order)) {
                $status = Order::$fail;

                if ($orderStatus == 'PAID') {
                    $status = Order::$paying;
                }

                $order->update([
                    'status' => $status
                ]);
            }

            return $order;

        } catch (\Exception $exception) {
            //dd($exception);
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
