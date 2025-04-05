<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class FormFieldOptionTranslation extends Model
{

    protected $table = 'form_field_option_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
