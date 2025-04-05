<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    // custom user badges

    protected $table = 'users_badges';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function badge()
    {
        return $this->belongsTo('App\Models\Badge', 'badge_id', 'id');
    }
}
