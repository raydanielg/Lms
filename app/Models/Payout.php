<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    public static $waiting = 'waiting';
    public static $done = 'done';
    public static $reject = 'reject';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function userSelectedBank()
    {
        return $this->belongsTo('App\Models\UserSelectedBank', 'user_selected_bank_id', 'id');
    }
}
