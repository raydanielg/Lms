<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductFilter extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'product_filters';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }


    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'category_id', 'id');
    }

    public function options()
    {
        return $this->hasMany('App\Models\ProductFilterOption', 'filter_id', 'id')->orderBy('order', 'asc');
    }
}
