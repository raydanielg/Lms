<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ProductFaqTranslation extends Model
{
    protected $table = 'product_faq_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
