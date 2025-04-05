<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "form_fields";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    static $fieldTypes = ['input', 'number', 'upload', 'date_picker', 'toggle', 'textarea', 'dropdown', 'checkbox', 'radio'];
    public $translatedAttributes = ['title'];


    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }


    /********
     * Relations
     * ******/

    public function options()
    {
        return $this->hasMany(FormFieldOption::class, 'form_field_id', 'id');
    }

}
