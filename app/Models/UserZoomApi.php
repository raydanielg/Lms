<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserZoomApi extends Model
{
    protected $table = 'users_zoom_api';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
