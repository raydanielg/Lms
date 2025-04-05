<?php

namespace App\Models\Api;

use App\Models\Api\Traits\CheckWebinarItemAccessTrait;
use App\Models\Sale;
use  App\Models\WebinarAssignment as Model;
use App\Models\WebinarAssignmentHistory;
use App\Models\WebinarAssignmentHistoryMessage;
use Illuminate\Http\Request;

class WebinarAssignment extends Model
{
    use CheckWebinarItemAccessTrait;

    public function scopeHandleFilters($query)
    {
        $request = \request();
        $user = apiAuth();
        $from = $request->get('from');
        $to = $request->get('to');
        $webinarId = $request->get('webinar_id');
        $status = $request->get('status');

        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($webinarId)) {
            $query->where('webinar_id', $webinarId);
        }

        if (!empty($status)) {
            $query->whereHas('assignmentHistory', function ($query) use ($user, $status) {
                $query->where('student_id', $user->id);
                $query->where('status', $status);
            });
        }

        return $query;
    }

    public function getDeadlineTimeAttribute()
    {
        if (!apiAuth()) {
            return null;
        }
        if (!empty($this->deadline)) {
            $sale = Sale::where('buyer_id', apiAuth()->id)
                ->where('webinar_id', $this->webinar_id)
                ->whereNull('refund_at')
                ->first();

            return strtotime("+{$this->deadline} days", $sale->created_at);

        }
    }

    public function userAssignmentHistory()
    {
        $user = apiAuth();
        $id = 0;
        if($user){
            $id = apiAuth()->id;
        }
        return $this->assignmentHistory()->where('student_id', $id);
    }


    public function getLastSubmissionAttribute()
    {
        $user = apiAuth();
        $id = 0;
        if($user){
            $id = apiAuth()->id;
        }
        if ($this->userAssignmentHistory and $this->userAssignmentHistory->messages) {
            return $this->userAssignmentHistory->messages->where('sender_id',$id)->first()->created_at ?? null;
        }
        return null;
    }
    public function attachments()
    {
        return $this->hasMany('App\Models\Api\WebinarAssignmentAttachment', 'assignment_id', 'id');
    }

    public function getFirstSubmissionAttribute()
    {
        if ($this->userAssignmentHistory and $this->userAssignmentHistory->messages) {
            return $this->userAssignmentHistory->messages->where('sender_id', apiAuth()->id)->last()->created_at ?? null;
        }
        return null;

    }

    public function getUsedAttemptsCountAttribute()
    {
        if ($this->userAssignmentHistory and $this->userAssignmentHistory->messages) {
            return $this->userAssignmentHistory->messages->where('sender_id', apiAuth()->id)->count() ?? 0;
        }
        return 0;
    }

    public function getAssignmentStatusAttribute()
    {
        if (empty($this->assignmentHistory) or ($this->assignmentHistory->status == \App\Models\WebinarAssignmentHistory::$notSubmitted)) {

            return 'not_submitted';
        } else {
            return $this->userAssignmentHistory->status;
        }
    }

    public function grades()
    {

        return $this->assignmentHistory()->get()->filter(function ($item) {
            return !is_null($item->grade);
        });

    }

    public function getMinGradeAttribute()
    {
        $grades = $this->grades();
        return $grades->count() ? $grades->min('grade') : null;
    }

    public function getAvgGradeAttribute()
    {
        $grades = $this->grades();
        return $grades->count() ? $grades->avg('grade') : null;
    }

    public function getPendingCountAttribute()
    {
        return $this->instructorAssignmentHistories()->where('status', WebinarAssignmentHistory::$pending)->count();
    }

    public function getPassedCountAttribute()
    {
        return $this->instructorAssignmentHistories()->where('status', WebinarAssignmentHistory::$passed)->count();
    }

    public function getFailedCountAttribute()
    {

        return $this->instructorAssignmentHistories()->where('status', WebinarAssignmentHistory::$notPassed)->count();
    }

    public function getSubmissionsCountAttribute()
    {

        $historyIds = $this->instructorAssignmentHistories->pluck('id')->toArray();

        return WebinarAssignmentHistoryMessage::whereIn('assignment_history_id', $historyIds)
            ->where('sender_id', '!=', $this->creator_id)
            ->count();
        // return $this->instructorAssignmentHistories()->messages()->where('sender_id', '!=', $this->creator_id)->count();
    }

    public function assignmentHistory()
    {
        $user = apiAuth();
        $id = 0;
        if($user){
            $id = apiAuth()->id;
        }

        return $this->hasOne('App\Models\Api\WebinarAssignmentHistory', 'assignment_id', 'id')
            ->withDefault([
                'student_id' => $id,
                'status' => WebinarAssignmentHistory::$notSubmitted
            ]);
    }



}
