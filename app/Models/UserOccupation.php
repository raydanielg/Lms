<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOccupation extends Model
{
    protected $table = 'users_occupations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
