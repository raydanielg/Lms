<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class CartDiscountTranslation extends Model
{

    protected $table = 'cart_discount_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
