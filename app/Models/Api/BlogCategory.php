<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class BlogCategory extends Model  implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['title'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    //
    public function getDetailsAttribute(){
        return [
            'id'=>$this->id ,
            'title'=>$this->title
        ] ;
    }
}
