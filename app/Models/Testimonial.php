<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Testimonial extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'testimonials';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['user_name', 'user_bio', 'comment'];

    public function getUserNameAttribute()
    {
        return getTranslateAttributeValue($this, 'user_name');
    }

    public function getUserBioAttribute()
    {
        return getTranslateAttributeValue($this, 'user_bio');
    }

    public function getCommentAttribute()
    {
        return getTranslateAttributeValue($this, 'comment');
    }
}
