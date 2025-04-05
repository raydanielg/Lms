<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class InstallmentTranslation extends Model
{

    protected $table = 'installment_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
