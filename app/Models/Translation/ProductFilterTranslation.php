<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ProductFilterTranslation extends Model
{
    protected $table = 'product_filter_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
