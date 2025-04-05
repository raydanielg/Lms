<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductSpecificationMultiValue extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'product_specification_multi_values';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function createName()
    {
        return str_replace(' ', '_', $this->title);
    }
}
