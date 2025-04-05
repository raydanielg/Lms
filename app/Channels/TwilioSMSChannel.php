<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioSMSChannel
{
    /**
     * @param $message
     * @param $recipients
     * @return \Twilio\Rest\Api\V2010\Account\MessageInstance
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTwilioSMS($notifiable);

        $settings = getFeaturesSettings();


        $account_sid = !empty($settings['twilio_sid']) ? $settings['twilio_sid'] : '';
        $auth_token = !empty($settings['twilio_auth_token']) ? $settings['twilio_auth_token'] : '';
        $twilio_number = !empty($settings['twilio_number']) ? $settings['twilio_number'] : '';

        $twilio = new Client($account_sid, $auth_token);


        $twilio->messages->create($message['to'],
            [
                'from' => $twilio_number,
                'body' => $message['content']
            ]
        );
    }
}
