<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class CashbackRuleTranslation extends Model
{

    protected $table = 'cashback_rule_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
