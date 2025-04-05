<?php

namespace App\Models\Api;

use App\Models\QuizzesResult as WebQuizzesResult;
use App\User;
use App\Models\Role;


class QuizzesResult extends WebQuizzesResult
{
    public function getBriefAttribute()
    {
        return [
            'id' => $this->id,
            'quiz' => $this->quiz->details,
            'webinar' => $this->quiz->webinar->brief,
            'user' => $this->user->brief,
            'user_grade' => $this->user_grade,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'auth_can_try_again' => $this->quiz->auth_can_take_quiz,
            'count_try_again' => $this->quiz->CountTryAgain,

        ];
    }

    public function getDetailsAttribute()
    {
        $details = [
            'reviewable' => $this->reviewable,

            'answer_sheet' => json_decode($this->results, true),
            'quiz_review' => $this->quiz_review,
        ];

        return array_merge($this->brief, $details);
    }


    public function getFinishedAttribute()
    {
        if (
            !$this->results && $this->status == QuizzesResult::$waiting
        ) {

            return false;
        }

        return true;
    }


    public function getQuizReviewAttribute()
    {

        $r = [];


        foreach ($this->quiz->quizQuestions as $question) {

            $details = $question->details;


            $answer_sheet = json_decode($this->results, true);
            $user_answer = $answer_sheet[$question['id']] ?? null;
            if (!$user_answer) {
                continue;
            }
            // $question->user_answer= $user_answer ;
            $details['user_answer'] = [
                'grade' => $user_answer['grade'] ?? null,
                'status' => $user_answer['status'] ?? null,
            ];
            $details['user_answer']['answer'] = $user_answer['answer'] ?? null;

            // if($question->type==QuizzesQuestion::$descriptive){
            //     $details['user_answer']['answer'] =$user_answer['answer']??null ;
            // }else{
            //     $details['user_answer']['answer_id'] =$user_answer['answer']??null ;
            // }


            $correct_answer = $question->quizzesQuestionsAnswers()->where('correct', 1)->first();


            $details['descriptive_correct_answer'] = ($question->type == QuizzesQuestion::$descriptive) ? $question->correct : null;
            $details['multiple_correct_answer'] = ($question->type == QuizzesQuestion::$multiple) ? $correct_answer->details : null;

            $r[] = $details;


        }

        return $r;

    }


    public function getReviewableAttribute()
    {

        return ($this->status == self::$waiting && $this->results) ? true : false;
    }

    public function scopeHandleFilters($query)
    {

        $request = request();
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $quiz_id = $request->get('quiz_id', null);
        $total_mark = $request->get('total_mark', null);
        $status = $request->get('status', null);
        $user_id = $request->get('user_id', null);
        $creator_id = $request->get('creator_id', null);

        $instructor = $request->get('instructor', null);
        $open_results = $request->get('open_results', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($quiz_id) and $quiz_id != 'all') {
            $query->where('quiz_id', $quiz_id);
        }

        if ($total_mark) {
            $query->where('total_mark', $total_mark);
        }

        if (!empty($user_id) and $user_id != 'all') {
            $query->where('user_id', $user_id);
        }
        if (!empty($creator_id) and $creator_id != 'all') {
            $query->where('creator_id', $creator_id);
        }

        if ($instructor) {
            $userIds = User::whereIn('role_name', [Role::$teacher, Role::$organization])
                ->where('full_name', 'like', '%' . $instructor . '%')
                ->pluck('id')->toArray();

            $query->whereIn('creator_id', $userIds);
        }

        if ($status and $status != 'all') {
            $query->where('status', strtolower($status));
        }

        if (!empty($open_results)) {
            $query->where('status', 'waiting');
        }

        return $query;

    }

    public function quiz()
    {
        return $this->belongsTo('App\Models\Api\Quiz', 'quiz_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Api\User', 'user_id', 'id');
    }

    public function certificate()
    {
        return $this->hasOne('App\Models\Api\Certificate', 'quiz_result_id', 'id');
    }

}
