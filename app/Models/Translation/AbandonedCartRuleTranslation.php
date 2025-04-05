<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class AbandonedCartRuleTranslation extends Model
{

    protected $table = 'abandoned_cart_rule_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
