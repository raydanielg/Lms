<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ProductSpecificationTranslation extends Model
{
    protected $table = 'product_specification_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
