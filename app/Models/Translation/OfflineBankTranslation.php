<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class OfflineBankTranslation extends Model
{

    protected $table = 'offline_bank_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
