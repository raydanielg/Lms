<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ProductCategoryTranslation extends Model
{
    protected $table = 'product_category_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
