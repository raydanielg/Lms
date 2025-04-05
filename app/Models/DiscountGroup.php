<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountGroup extends Model
{
    protected $table = 'discount_groups';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function discount()
    {
        return $this->belongsTo('App\Models\Discount', 'discount_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }
}
