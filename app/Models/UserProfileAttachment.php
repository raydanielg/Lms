<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class UserProfileAttachment extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "user_profile_attachments";
    public $timestamps = false;

    protected $guarded = ['id'];

    public $translatedAttributes = ['title', 'description'];


    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }


    public function user()
    {
        return $this->belongsTo("\App\User", 'user_id', 'id');
    }

}
