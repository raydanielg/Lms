<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductSelectedSpecification extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'product_selected_specifications';
    public $timestamps = false;
    protected $guarded = ['id'];

    static $inputTypes = ['textarea', 'multi_value'];
    static $Active = 'active';
    static $Inactive = 'inactive';
    static $itemsStatus = ['active', 'inactive'];

    public $translatedAttributes = ['value'];

    public function getValueAttribute()
    {
        return getTranslateAttributeValue($this, 'value');
    }


    public function specification()
    {
        return $this->belongsTo('App\Models\ProductSpecification', 'product_specification_id', 'id');
    }

    public function selectedMultiValues()
    {
        return $this->hasMany('App\Models\ProductSelectedSpecificationMultiValue', 'selected_specification_id', 'id');
    }
}
