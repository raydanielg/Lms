<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class QuizzesQuestionsAnswerTranslation extends Model
{
    protected $table = 'quizzes_questions_answer_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
