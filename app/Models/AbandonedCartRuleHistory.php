<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCartRuleHistory extends Model
{
    protected $table = "abandoned_cart_rule_histories";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    // #############
    // Relations
    // ############
}
