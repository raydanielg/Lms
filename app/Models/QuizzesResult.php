<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizzesResult extends Model
{
    static $passed = 'passed';
    static $failed = 'failed';
    static $waiting = 'waiting';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function quiz()
    {
        return $this->belongsTo('App\Models\Quiz', 'quiz_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }


    public function getQuestions()
    {
        $quiz = $this->quiz;

        if ($quiz->display_limited_questions and !empty($quiz->display_number_of_questions)) {

            $results = json_decode($this->results, true);
            $quizQuestionIds = [];

            if (!empty($results)) {
                foreach ($results as $id => $v) {
                    if (is_numeric($id)) {
                        $quizQuestionIds[] = $id;
                    }
                }
            }

            $quizQuestions = $quiz->quizQuestions()->whereIn('id',$quizQuestionIds)->get();
        } else {
            $quizQuestions = $quiz->quizQuestions;
        }

        return $quizQuestions;
    }
}
