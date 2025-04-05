<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class ProductBadge extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "product_badges";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title'];


    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    /********
     * Relations
     * ******/

    public function contents()
    {
        return $this->hasMany('App\Models\ProductBadgeContent', 'product_badge_id', 'id');
    }


}
