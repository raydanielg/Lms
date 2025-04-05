<?php

namespace App\Sessions;

use Illuminate\Support\Carbon;

class Zoom
{
    public function getUserByJwt($jwt)
    {
        try {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.zoom.us/v2/users/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "authorization: Bearer $jwt",
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $result = null;
            } else {
                $response = json_decode($response, true);

                if (!empty($response) and !empty($response['users'])) {
                    $result = $response['users'][0];
                } else {
                    $result = null;
                }
            }

            return $result;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function storeUserMeeting($session, $user, $jwt)
    {
        try {
            $data = [
                'topic' => $session->title,
                'type' => 1,
                'start_time' => new Carbon($session->date),
                'duration' => $session->duration,
            ];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.zoom.us/v2/users/" . $user['id'] . "/meetings",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => array(
                    "authorization: Bearer $jwt",
                    "content-type: application/json"
                ),
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $result = null;
            } else {
                $response = json_decode($response, true);

                if (!empty($response) and !empty($response['uuid'])) {
                    $result = [
                        'start_url' => $response['start_url'],
                        'join_url' => $response['join_url']
                    ];
                } else {
                    $result = null;
                }
            }

            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }
}
