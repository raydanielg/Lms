<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class AdvertisingBanner extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'advertising_banners';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title', 'image'];


    static $positions = [
        'home1', 'home2', 'course', 'course_sidebar', 'product_show', 'bundle', 'bundle_sidebar', 'upcoming_course', 'upcoming_course_sidebar'
    ];

    static $size = [
        '12' => 'full',
        '6' => '1/2',
        '4' => '1/3',
        '3' => '1/4'
    ];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getImageAttribute()
    {
        return getTranslateAttributeValue($this, 'image');
    }
}
