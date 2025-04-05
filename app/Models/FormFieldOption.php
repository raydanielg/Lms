<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class FormFieldOption extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "form_field_options";
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


}
