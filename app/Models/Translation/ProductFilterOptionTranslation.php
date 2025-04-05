<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ProductFilterOptionTranslation extends Model
{
    protected $table = 'product_filter_option_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
