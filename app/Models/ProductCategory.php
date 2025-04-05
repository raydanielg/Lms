<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ProductCategory extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'product_categories';
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
        return $this->belongsTo('App\Models\ProductCategory', 'parent_id', 'id');
    }

    public function subCategories()
    {
        return $this->hasMany($this, 'parent_id', 'id')->orderBy('order', 'asc');
    }

    public function filters()
    {
        return $this->hasMany('App\Models\ProductFilter', 'category_id', 'id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'category_id', 'id');
    }

    public function getUrl()
    {
        return '/products?category_id=' . $this->id;
    }

    public function getSelfAndChideProductsCount($productType = null)
    {
        $ids = [$this->id];
        $subCategoriesIds = $this->subCategories->pluck('id')->toArray();
        $ids = array_merge($ids, $subCategoriesIds);

        $query = Product::whereIn('category_id', $ids);

        if (!empty($productType)) {
            $query->where('type', $productType);
        }

        return $query->count();
    }
}
