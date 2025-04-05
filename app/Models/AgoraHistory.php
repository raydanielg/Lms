<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgoraHistory extends Model
{
    protected $table = 'agora_history';
    public $timestamps = false;
    protected $dateFormat = "U";
    protected $guarded = ['id'];

    public function session()
    {
        return $this->belongsTo('App\Models\Session', 'session_id', 'id');
    }
}
