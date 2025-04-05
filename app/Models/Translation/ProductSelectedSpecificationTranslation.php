<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ProductSelectedSpecificationTranslation extends Model
{
    protected $table = 'product_selected_specification_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
