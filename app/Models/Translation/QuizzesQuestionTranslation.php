<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class QuizzesQuestionTranslation extends Model
{
    protected $table = 'quiz_question_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
