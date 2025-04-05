<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class NavbarButton extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'navbar_buttons';
    public $timestamps = false;
    protected $guarded = ['id'];

    public $translatedAttributes = ['title', 'url'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getUrlAttribute()
    {
        return getTranslateAttributeValue($this, 'url');
    }


    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }
}
