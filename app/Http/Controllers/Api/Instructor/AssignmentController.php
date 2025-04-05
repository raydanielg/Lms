<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebinarAssignmentHistoryResource;
use App\Http\Resources\WebinarAssignmentResource;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Sale;
use App\Models\Api\WebinarAssignment;
use App\Models\Api\WebinarAssignmentHistory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        if (!getFeaturesSettings('webinar_assignment_status')) {
            abort(403);
        }

        $user = apiAuth();

        $query = WebinarAssignment::where('creator_id', $user->id);

        $courseAssignmentsCount = deepClone($query)->count();

        $pendingReviewCount = deepClone($query)->whereHas('instructorAssignmentHistories', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
            $query->where('status', WebinarAssignmentHistory::$pending);
        })->count();

        $passedCount = deepClone($query)->whereHas('instructorAssignmentHistories', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
            $query->where('status', WebinarAssignmentHistory::$passed);
        })->count();

        $failedCount = deepClone($query)->whereHas('instructorAssignmentHistories', function ($query) use ($user) {
            $query->where('instructor_id', $user->id);
            $query->where('status', WebinarAssignmentHistory::$notPassed);
        })->count();

        $assignments = $query->with([
            'webinar',
            'instructorAssignmentHistories' => function ($query) use ($user) {
                $query->where('instructor_id', $user->id);
            },
        ])->orderBy('created_at', 'desc')
            ->get();

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [
                'course_assignments_count' => $courseAssignmentsCount,
                'pending_reviews_count' => $pendingReviewCount,
                'passed_count' => $passedCount,
                'failed_count' => $failedCount,
                'assignments' => WebinarAssignmentResource::collection($assignments),

            ]);

    }

    public function students(Request $request)
    {
        if (!getFeaturesSettings('webinar_assignment_status')) {
            abort(403);
        }

        $user = apiAuth();

        $assignment = WebinarAssignment::where('creator_id', $user->id)
            // ->where('creator_id', $user->id)
            ->with([
                'webinar',
            ])
            ->first();

        if (!empty($assignment)) {
            $webinar = $assignment->webinar;

            $query = $assignment->assignmentHistory()
                ->where('instructor_id', $user->id)
                ->where('student_id', '!=', $user->id)
                ->with([
                    'student'
                ]);

            $courseAssignmentsCount = WebinarAssignment::where('creator_id', $user->id)
                ->where('webinar_id', $webinar->id)
                ->count();

            $pendingReviewCount = deepClone($query)->where('status', WebinarAssignmentHistory::$pending)->count();
            $passedCount = deepClone($query)->where('status', WebinarAssignmentHistory::$passed)->count();
            $failedCount = deepClone($query)->where('status', WebinarAssignmentHistory::$notPassed)->count();


            $histories = $query->orderBy('created_at', 'desc')
                ->get();
            //  dd($histories);
            foreach ($histories as &$history) {
                $history->usedAttemptsCount = 0;

                $sale = Sale::where('buyer_id', $history->student_id)
                    ->where('webinar_id', $assignment->webinar_id)
                    ->whereNull('refund_at')
                    ->first();

                if (!empty($sale)) {
                    $history->purchase_date = $sale->created_at;
                }

                if (!empty($history) and count($history->messages)) {
                    try {
                        $history->last_submission = $history->messages->first()->created_at;
                        $history->first_submission = $history->messages->last()->created_at;
                        $history->usedAttemptsCount = $history->messages->count();
                    } catch (\Exception $exception) {

                    }
                }
            }
            $resource = WebinarAssignmentHistoryResource::collection($histories);
            //  dd($resource->groupBy('id')) ;
            //  $resource=$resource->groupBy('student_id')

            $data = [
                'pageTitle' => trans('update.students_assignments'),
                'assignment' => $assignment,
                'histories' => $histories,

                'webinar' => $webinar,
                'courseAssignmentsCount' => $courseAssignmentsCount,
                'pendingReviewCount' => $pendingReviewCount,
                'passedCount' => $passedCount,
                'failedCount' => $failedCount,
            ];

            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
                'assignment_histories' => $resource,
                'count' => $courseAssignmentsCount,
                'pending_count' => $pendingReviewCount,
                'passed_count' => $passedCount,
                'failed_count' => $failedCount,

            ]);

            //  return view('web.default.panel.assignments.students', $data);
        }

        abort(404);
    }

    public function submmision(Request $request,$id)
    {
        if (!getFeaturesSettings('webinar_assignment_status')) {
            abort(403);
        }

        $user = apiAuth();

        $assignment = WebinarAssignment::where('creator_id', $user->id)
            ->where('id',$id)
            // ->where('creator_id', $user->id)
            ->with([
                'webinar',
            ])
            ->first();

        if (!empty($assignment)) {
            $webinar = $assignment->webinar;

            $query = $assignment->assignmentHistory()
                ->where('instructor_id', $user->id)
                ->where('student_id', '!=', $user->id)
                ->with([
                    'student'
                ]);

            $courseAssignmentsCount = WebinarAssignment::where('creator_id', $user->id)
                ->where('webinar_id', $webinar->id)
                ->count();

            $pendingReviewCount = deepClone($query)->where('status', WebinarAssignmentHistory::$pending)->count();
            $passedCount = deepClone($query)->where('status', WebinarAssignmentHistory::$passed)->count();
            $failedCount = deepClone($query)->where('status', WebinarAssignmentHistory::$notPassed)->count();


            $histories = $query->orderBy('created_at', 'desc')
                ->get();
            //  dd($histories);
            foreach ($histories as &$history) {
                $history->usedAttemptsCount = 0;

                $sale = Sale::where('buyer_id', $history->student_id)
                    ->where('webinar_id', $assignment->webinar_id)
                    ->whereNull('refund_at')
                    ->first();

                if (!empty($sale)) {
                    $history->purchase_date = $sale->created_at;
                }

                if (!empty($history) and count($history->messages)) {
                    try {
                        $history->last_submission = $history->messages->first()->created_at;
                        $history->first_submission = $history->messages->last()->created_at;
                        $history->usedAttemptsCount = $history->messages->count();
                    } catch (\Exception $exception) {

                    }
                }
            }
            $resource = WebinarAssignmentHistoryResource::collection($histories);
            //  dd($resource->groupBy('id')) ;
            //  $resource=$resource->groupBy('student_id')

            $data = [
                'pageTitle' => trans('update.students_assignments'),
                'assignment' => $assignment,
                'histories' => $histories,

                'webinar' => $webinar,
                'courseAssignmentsCount' => $courseAssignmentsCount,
                'pendingReviewCount' => $pendingReviewCount,
                'passedCount' => $passedCount,
                'failedCount' => $failedCount,
            ];

            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $resource) ;

            //  return view('web.default.panel.assignments.students', $data);
        }

        abort(404);
    }


    public function setGrade(Request $request, $historyId)
    {
        $user = apiAuth();
        validateParam($request->all(), [
            'grade' => 'required|integer',
        ]);

        $assignmentHistory = WebinarAssignmentHistory::where('id', $historyId)->first();
        abort_unless($assignmentHistory, 404);
        $assignment = $assignmentHistory->assignment;
        $webinar = $assignment->webinar;
        $data = $request->all();
        $grade = $data['grade'];

        $status = WebinarAssignmentHistory::$passed;

        if ($grade < $assignment->pass_grade) {
            $status = WebinarAssignmentHistory::$notPassed;
        }

        $assignmentHistory->update([
            'status' => $status,
            'grade' => $grade
        ]);

        if ($status == WebinarAssignmentHistory::$passed) {
            $buyStoreReward = RewardAccounting::calculateScore(Reward::PASS_ASSIGNMENT);
            RewardAccounting::makeRewardAccounting($assignmentHistory->student_id, $buyStoreReward, Reward::PASS_ASSIGNMENT, $assignment->id);
        }

        $notifyOptions = [
            '[instructor.name]' => $assignmentHistory->instructor->full_name,
            '[c.title]' => $webinar->title,
            '[student.name]' => $assignmentHistory->student->full_name,
            '[assignment_grade]' => $assignmentHistory->grade,
        ];

        sendNotification('instructor_set_grade', $notifyOptions, $assignmentHistory->student_id);

        return apiResponse2(1, 'stored', trans('api.public.stored'));

    }


}
