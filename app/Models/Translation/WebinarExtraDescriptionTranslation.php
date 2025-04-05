<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class WebinarExtraDescriptionTranslation extends Model
{
    protected $table = 'webinar_extra_description_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
