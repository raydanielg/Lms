<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class WebinarExtraDescription extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'webinar_extra_descriptions';
    public $timestamps = false;
    protected $guarded = ['id'];

    static $types = ['learning_materials', 'company_logos', 'requirements'];
    static $LEARNING_MATERIALS = 'learning_materials';
    static $COMPANY_LOGOS = 'company_logos';
    static $REQUIREMENTS = 'requirements';

    public $translatedAttributes = ['value'];

    public function getValueAttribute()
    {
        return getTranslateAttributeValue($this, 'value');
    }

}
