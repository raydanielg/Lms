<?php

namespace App\Models\Api;

use App\Models\Certificate as WebCertificate;

class Certificate extends WebCertificate
{

    public function getDetailsAttribute()
    {
        return [
            'id' => $this->id,
            'user_grade' => $this->user_grade,
            'user' => $this->student->brief,
            'quiz' => $this->quiz->details,
            'quiz_result' => $this->quizzesResult->details,
            'file' => ($this->file) ? url($this->file) : null,
            'created_at' => $this->created_at,
        ];
    }

    public function getBriefAttribute()
    {
        return [
            'id' => $this->id,
            'user_grade' => $this->user_grade,
            'file' => ($this->file) ? url($this->file) : null,
            'link' => route("api.quiz.certificate.show",$this->quizzesResult->id),
            'created_at' => $this->created_at,
        ];
    }

    public function scopeHandleFilter($query)
    {
        $request = request();
        $from = $request->get('from');
        $to = $request->get('to');
        $webinar_id = $request->get('webinar_id');

        fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($webinar_id)) {
            $query->where('webinar_id', $webinar_id);
        }

        return $query;
    }


    public function quiz()
    {
        return $this->hasOne('App\Models\Api\Quiz', 'id', 'quiz_id');
    }

    public function student()
    {
        return $this->hasOne('App\Models\Api\User', 'id', 'student_id');
    }

    public function quizzesResult()
    {
        return $this->hasOne('App\Models\Api\QuizzesResult', 'id', 'quiz_result_id');
    }
}
