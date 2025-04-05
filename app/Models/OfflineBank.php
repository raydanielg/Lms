<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class OfflineBank extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "offline_banks";
    public $timestamps = false;

    protected $guarded = ['id'];


    public $translatedAttributes = ['title'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function specifications()
    {
        return $this->hasMany('App\Models\OfflineBankSpecification', 'offline_bank_id', 'id');
    }
}
