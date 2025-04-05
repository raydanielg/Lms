<?php

namespace App\Sessions;

use Illuminate\Support\Carbon;

class ZoomOAuth
{

    private function handleConfigs()
    {
        $settings = getFeaturesSettings();

        \Config::set("zoom.client_id", !empty($settings['zoom_client_id']) ? $settings['zoom_client_id'] : '');
        \Config::set("zoom.client_secret", !empty($settings['zoom_client_secret']) ? $settings['zoom_client_secret'] : '');
        \Config::set("zoom.account_id", !empty($settings['zoom_account_id']) ? $settings['zoom_account_id'] : '');
        \Config::set("zoom.base_url", "https://api.zoom.us/v2/");
    }


    public function makeMeeting($session): bool
    {
        $this->handleConfigs();

        $meeting = \Zoom::createMeeting([
            "agenda" => $session->title,
            "topic" => 'New meeting',
            "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
            "duration" => $session->duration, // in minutes
            "timezone" => 'UTC', // set your timezone
            "password" => $session->api_secret,
            "start_time" => new Carbon($session->date), // set your start time
            //"template_id" => 'set your template id', // set your template id  Ex: "Dv4YdINdTk+Z5RToadh5ug==" from https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingtemplates
            "pre_schedule" => false,  // set true if you want to create a pre-scheduled meeting
            "schedule_for" => null, // set your schedule for profile email
            "settings" => [
                'join_before_host' => true, // if you want to join before host set true otherwise set false
                'host_video' => true, // if you want to start video when host join set true otherwise set false
                'participant_video' => false, // if you want to start video when participants join set true otherwise set false
                'mute_upon_entry' => false, // if you want to mute participants when they join the meeting set true otherwise set false
                'waiting_room' => false, // if you want to use waiting room for participants set true otherwise set false
                'audio' => 'both', // values are 'both', 'telephony', 'voip'. default is both.
                'auto_recording' => 'none', // values are 'none', 'local', 'cloud'. default is none.
                'approval_type' => 0, // 0 => Automatically Approve, 1 => Manually Approve, 2 => No Registration Required
            ],
        ]);


        if (!empty($meeting) and isset($meeting['status']) and $meeting['status']) {
            unset($session->title, $session->locale);

            $session->update([
                'link' => $meeting['data']['join_url'],
                'api_secret' => $meeting['data']['password'],
            ]);

            return true;
        }

        return false;
    }
}
