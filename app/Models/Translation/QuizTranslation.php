<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class QuizTranslation extends Model
{
    protected $table = 'quiz_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
