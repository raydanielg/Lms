<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountUser extends Model
{
    protected $table = 'discount_users';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function discount()
    {
        return $this->belongsTo('App\Models\Discount', 'discount_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
