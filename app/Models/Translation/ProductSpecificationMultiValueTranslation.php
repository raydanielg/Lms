<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ProductSpecificationMultiValueTranslation extends Model
{
    protected $table = 'product_specification_multi_value_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
