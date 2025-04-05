<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class SessionTranslation extends Model
{
    protected $table = 'session_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
