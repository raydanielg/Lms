<?php

namespace App\PaymentChannels\Drivers\Telebirr;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

trait TelebirrIntegrationTrait
{

    public function applyFabricToken()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-APP-Key' => $this->fabric_app_id,
        ])
            ->post($this->base_url . '/payment/v1/token', [
                'appSecret' => $this->app_secret,
            ]);
        dd($response);

        $client = new Client();

        $response = $client->post($this->base_url . "/payment/v1/token", [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-APP-Key' => $this->fabric_app_id,
            ],
            'json' => [
                'appSecret' => $this->app_secret,
            ]
        ]);
dd($response);
        return json_decode($response->getBody(), true);
    }

    public function createOrder($title, $amount, $returnUrl)
    {

        $applyFabricTokenResult = $this->applyFabricToken();
        $fabricToken = $applyFabricTokenResult['token'];

        $createOrderResult = $this->requestCreateOrder($fabricToken, $title, $amount, $returnUrl);
        $prepayId = $createOrderResult['biz_content']['prepay_id'];

        $rawRequest = $this->createRawRequest($prepayId);

        $url = $this->base_url . $rawRequest . "&version=1.0&trade_type=Checkout";
dd($url);
        return $url;
    }

    private function requestCreateOrder($fabricToken, $title, $amount, $returnUrl)
    {
        $client = new Client();

        $response = $client->post($this->base_url . "/payment/v1/merchant/preOrder", [
            'headers' => [
                'Content-Type' => 'application/json',
                'X-APP-Key' => $this->fabric_app_id,
                'Authorization' => $fabricToken,
            ],
            'json' => [
                'timestamp' => time(),
                'nonce_str' => bin2hex(random_bytes(16)),
                'method' => 'payment.preorder',
                'version' => '1.0',
                'biz_content' => [
                    'notify_url' => $returnUrl,
                    'appid' => $this->merchant_app_id,
                    'merch_code' => $this->merchant_code,
                    'merch_order_id' => $this->order->id,
                    'trade_type' => 'Checkout',
                    'title' => $title,
                    'total_amount' => $amount,
                    'trans_currency' => $this->currency,
                    'timeout_express' => '120m',
                    'business_type' => 'BuyGoods',
                    'redirect_url' => $returnUrl,
                    'callback_info' => 'From web',
                ],

                'sign' => $this->signRequestObject([
                    'timestamp' => time(),
                    'nonce_str' => bin2hex(random_bytes(16)),
                    'method' => 'payment.preorder',
                    'version' => '1.0',
                ]),

                'sign_type' => 'SHA256WithRSA',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    private function createRawRequest($prepayId)
    {
        $map = [
            'appid' => $this->merchant_app_id,
            'merch_code' => $this->merchant_code,
            'nonce_str' => bin2hex(random_bytes(16)),
            'prepay_id' => $prepayId,
            'timestamp' => time(),
            "sign_type" => "SHA256WithRSA"
        ];

        $sign = $this->signRequestObject($map);

        // Order by ASCII in array
        ksort($map);

        return http_build_query($map) . "&sign=" . $sign . "&sign_type=SHA256WithRSA";
    }

    private function signRequestObject($data)
    {
        $exclude_fields = array("sign", "sign_type", "header", "refund_info", "openType", "raw_request");

        ksort($data);
        $stringApplet = '';

        foreach ($data as $key => $values) {

            if (in_array($key, $exclude_fields)) {
                continue;
            }

            if ($key == "biz_content") {
                foreach ($values as $value => $single_value) {
                    if ($stringApplet == '') {
                        $stringApplet = $value . '=' . $single_value;
                    } else {
                        $stringApplet = $stringApplet . '&' . $value . '=' . $single_value;
                    }
                }
            } else {
                if ($stringApplet == '') {
                    $stringApplet = $key . '=' . $values;
                } else {
                    $stringApplet = $stringApplet . '&' . $key . '=' . $values;
                }
            }
        }

        $sortedString = $this->sortedString($stringApplet);

        return $this->signWithPrivateKey($sortedString);
    }

    private function sortedString($stringApplet)
    {
        $stringExplode = '';
        $sortedArray = explode("&", $stringApplet);
        sort($sortedArray);
        foreach ($sortedArray as $x => $x_value) {
            if ($stringExplode == '') {
                $stringExplode = $x_value;
            } else {
                $stringExplode = $stringExplode . '&' . $x_value;
            }
        }

        return $stringExplode;
    }

    private function signWithPrivateKey($data)
    {
        $privateKey = '-----BEGIN PRIVATE KEY-----
        ' . $this->private_key . '
-----END PRIVATE KEY-----'; // dont format. this is correct

        $privateKey = openssl_pkey_get_private($privateKey);

        if (!$privateKey) {
            dd("Error loading PrivateKey");
        }

        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        openssl_free_key($privateKey);

        return base64_encode($signature);
    }

}
