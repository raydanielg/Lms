<?php

namespace App\Mixins\Notifications;

use Craftsys\Msg91\Facade\Msg91;
use Illuminate\Support\Facades\Http;
use Kavenegar;
use Msegat;
use Twilio\Rest\Client;
use Vonage\Laravel\Facade\Vonage;

class SendSMS
{
    protected $to;
    protected $content;

    const TWILIO = 'twilio';
    const MSEGAT = 'msegat';
    const VONAGE = 'vonage';
    const MSG91 = 'msg91';
    const TWO_FACTOR = '2factor';


    const allChannels = [
        self::TWILIO,
        self::MSEGAT,
        self::VONAGE,
        self::MSG91,
        self::TWO_FACTOR,
    ];

    public function __construct($to, $content)
    {
        $this->to = $to;
        $this->content = $content;
    }

    public function send()
    {
        $smsSendingChannel = getSMSChannelsSettings("sms_sending_channel");

        if (!empty($smsSendingChannel)) {
            if ($smsSendingChannel == self::TWILIO) {
                $this->sendByTwilio();
            } else if ($smsSendingChannel == self::MSEGAT) {
                $this->sendByMsegat();
            } else if ($smsSendingChannel == self::VONAGE) {
                $this->sendByVonage();
            } else if ($smsSendingChannel == self::MSG91) {
                $this->sendByMsg91();
            } else if ($smsSendingChannel == self::TWO_FACTOR) {
                $this->sendByTwoFactor();
            }
        }

        return false;
    }


    /**
     *
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    private function sendByTwilio()
    {
        $settings = getSMSChannelsSettings();

        $account_sid = !empty($settings['twilio_sid']) ? $settings['twilio_sid'] : '';
        $auth_token = !empty($settings['twilio_auth_token']) ? $settings['twilio_auth_token'] : '';
        $twilio_number = !empty($settings['twilio_number']) ? $settings['twilio_number'] : '';

        $twilio = new Client($account_sid, $auth_token);


        $twilio->messages->create($this->to,
            [
                'from' => $twilio_number,
                'body' => $this->content
            ]
        );

    }


    private function sendByKavenegar()
    {
        // https://github.com/KaveNegar/kavenegar-laravel

        $settings = getSMSChannelsSettings();
        $kavenegarUrl = !empty($settings['kavenegar_url']) ? $settings['kavenegar_url'] : null;
        $kavenegarApiKey = !empty($settings['kavenegar_api_key']) ? $settings['kavenegar_api_key'] : null;
        $kavenegarNumber = !empty($settings['kavenegar_number']) ? $settings['kavenegar_number'] : null;

        if (!empty($kavenegarUrl) and !empty($kavenegarApiKey) and !empty($kavenegarNumber)) {
            \config()->set('kavenegar.apikey', $kavenegarApiKey);

            try {
                $result = Kavenegar::Send($kavenegarNumber, $this->to, $this->content);
            } catch (\Exception $e) {
                dd($e);
            }

        }

    }

    private function sendByMsegat()
    {
        // https://github.com/Moemen-Gaballah/laravel-msegat

        $settings = getSMSChannelsSettings();

        $username = !empty($settings['msegat_username']) ? $settings['msegat_username'] : null;
        $user_sender = !empty($settings['msegat_user_sender']) ? $settings['msegat_user_sender'] : null;
        $api_key = !empty($settings['msegat_api_key']) ? $settings['msegat_api_key'] : null;

        if (!empty($username) and !empty($user_sender) and !empty($api_key)) {
            \config()->set('msegat.MSEGAT_USERNAME', $username);
            \config()->set('msegat.MSEGAT_USER_SENDER', $user_sender);
            \config()->set('msegat.MSEGAT_API_KEY', $api_key);


            try {
                $msg = Msegat::sendMessage($this->to, $this->content);

            } catch (\Exception $e) {
                dd($e);
            }

        }

    }

    private function sendByVonage()
    {
        // https://github.com/Vonage/vonage-laravel

        $settings = getSMSChannelsSettings();

        $number = !empty($settings['vonage_number']) ? $settings['vonage_number'] : null;
        $key = !empty($settings['vonage_key']) ? $settings['vonage_key'] : null;
        $secret = !empty($settings['vonage_secret']) ? $settings['vonage_secret'] : null;
        $application_id = !empty($settings['vonage_application_id']) ? $settings['vonage_application_id'] : null;
        $private_key = !empty($settings['vonage_private_key']) ? $settings['vonage_private_key'] : null;

        if (!empty($key) and !empty($secret)) {
            \config()->set('vonage.api_key', $key);
            \config()->set('vonage.api_secret', $secret);
            \config()->set('vonage.private_key', $private_key);
            \config()->set('vonage.application_id', $application_id);

            try {
                $text = new \Vonage\SMS\Message\SMS($this->to, $number, $this->content);
                Vonage::sms()->send($text);
            } catch (\Exception $e) {
                dd($e);
            }

        }

    }

    private function sendByMsg91()
    {
        // https://github.com/craftsys/msg91-laravel

        $settings = getSMSChannelsSettings();

        $key = !empty($settings['msg91_key']) ? $settings['msg91_key'] : null;
        $flowId = !empty($settings['msg91_flow_id']) ? $settings['msg91_flow_id'] : null;

        if (!empty($key) and !empty($flowId)) {
            \config()->set('services.msg91.key', $key);

            try {

                $res = Msg91::sms()
                    ->to($this->to)
                    ->flow($flowId)
                    ->content($this->content)
                    ->send();

                dd($res);
            } catch (\Exception $e) {
                dd($e);
            }

        }

    }

    private function sendByTwoFactor()
    {
        // https://documenter.getpostman.com/view/301893/TWDamFGh#726383fc-97cb-4355-b6c3-4041adbf0ddd

        $settings = getSMSChannelsSettings();

        $api_key = !empty($settings['2factor_api_key']) ? $settings['2factor_api_key'] : null;

        if (!empty($api_key)) {

            try {
                /*$response = Http::post('https://2factor.in/API/R1/', [
                    'module' => 'TRANS_SMS',
                    'apikey' => $api_key,
                    'to' => "{$this->to}",
                    'from' => 'HEADER',
                    'msg' => "{$this->content}",
                ]);*/

                $url = "https://2factor.in/API/V1/{$api_key}/SMS/{$this->to}/{$this->content}/OTP1";

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                ])->get($url);


                if ($response->successful()) {

                } else {
                    dd($response,$response->body());
                }
            } catch (\Exception $e) {
                dd($e);
            }

        }

    }

}
