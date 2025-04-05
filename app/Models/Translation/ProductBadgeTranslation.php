<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class ProductBadgeTranslation extends Model
{

    protected $table = 'product_badge_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
