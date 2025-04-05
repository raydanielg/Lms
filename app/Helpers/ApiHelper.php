<?php

use App\Api\Response;
use App\Api\Request;
use App\Models\Api\UserFirebaseSessions;
use Kreait\Firebase\Messaging\CloudMessage;

function validateParam($request_input, $rules, $somethingElseIsInvalid = null)
{
    $request = new Request();
    return $request->validateParam($request_input, $rules, $somethingElseIsInvalid);
}

function apiResponse2($success, $status, $msg, $data = null, $title = null)
{
    $response = new Response();
    return $response->apiResponse2($success, $status, $msg, $data, $title);
}


function apiAuth()
{
    if (request()->input('test_auth_id')) {
        return App\Models\Api\User::find(request()->input('test_auth_id')) ?? die('test_auth_id not found');
    }
    return auth('api')->user();


}

function nicePrice($price)
{
    $nice = handlePrice($price, false);

    if (is_string($nice)) {
        $nice = (float)$nice;
    }

    return round($nice, 2);
}

function nicePriceWithTax($price)
{

    // return round(handlePrice($price, true,false,true), 2);
    $nice = handlePrice($price, false, false, true);
    if ($nice === 0) {
        return [
            "price" => 0,
            "tax" => 0
        ];
    }
    return $nice;
}


function handleSendFirebaseMessages($user_id, $group_id, $sender, $type, $title, $message)
{
    $fcmTokens = UserFirebaseSessions::where('user_id', $user_id)
        ->select('fcm_token')->get()->all();

    $deviceTokens = [];

    foreach ($fcmTokens as $fcmToken) {
        $deviceTokens[] = $fcmToken->fcm_token;
    }

    if (count($deviceTokens) > 0) {
        $messageFCM = app('firebase.messaging');

        foreach ($deviceTokens as $fcmToken) {
            $fcmMessage = CloudMessage::withTarget('token', $fcmToken);

            $fcmMessage = $fcmMessage->withNotification([
                'title' => $title,
                'body' => preg_replace('/<[^>]*>/', '', $message)
            ]);

            $fcmMessage = $fcmMessage->withData([
                'user_id' => $user_id,
                'group_id' => $group_id,
                'title' => $title,
                'message' => preg_replace('/<[^>]*>/', '', $message),
                'sender' => $sender,
                'type' => $type,
                'created_at' => time()
            ]);

            $fcmMessage = $fcmMessage->withAndroidConfig(\Kreait\Firebase\Messaging\AndroidConfig::fromArray([
                'ttl' => '3600s',
                'priority' => 'high',
                'notification' => [
                    'color' => '#f45342',
                    'sound' => 'default',
                ],
            ]));

            try {
                $messageFCM->send($fcmMessage);
            } catch (\Exception $exception) {

            }

        }

    }
}



