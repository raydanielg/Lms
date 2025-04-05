<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class FormTranslation extends Model
{

    protected $table = 'form_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
