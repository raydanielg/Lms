<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class CertificateTemplate extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "certificates_templates";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
    static $templateWidth = 930;
    static $templateHeight = 600;

    public $translatedAttributes = ['title', 'body', 'elements'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getBodyAttribute()
    {
        return getTranslateAttributeValue($this, 'body');
    }

    public function getElementsAttribute()
    {
        $elements = getTranslateAttributeValue($this, 'elements');

        if (!empty($elements)) {
            $elements = json_decode($elements, true);
        }

        return $elements;
    }

    public function getRtlAttribute()
    {
        return getTranslateAttributeValue($this, 'rtl');
    }
}
