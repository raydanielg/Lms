<?php

namespace App\PaymentChannels\Drivers\Instamojo;

use App\Models\Order;
use App\Models\PaymentChannel;
use App\PaymentChannels\BasePaymentChannel;
use App\PaymentChannels\IChannel;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Instamojo\Instamojo;

class Channel extends BasePaymentChannel implements IChannel
{
    protected $currency;
    protected $order_session_key;

    protected $test_mode;
    protected $client_id;
    protected $client_secret;
    protected $authType;
    protected $username;
    protected $password;

    protected array $credentialItems = [
        'client_id',
        'client_secret',
        'authType',
        'username',
        'password',
    ];


    public function __construct(PaymentChannel $paymentChannel)
    {
        $this->currency = "INR";//currency();
        $this->order_session_key = 'instamojo.payments.order_id';
        $this->setCredentialItems($paymentChannel);
    }

    private function handleAccessToken()
    {
        // https://docs.instamojo.com/reference/generate-access-token-application-based-authentication

        try {
            $client = new Client();

            $response = $client->post('https://api.instamojo.com/oauth2/token/', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret
                ]
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);
            
        } catch (\Exception $e) {
            dd($e);
        }

        dd($data);
    }

    private function makeApi()
    {
        return Instamojo::init($this->authType, [
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "username" => $this->username, /** In case of user based authentication**/
            "password" => $this->password/** In case of user based authentication**/
        ], $this->test_mode);
    }

    public function paymentRequest(Order $order)
    {
        $user = $order->user;

        try {
            $this->handleAccessToken();

            $api = $this->makeApi();

            $response = $api->createPaymentRequest([
                "purpose" => 'order payment',
                "amount" => $this->makeAmountByCurrency($order->total_amount, $this->currency),
                "send_email" => false,
                "email" => $user->email,
                "phone" => $user->mobile,
                "buyer_name" => $user->full_name,
                "redirect_url" => $this->makeCallbackUrl()
            ]);

            session()->put($this->order_session_key, $order->id);
            dd($response);
            return $response['longurl'];
        } catch (\Exception $e) {
            dd($e);
        }
    }

    private function makeCallbackUrl()
    {
        return url("/payments/verify/Instamojo");
    }

    public function verify(Request $request)
    {
        $data = $request->all();

        try {
            $order_id = session()->get($this->order_session_key, null);
            session()->forget($this->order_session_key);

            $user = auth()->user();

            $order = Order::where('id', $order_id)
                ->where('user_id', $user->id)
                ->first();

            if (!empty($order)) {
                $api = $this->makeApi();

                $response = $api->getPaymentRequestDetails($data['payment_request_id'] ?? null);

                $orderStatus = Order::$fail;

                if (!empty($response) and !empty($response['status']) and $response['status'] == "Completed") {
                    $orderStatus = Order::$paying;
                }

                $order->update([
                    'status' => $orderStatus,
                ]);
            }

            return $order;
        } catch (\Exception $e) {
            dd($e, 1);
            print('Error: ' . $e->getMessage());
        }
    }
}
