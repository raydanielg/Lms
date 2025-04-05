<?php
namespace App\Models\Api ;
use App\Models\QuizzesQuestion as WebQuizzesQuestion;

class QuizzesQuestion extends WebQuizzesQuestion{

    public function quizzesQuestionsAnswers()
    {
        return $this->hasMany('App\Models\Api\QuizzesQuestionsAnswer', 'question_id', 'id');
    }

    public function getAnswersAttribute(){

        return $this->quizzesQuestionsAnswers->map(function($answer){
            return $answer->details ;
        }) ;
    }

    public function getDetailsAttribute(){

        return [
            'id'=>$this->id ,
            'title'=>$this->title ,
            'type'=>$this->type ,
            'descriptive_correct_answer'=>$this->correct  ,
            'grade'=>$this->grade ,
            'created_at'=>$this->created_at ,
            'answers'=>$this->answers ,
            'updated_at'=>$this->updated_at ,

            
        ] ;
    }
}
