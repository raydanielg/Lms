<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class SubscribeTranslation extends Model
{
    protected $table = 'subscribe_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
