<?php

namespace App\Models\Api;

use App\Models\Api\Traits\CheckWebinarItemAccessTrait;
use App\Models\Session as WebSession;

class Session extends WebSession
{
    use CheckWebinarItemAccessTrait ;

    public function getDetailsAttribute()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'auth_has_read' => $this->read,
            'user_has_access' => $this->user_has_access,
            'is_finished' => $this->isFinished(),
            'is_started'=>(time() > $this->date) ,
            'status' => $this->status,
            'order' => $this->order,
            'moderator_secret' => $this->moderator_secret,
            'date' => $this->date,
            'duration' => $this->duration,
            'link' => $this->link,
            'join_link' => (apiAuth()) ? $this->getJoinLink() : null,
            'can_join'=>(apiAuth() and !$this->isFinished() and time() > $this->date ) ,
            'session_api' => $this->session_api,
            'zoom_start_link' => $this->zoom_start_link,
            // 'session_api' => $this->session_api,
            'api_secret' => $this->api_secret,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'agora_settings ' => $this->agora_settings
        ];

    }

    public function getJoinLink($zoom_start_link = false)
    {
        $link = $this->link;

        if ($this->session_api == 'big_blue_button') {
            $link = route('big_blue_button', [
                'user_id' => apiAuth()->id,
                'session_id' => $this->id,
            ]);
            //  $link = url('panel/sessions/' . $this->id . '/joinToBigBlueButton');
        }

        if ($zoom_start_link and auth('api')->check() and auth('api')->id() == $this->creator_id and $this->session_api == 'zoom') {
            $link = $this->zoom_start_link;
        }

        if ($this->session_api == 'agora') {
            //  $link = url('panel/sessions/' . $this->id . '/joinToAgora');
           /* $link = route('agora', [
                'user_id' => apiAuth()->id,
                'session_id' => $this->id,
            ]);*/
            $link=null;
        }

        return $link;
    }

    public function getUserHasAccessAttribute()
    {
        $user = apiAuth();
        $hasBought = $this->webinar->checkUserHasBought($user);
        $access = false;
        if ($user and $hasBought and !$this->isFinished()) {
            $access = true;
        }
        return $access;
    }

    public function getReadAttribute()
    {
        $user = apiAuth();
        if (!$user) {
            return null;
        }

        return ($this->learningStatus()->where('user_id', $user->id)->count()) ? true : false;


    }
}



