<?php

namespace App\Models\Api;

use App\Models\Sale;
use App\Models\WebinarAssignmentHistory as Model;

class WebinarAssignmentHistory extends Model
{
    public function deadline()
    {
        $deadline = true; // default can access
        $assignment = $this->assignment;
        if (!empty($assignment->deadline)) {
            $conditionDay = $assignment->getDeadlineTimestamp($this->student);
            if (time() > $conditionDay) {
                $deadline = false;
            } else {
                $deadline = round(($conditionDay - time()) / (60 * 60 * 24), 1);
                $deadline = ceil($deadline);
            }
        }
        return $deadline;
    }

    public function deadlineDays()
    {
        if (!$this->deadline()) {
            return 'expired';
        } elseif (is_bool($this->deadline())) {
            return 'unlimited';
        } else {
            return $this->deadline();
        }
    }

    public function checkHasAttempts()
    {
        $result = true;
        $user = $this->student;
        $assignment = $this->assignment;

        if (!empty($assignment->attempts) and $user->id != $assignment->creator_id) {
            $submissionTimes = $this->messages
                ->where('sender_id', $user->id)
                //->whereNotNull('file_path')
                ->count();

            $result = ($submissionTimes < $assignment->attempts);
        }

        return $result;
    }

    public function submissionTimes()
    {
        return $this->messages->count();
    }

    public function canSendMessage()
    {
        $user = $this->student;

        $assignment = $this->assignment;
        return !(
            $user->id != $assignment->creator_id and
            (
                $this->status == WebinarAssignmentHistory::$passed or
                $this->status == WebinarAssignmentHistory::$notPassed or
                !$this->deadline() or
                (
                    !$this->checkHasAttempts() and !empty($assignment->attempts) and $this->submissionTimes() >= $assignment->attempts
                )
            )
        );
    }

    public function getLastSubmissionAttribute()
    {
        if ($this->messages) {
            return $this->messages->where('sender_id', apiAuth()->id)->first()->created_at ?? null;
        }
        return null;
    }

    public function getFirstSubmissionAttribute()
    {
        if ($this->messages) {
            return $this->messages->where('sender_id', apiAuth()->id)->last()->created_at ?? null;
        }
        return null;
    }

    public function getUsedAttemptsCountAttribute()
    {
        if ($this->messages) {
            return $this->messages->where('sender_id', $this->student_id)->count() ?? 0;
        }
        return 0;
    }

    public function sale()
    {
        return Sale::where('buyer_id', $this->student_id)
            ->where('webinar_id', $this->assignment->webinar_id)
            ->whereNull('refund_at')
            ->first();

    }

    public function assignment()
    {
        return $this->belongsTo('App\Models\Api\WebinarAssignment', 'assignment_id', 'id');
    }
}
