<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCategory extends Model
{
    protected $table = 'discount_categories';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function discount()
    {
        return $this->belongsTo('App\Models\Discount', 'discount_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }
}
