<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class OfflineBankSpecificationTranslation extends Model
{

    protected $table = 'offline_bank_specification_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
