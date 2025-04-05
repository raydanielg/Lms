<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class TextLessonTranslation extends Model
{
    protected $table = 'text_lesson_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
