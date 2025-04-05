<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class WebinarChapterTranslation extends Model
{
    protected $table = 'webinar_chapter_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
