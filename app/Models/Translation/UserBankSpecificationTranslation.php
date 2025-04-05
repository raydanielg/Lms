<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class UserBankSpecificationTranslation extends Model
{

    protected $table = 'user_bank_specification_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
