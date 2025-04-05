<?php

namespace App\Models\Api;

use App\Models\Follow as Model;

class Follow extends Model
{


    public function user()
    {
        return $this->belongsTo('App\Models\Api\User', 'user_id', 'id');
    }

    public function userFollower()
    {
        return $this->belongsTo('App\Models\Api\User', 'follower', 'id');
    }
}
