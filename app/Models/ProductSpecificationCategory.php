<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSpecificationCategory extends Model
{
    protected $table = 'product_specification_categories';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'category_id', 'id');
    }
}
