<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class PromotionTranslation extends Model
{
    protected $table = 'promotion_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
