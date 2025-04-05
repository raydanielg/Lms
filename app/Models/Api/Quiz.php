<?php

namespace App\Models\Api;

use App\Models\Api\QuizzesResult;
use App\Models\Api\Traits\CheckWebinarItemAccessTrait;
use App\Models\Quiz as Model;
use App\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Failed;

class Quiz extends Model
{
    use CheckWebinarItemAccessTrait ;

    public function getBriefAttribute()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'time' => $this->time,
            'auth_status' => $this->auth_status,
            'question_count' => $this->quizQuestions->count(),
            'total_mark' => $this->quizQuestions->sum('grade'),
            'pass_mark' => $this->pass_mark,
            'average_grade' => $this->average_grade,
            'student_count' => $this->quizResults->pluck('user_id')->count(),
            'certificates_count' => $this->certificates->count(),
            'success_rate' => $this->success_rate,
            'status' => $this->status,
            'attempt' => $this->attempt,
            'created_at' => $this->created_at,
            'certificate' => $this->certificate,
            'teacher' => $this->creator->brief,

            /**********************/

            'auth_attempt_count' => $this->auth_attempt_count,
            'attempt_state' => $this->attempt_state,
            'auth_can_start' => $this->auth_can_take_quiz,
            'webinar' => $this->webinar->brief,
        ];
    }

    public function getDetailsAttribute()
    {
        $details = [
            'questions' => $this->quizQuestions->map(function ($question) {
                return $question->details;
            }),
            'chapter' => ($this->chapter) ? $this->chapter->details : null,

            'auth_can_download_certificate' => $this->auth_can_download_certificate,
            'participated_count' => $this->quizResults->count(),



            'latest_students' => $this->latest_students,

        ];

        return array_merge($this->brief, $details);
    }


    public function getAuthCanTakeQuizStatusAttribute()
    {
        $user = apiAuth();
        if (!$user) {
            return null;
        }
        $status = 'ok';
        $hasBought = $this->webinar->checkUserHasBought($user);
        if (!$hasBought) {
            $status = 'not_purchased';
        } elseif ($this->auth_passed_quiz) {
            $status = 'passed';
        } //  !$this->results && $this->status == QuizzesResult::$waiting
        elseif (isset($this->attempt) and
            $this->auth_attempt_count >= $this->attempt

        ) {
            $status = 'max_attempt';
        }


        return $status;

    }

    public function getAuthCanTakeQuizAttribute()
    {
        $user = apiAuth();
        if (!$user) {
            return null;
        }

        if ($this->auth_can_take_quiz_status == 'ok') {
            return true;
        }
        return false;
    }

    public function getAuthPassedQuizAttribute()
    {
        $user = apiAuth();
        if (!$user) {
            return null;
        }
        $userQuizDone = $this->auth_results;

        $status_pass = false;
        foreach ($userQuizDone as $result) {
            if ($result->status == QuizzesResult::$passed) {
                $status_pass = true;
            }
        }
        return $status_pass;
    }

    public function getAuthAttemptCountAttribute()
    {
        if ($this->auth_results) {
            return $this->auth_results->count();
        }
        return null;

    }

    public function getCountTryAgainAttribute()
    {
        if (!$this->auth_can_take_quiz) {
            return 0;
        }

        if (!$this->attempt) {
            return 'unlimited';
        }

        $diff = $this->attempt - $this->auth_results->count();
        return ($diff >= 0) ? $diff : 0;
    }

    public function getAuthResultsAttribute()
    {
        // $user->quizResults->where('quiz_id', $this->id)
        $user = apiAuth();
        if (!$user) {
            return null;
        }
        $userQuizDone = QuizzesResult::where('quiz_id', $this->id)
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return $userQuizDone;
    }

    public function getAttemptStateAttribute()
    {
        $a = (!empty(apiAuth()) and !empty($this->auth_results))
            ? $this->auth_results->count() : '0';

        return $a . '/' . $this->attempt;
    }

    public function getAuthCanDownloadCertificateAttribute()
    {

        if (!apiAuth()) {
            return null;
        }

        $canDownloadCertificate = false;

        if (!$this->certificate) {
            return false;
        }

        $user_passed_quiz = apiAuth()->quizResults->where('quiz_id', $this->id)->where('status', 'passed');

        if ($user_passed_quiz->count()) {
            $canDownloadCertificate = true;
        }

        return $canDownloadCertificate;

    }

    public function getSuccessRateAttribute()
    {

        if ($this->quizResults->count()) {
            return round($this->quizResults->where('status', QuizzesResult::$passed)->pluck('user_id')->count() / $this->quizResults->count() * 100);
        }
        return 0;

    }

    public function getLatestStudentsAttribute()
    {

        ///   return 'f' ;
        return $this->quizResults()->orderBy('created_at', 'desc')->groupBy('user_id')->get()->map(function ($result) {
            return $result->user->brief/// ->user()
                ;
        });
    }

    public function getAverageGradeAttribute()
    {
       // $quiz->avg_grade = $quizResults->where('status', \App\Models\QuizzesResult::$passed)->avg('user_grade');

        return  round($this->quizResults->where('status', QuizzesResult::$passed)->avg('user_grade'),2);


    }

    public function getAuthStatusAttribute()
    {
        $user = apiAuth();
        if (!$user) {
            return null;
        }
        $user_quiz_result = $user->quizResults()->
        where('quiz_id', $this->id)
            ->orderBy('id', 'desc')
            ->get();

        if (!$user_quiz_result->count()) {
            return 'not_participated';
        }
        if ($user_quiz_result->where('status', 'passed')->count() > 0) {
            return 'passed';
        }
        if ($user_quiz_result->first()->status == 'waiting') {
            return 'waiting';
        }

        if ($user_quiz_result->first()->status == 'failed') {
            return 'failed';
        }

        return null;

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
        $webinar_id = $request->get('webinar_id', null);
        $instructor = $request->get('instructor', null);
        $open_results = $request->get('open_results', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($webinar_id)) {
            $query->where('webinar_id', $webinar_id);
        }

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


    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Models\Api\WebinarChapter', 'webinar_id', 'id');
    }

    public function quizResults()
    {
        return $this->hasMany('App\Models\Api\QuizzesResult', 'quiz_id', 'id');
    }

    public function quizQuestions()
    {
        return $this->hasMany('App\Models\Api\QuizzesQuestion', 'quiz_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\Api\User', 'creator_id', 'id');
    }


}





