<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebinarAssignmentHistoryMessageResource;
use App\Models\Api\WebinarAssignment;
use App\Models\Api\WebinarAssignmentHistory;
use App\Models\Api\WebinarAssignmentHistoryMessage;
use Illuminate\Http\Request;

class AssignmentHistoryMessageController extends Controller
{
    public function store(Request $request, $assignmentId)
    {
        $user = apiAuth();

        $assignment = WebinarAssignment::where('id', $assignmentId)->first();
        abort_unless($assignment, 404);
        $webinar = $assignment->webinar;

        if (!empty($webinar) and $webinar->checkUserHasBought($user)) {
            // $studentId = $request->get('student_id');
            $user_id = \request()->input('student_id');
            $assignmentHistory = $this->getAssignmentHistory($webinar, $assignment, $user, $user_id);

            abort_unless($assignmentHistory,404);

            if (!empty($assignmentHistory)) {

                if ($user->id != $assignment->creator_id) {
                    $submissionTimes = $assignmentHistory->messages
                        ->where('sender_id', $user->id)
                        //  ->whereNotNull('file_path')
                        ->count();
                    $deadline = $this->getAssignmentDeadline($assignment, $user);

                    // dd($assignment->attempts) ;
                    //     dd((!empty($assignment->attempts) and $submissionTimes >= $assignment->attempts)) ;
                    if (!$deadline or (!empty($assignment->attempts) and $submissionTimes >= $assignment->attempts)) {
                        return apiResponse2(0, 401, trans('update.assignment_deadline_error_desc'));
                    }
                }

                $data = $request->all();
                validateParam($request->all(), [
                    'message' => 'required',
                    'file_title' => 'nullable|max:255',
                    'file_path' => 'nullable',
                ]);
                $ee = [
                    'assignment_history_id' => $assignmentHistory->id,
                    'sender_id' => $user->id,
                    'message' => $data['message'],
                    'file_title' => $data['file_title'] ?? null,
                    'file_path' => $data['file_path'] ?? null,
                    'created_at' => time(),
                ];
                if (isset($data['file_path'])) {
                    $ee['file_path'] = $data['file_path'];
                }
                WebinarAssignmentHistoryMessage::create($ee);

                if ($assignmentHistory->status == WebinarAssignmentHistory::$notSubmitted) {
                    $assignmentHistory->update([
                        'status' => WebinarAssignmentHistory::$pending
                    ]);
                }

                $notifyOptions = [
                    '[instructor.name]' => $assignmentHistory->instructor->full_name,
                    '[c.title]' => $webinar->title,
                    '[student.name]' => $assignmentHistory->student->full_name,
                    //'[assignment_grade]' => $assignmentHistory->grade,
                ];

                if ($user->id == $assignment->creator_id) {
                    sendNotification('instructor_send_message', $notifyOptions, $assignmentHistory->student_id);
                } else {
                    sendNotification('student_send_message', $notifyOptions, $assignmentHistory->instructor_id);
                }

                return apiResponse2(1, 'stored', trans('api.public.stored'),$ee);
            }
        }
        return apiResponse2(0, 'UserNotBought',"");
    }

    public function index($assignment_id)
    {
        $assignment = WebinarAssignment::find($assignment_id);
        $user_id = \request()->input('student_id');

        $rr = $this->getAssignmentHistory($assignment->webinar, $assignment, apiAuth(), $user_id);

        $resource = ($rr) ? WebinarAssignmentHistoryMessageResource::collection($rr->messages) : [];
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $resource);
    }

    private function getAssignmentDeadline($assignment, $user)
    {
        $deadline = true; // default can access

        if (!empty($assignment->deadline)) {
            $conditionDay = $assignment->getDeadlineTimestamp($user);

            if (time() > $conditionDay) {
                $deadline = false;
            } else {
                $deadline = round(($conditionDay - time()) / (60 * 60 * 24), 1);
            }
        }

        return $deadline;
    }


    private function getAssignmentHistory($course, $assignment, $user, $studentId = null)
    {
        $assignmentHistory = \App\Models\WebinarAssignmentHistory::where('instructor_id', $assignment->creator_id)
            ->where(function ($query) use ($user, $studentId) {
                if (!empty($studentId)) {
                    $query->where('student_id', $studentId);
                } else {
                    $query->where('student_id', $user->id);
                }
            })
            ->where('assignment_id', $assignment->id)
            ->with([
                'messages' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                    $query->whereHas('sender');
                    $query->with([
                        'sender'
                    ]);
                }
            ])->first();

        if (empty($assignmentHistory) and !$course->isOwner($user->id) and !$user->isAdmin()) {
            $assignmentHistory = WebinarAssignmentHistory::create([
                'instructor_id' => $assignment->creator_id,
                'student_id' => $user->id,
                'assignment_id' => $assignment->id,
                'status' => WebinarAssignmentHistory::$notSubmitted,
                'created_at' => time(),
            ]);
        }

        return $assignmentHistory;
    }


}
