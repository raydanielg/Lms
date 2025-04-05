<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class FaqTranslation extends Model
{
    protected $table = 'faq_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
