<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class SupportDepartment extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'support_departments';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }


    public function supports()
    {
        return $this->hasMany('App\Models\Support', 'department_id', 'id');
    }
}
