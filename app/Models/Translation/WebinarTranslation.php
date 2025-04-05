<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class WebinarTranslation extends Model
{
    protected $table = 'webinar_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
