<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class InstallmentStepTranslation extends Model
{

    protected $table = 'installment_step_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
