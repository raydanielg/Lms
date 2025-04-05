<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class AbandonedCartRuleUserGroup extends Model
{
    protected $table = 'abandoned_cart_rule_users_groups';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


}
