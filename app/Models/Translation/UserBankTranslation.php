<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class UserBankTranslation extends Model
{

    protected $table = 'user_bank_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
