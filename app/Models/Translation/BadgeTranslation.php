<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class BadgeTranslation extends Model
{
    protected $table = 'badge_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
