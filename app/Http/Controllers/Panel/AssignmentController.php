<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Sale;
use App\Models\Translation\FileTranslation;
use App\Models\Translation\WebinarAssignmentTranslation;
use App\Models\Webinar;
use App\Models\WebinarAssignment;
use App\Models\WebinarAssignmentAttachment;
use App\Models\WebinarAssignmentHistory;
use App\Models\WebinarAssignmentHistoryMessage;
use App\Models\WebinarChapterItem;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    public function myAssignments(Request $request)
    {
        $this->authorize("panel_assignments_lists");

        if (!getFeaturesSettings('webinar_assignment_status')) {
            abort(403);
        }

        $user = auth()->user();

        $purchasedCoursesIds = Sale::where('buyer_id', $user->id)
            ->whereNotNull('webinar_id')
            ->whereNull('refund_at')
            ->pluck('webinar_id')
            ->toArray();

        $webinars = Webinar::select('id', 'creator_id', 'teacher_id')
            ->whereIn('id', $purchasedCoursesIds)
            ->where('status', 'active')
            ->get();

        $query = WebinarAssignment::whereIn('webinar_id', $purchasedCoursesIds)
            ->where('status', 'active');

        $courseAssignmentsCount = deepClone($query)->count();

        $pendingReviewCount = deepClone($query)->whereHas('assignmentHistory', function ($query) use ($user) {
            $query->where('student_id', $user->id);
            $query->where('status', WebinarAssignmentHistory::$pending);
        })->count();

        $passedCount = deepClone($query)->whereHas('assignmentHistory', function ($query) use ($user) {
            $query->where('student_id', $user->id);
            $query->where('status', WebinarAssignmentHistory::$passed);
        })->count();

        $failedCount = deepClone($query)->whereHas('assignmentHistory', function ($query) use ($user) {
            $query->where('student_id', $user->id);
            $query->where('status', WebinarAssignmentHistory::$notPassed);
        })->count();


        $query = $this->handleMyAssignmentsFilters($request, $query, $user);

        $assignments = $query->with([
            'webinar',
            'assignmentHistory' => function ($query) use ($user) {
                $query->where('student_id', $user->id);
                $query->with([
                    'messages' => function ($query) use ($user) {
                        $query->where('sender_id', $user->id);
                        $query->orderBy('created_at', 'desc');
                    }
                ]);
            },
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($assignments as &$assignment) {
            $this->getAssignmentDeadline($assignment, $user);
            $assignment->usedAttemptsCount = 0;

            if (!empty($assignment->assignmentHistory) and count($assignment->assignmentHistory->messages)) {
                try {
                    $assignment->last_submission = $assignment->assignmentHistory->messages->first()->created_at;
                    $assignment->first_submission = $assignment->assignmentHistory->messages->last()->created_at;
                    $assignment->usedAttemptsCount = $assignment->assignmentHistory->messages->count();
                } catch (\Exception $exception) {

                }
            }
        }

        $data = [
            'pageTitle' => trans('update.my_assignments'),
            'assignments' => $assignments,
            'webinars' => $webinars,
            'courseAssignmentsCount' => $courseAssignmentsCount,
            'pendingReviewCount' => $pendingReviewCount,
            'passedCount' => $passedCount,
            'failedCount' => $failedCount,
        ];

        return view('web.default.panel.assignments.my-assignments', $data);
    }

    private function handleMyAssignmentsFilters(Request $request, $query, $user)
    {
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

    private function getAssignmentDeadline(&$assignment, $user)
    {
        if (!empty($assignment->deadline)) {
            $sale = Sale::where('buyer_id', $user->id)
                ->where('webinar_id', $assignment->webinar_id)
                ->whereNull('refund_at')
                ->first();

            $assignment->deadlineTime = strtotime("+{$assignment->deadline} days", $sale->created_at);
        }
    }

    public function myCoursesAssignments(Request $request)
    {
        $this->authorize("panel_assignments_my_courses_assignments");

        if (!getFeaturesSettings('webinar_assignment_status')) {
            abort(403);
        }

        $user = auth()->user();

        if (!$user->isOrganization() and !$user->isTeacher()) {
            abort(404);
        }

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
            ->paginate(10);

        foreach ($assignments as &$assignment) {
            $grades = $assignment->instructorAssignmentHistories->filter(function ($item) {
                return !is_null($item->grade);
            });

            $historyIds = $assignment->instructorAssignmentHistories->pluck('id')->toArray();

            $assignment->min_grade = count($grades) ? $grades->min('grade') : null;
            $assignment->average_grade = count($grades) ? $grades->avg('grade') : null;
            $assignment->submissions = WebinarAssignmentHistoryMessage::whereIn('assignment_history_id', $historyIds)
                ->where('sender_id', '!=', $user->id)
                ->count();

            $assignment->pendingCount = $assignment->instructorAssignmentHistories->where('status', WebinarAssignmentHistory::$pending)->count();
            $assignment->passedCount = $assignment->instructorAssignmentHistories->where('status', WebinarAssignmentHistory::$passed)->count();
            $assignment->failedCount = $assignment->instructorAssignmentHistories->where('status', WebinarAssignmentHistory::$notPassed)->count();
        }

        $data = [
            'pageTitle' => trans('update.my_courses_assignments'),
            'assignments' => $assignments,
            'courseAssignmentsCount' => $courseAssignmentsCount,
            'pendingReviewCount' => $pendingReviewCount,
            'passedCount' => $passedCount,
            'failedCount' => $failedCount,
        ];

        return view('web.default.panel.assignments.my-courses-assignments', $data);
    }

    public function students(Request $request, $id)
    {
        $this->authorize("panel_assignments_students");

        if (!getFeaturesSettings('webinar_assignment_status')) {
            abort(403);
        }

        $user = auth()->user();

        if (!$user->isOrganization() and !$user->isTeacher()) {
            abort(404);
        }

        $assignment = WebinarAssignment::where('id', $id)
            ->where('creator_id', $user->id)
            ->with([
                'webinar',
            ])
            ->first();

        if (!empty($assignment)) {
            $webinar = $assignment->webinar;

            $query = $assignment->instructorAssignmentHistories()
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

            $query = $this->handleAssignmentStudentsFilters($request, $query);

            $histories = $query->orderBy('created_at', 'desc')
                ->paginate(10);

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

            $studentsIds = Sale::where('webinar_id', $webinar->id)
                ->whereNull('refund_at')
                ->pluck('buyer_id')
                ->toArray();

            $students = User::select('id', 'full_name')
                ->whereIn('id', $studentsIds)
                ->get();

            $data = [
                'pageTitle' => trans('update.students_assignments'),
                'assignment' => $assignment,
                'histories' => $histories,
                'students' => $students,
                'webinar' => $webinar,
                'courseAssignmentsCount' => $courseAssignmentsCount,
                'pendingReviewCount' => $pendingReviewCount,
                'passedCount' => $passedCount,
                'failedCount' => $failedCount,
            ];

            return view('web.default.panel.assignments.students', $data);
        }

        abort(404);
    }

    private function handleAssignmentStudentsFilters(Request $request, $query)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $studentId = $request->get('student_id');
        $status = $request->get('status');

        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($studentId)) {
            $query->where('student_id', $studentId);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $rules = [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required|max:255',
            'description' => 'required',
            'grade' => 'required|integer',
            'pass_grade' => 'required|integer',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($webinar) and $webinar->canAccess($user)) {

            if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
                $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
                $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
            } else {
                $data['check_previous_parts'] = false;
                $data['access_after_day'] = null;
            }

            $assignment = WebinarAssignment::create([
                'creator_id' => $user->id,
                'webinar_id' => $data['webinar_id'],
                'chapter_id' => $data['chapter_id'],
                'grade' => $data['grade'] ?? null,
                'pass_grade' => $data['pass_grade'] ?? null,
                'deadline' => $data['deadline'] ?? null,
                'attempts' => $data['attempts'] ?? null,
                'check_previous_parts' => $data['check_previous_parts'],
                'access_after_day' => $data['access_after_day'],
                'status' => (!empty($data['status']) and $data['status'] == 'on') ? File::$Active : File::$Inactive,
                'created_at' => time(),
            ]);

            if (!empty($assignment)) {
                WebinarAssignmentTranslation::updateOrCreate([
                    'webinar_assignment_id' => $assignment->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);

                $this->handleAttachments($data['attachments'], $user->id, $assignment->id);

                WebinarChapterItem::makeItem($assignment->creator_id, $assignment->chapter_id, $assignment->id, WebinarChapterItem::$chapterAssignment);
            }

            return response()->json([
                'code' => 200,
            ], 200);
        }

        abort(403);
    }

    private function handleAttachments($attachments, $creatorId, $assignmentId)
    {
        WebinarAssignmentAttachment::where('creator_id', $creatorId)
            ->where('assignment_id', $assignmentId)
            ->delete();

        if (!empty($attachments) and count($attachments)) {
            foreach ($attachments as $attachment) {
                if (!empty($attachment['title']) and !empty($attachment['attach'])) {
                    WebinarAssignmentAttachment::create([
                        'creator_id' => $creatorId,
                        'assignment_id' => $assignmentId,
                        'title' => $attachment['title'],
                        'attach' => $attachment['attach'],
                    ]);
                }
            }
        }
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $rules = [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required|max:255',
            'description' => 'required',
            'grade' => 'required|integer',
            'pass_grade' => 'required|integer',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($webinar) and $webinar->canAccess($user)) {
            if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
                $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
                $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
            } else {
                $data['check_previous_parts'] = false;
                $data['access_after_day'] = null;
            }

            $assignment = WebinarAssignment::where('id', $id)
                ->where(function ($query) use ($user, $webinar) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere('webinar_id', $webinar->id);
                })
                ->first();

            if (!empty($assignment)) {
                $changeChapter = ($data['chapter_id'] != $assignment->chapter_id);
                $oldChapterId = $assignment->chapter_id;

                $assignment->update([
                    'chapter_id' => $data['chapter_id'],
                    'grade' => $data['grade'] ?? null,
                    'pass_grade' => $data['pass_grade'] ?? null,
                    'deadline' => $data['deadline'] ?? null,
                    'attempts' => $data['attempts'] ?? null,
                    'check_previous_parts' => $data['check_previous_parts'],
                    'access_after_day' => $data['access_after_day'],
                    'status' => (!empty($data['status']) and $data['status'] == 'on') ? File::$Active : File::$Inactive,
                ]);

                if ($changeChapter) {
                    WebinarChapterItem::changeChapter($assignment->creator_id, $oldChapterId, $assignment->chapter_id, $assignment->id, WebinarChapterItem::$chapterAssignment);
                }

                WebinarAssignmentTranslation::updateOrCreate([
                    'webinar_assignment_id' => $assignment->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);

                $this->handleAttachments($data['attachments'], $assignment->creator_id, $assignment->id);

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        abort(403);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        $assignments = WebinarAssignment::where('id', $id)->first();

        if (!empty($assignments)) {
            $webinar = Webinar::query()->find($assignments->webinar_id);

            if ($assignments->creator_id == $user->id or (!empty($webinar) and $webinar->canAccess($user))) {

                WebinarChapterItem::where('user_id', $assignments->creator_id)
                    ->where('item_id', $assignments->id)
                    ->where('type', WebinarChapterItem::$chapterAssignment)
                    ->delete();

                $assignments->delete();
            }
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
