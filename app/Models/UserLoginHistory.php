<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    protected $table = "user_login_histories";
    public $timestamps = false;
    protected $guarded = ['id'];


    /* ==========
     | Relations
     * ==========*/

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }


    /* ==========
     | Helpers
     * ==========*/

    public function getDuration()
    {
        $result = "-";

        if (!empty($this->session_end_at)) {
            $timestamp1 = Carbon::createFromTimestamp($this->session_start_at);
            $timestamp2 = Carbon::createFromTimestamp($this->session_end_at);

            $duration = $timestamp1->diff($timestamp2);
            $hours = $duration->h; // Hours
            $minutes = $duration->i; // Minutes
            $sec = $duration->s; // Second

            $result = "";

            if ($hours > 0) {
                $result .= $hours . ' Hr';
            }

            if ($minutes > 0) {
                $result .= ($result ? ', ' : '') . $minutes . ' Min';
            }

            if ($hours <= 0 and $minutes <= 0 and $sec > 0) {
                $result .= $sec . ' Second';
            }
        }

        return $result;
    }
}
