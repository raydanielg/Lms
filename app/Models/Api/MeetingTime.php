<?php

namespace App\Models\Api;

use App\Models\MeetingTime as Model;

class MeetingTime extends Model
{
    //
    public function meeting()
    {
        return $this->belongsTo('App\Models\Api\Meeting', 'meeting_id', 'id');
    }
}
