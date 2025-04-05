<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Sale;
use App\Models\Translation\WebinarAssignmentTranslation;
use App\Models\Webinar;
use App\Models\WebinarAssignment;
use App\Models\WebinarAssignmentAttachment;
use App\Models\WebinarAssignmentHistory;
use App\Models\WebinarChapterItem;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_webinar_assignments_lists');

        $query = WebinarAssignment::query();

        $courseAssignmentsCount = deepClone($query)->count();

        $pendingReviewCount = deepClone($query)->whereHas('assignmentHistory', function ($query) {
            $query->where('status', WebinarAssignmentHistory::$pending);
        })->count();

        $passedCount = deepClone($query)->whereHas('assignmentHistory', function ($query) {
            $query->where('status', WebinarAssignmentHistory::$passed);
        })->count();

        $failedCount = deepClone($query)->whereHas('assignmentHistory', function ($query) {
            $query->where('status', WebinarAssignmentHistory::$notPassed);
        })->count();

        $query = $this->handleAssignmentsFilters($request, $query);

        $assignments = $query->with([
            'webinar',
            'instructorAssignmentHistories' => function ($query) {
                $query->orderBy('created_at', 'desc');
                $query->with([
                    'messages' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }
                ]);
            },
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.assignments'),
            'assignments' => $assignments,
            'courseAssignmentsCount' => $courseAssignmentsCount,
            'pendingReviewCount' => $pendingReviewCount,
            'passedCount' => $passedCount,
            'failedCount' => $failedCount,
        ];

        $webinar_ids = $request->get('webinar_ids');
        if (!empty($webinar_ids)) {
            $data['webinars'] = Webinar::select('id')->whereIn('id', $webinar_ids)->get();
        }

        return view('admin.webinars.assignments.lists', $data);
    }

    private function handleAssignmentsFilters(Request $request, $query)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $webinar_ids = $request->get('webinar_ids');
        $student_ids = $request->get('student_ids');
        $status = $request->get('status', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($webinar_ids)) {
            $query->whereIn('webinar_id', $webinar_ids);
        }

        if (!empty($student_ids)) {
            $query->whereIn('student_id', $student_ids);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function students(Request $request, $id)
    {
        $this->authorize('admin_webinar_assignments_students');

        $assignment = WebinarAssignment::findOrFail($id);

        $webinar = $assignment->webinar;

        $query = $assignment->instructorAssignmentHistories()
            ->with([
                'student'
            ]);

        $pendingReviewCount = deepClone($query)->where('status', WebinarAssignmentHistory::$pending)->count();
        $passedCount = deepClone($query)->where('status', WebinarAssignmentHistory::$passed)->count();
        $failedCount = deepClone($query)->where('status', WebinarAssignmentHistory::$notPassed)->count();

        $query = $this->handleAssignmentsFilters($request, $query);

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

        $data = [
            'pageTitle' => trans('update.students_assignments'),
            'assignment' => $assignment,
            'histories' => $histories,
            'webinar' => $webinar,
            'pendingReviewCount' => $pendingReviewCount,
            'passedCount' => $passedCount,
            'failedCount' => $failedCount,
        ];

        $student_ids = $request->get('student_ids');
        if (!empty($student_ids)) {
            $data['students'] = User::select('id', 'full_name')->whereIn('id', $student_ids)->get();
        }

        return view('admin.webinars.assignments.students', $data);
    }

    public function conversations($assignmentId, $historyId)
    {
        $this->authorize('admin_webinar_assignments_conversations');

        $assignment = WebinarAssignment::findOrFail($assignmentId);

        $history = WebinarAssignmentHistory::where('assignment_id', $assignmentId)
            ->where('id', $historyId)
            ->with([
                'messages' => function ($query) {
                    $query->with([
                        'sender'
                    ]);
                }
            ])
            ->first();

        if (!empty($history)) {
            $data = [
                'pageTitle' => trans('update.assignment_conversations'),
                'assignment' => $assignment,
                'conversations' => $history->messages,
            ];

            return view('admin.webinars.assignments.conversation', $data);
        }

        abort(404);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required|max:255',
            'description' => 'required',
            'grade' => 'required|integer',
            'pass_grade' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
            $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
            $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
        } else {
            $data['check_previous_parts'] = false;
            $data['access_after_day'] = null;
        }

        $webinar = Webinar::where('id', $data['webinar_id'])->first();

        if (!empty($webinar)) {
            $assignment = WebinarAssignment::create([
                'creator_id' => $webinar->creator_id,
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

            if ($assignment) {
                WebinarAssignmentTranslation::updateOrCreate([
                    'webinar_assignment_id' => $assignment->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);


                $this->handleAttachments($data['attachments'], $webinar->creator_id, $assignment->id);

                if (!empty($assignment->chapter_id)) {
                    WebinarChapterItem::makeItem($webinar->creator_id, $assignment->chapter_id, $assignment->id, WebinarChapterItem::$chapterAssignment);
                }
            }

            return response()->json([
                'code' => 200,
            ], 200);
        }

        return response()->json([], 422);
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $assignment = WebinarAssignment::where('id', $id)->first();

        if (!empty($assignment)) {
            $locale = $request->get('locale', app()->getLocale());
            if (empty($locale)) {
                $locale = app()->getLocale();
            }
            storeContentLocale($locale, $assignment->getTable(), $assignment->id);

            $assignment->title = $assignment->getTitleAttribute();
            $assignment->description = $assignment->getDescriptionAttribute();
            $assignment->attachments = $assignment->attachments->toArray();
            $assignment->locale = mb_strtoupper($locale);
        }

        return response()->json([
            'assignment' => $assignment
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required|max:255',
            'description' => 'required',
            'grade' => 'required|integer',
            'pass_grade' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
            $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
            $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
        } else {
            $data['check_previous_parts'] = false;
            $data['access_after_day'] = null;
        }

        $assignment = WebinarAssignment::where('id', $id)
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

            removeContentLocale();

            return response()->json([
                'code' => 200,
            ], 200);
        }

        removeContentLocale();

        return response()->json([], 422);
    }

    public function destroy($id)
    {
        $this->authorize('admin_webinars_edit');

        $assignment = WebinarAssignment::where('id', $id)->first();

        if (!empty($assignment)) {
            WebinarChapterItem::where('user_id', $assignment->creator_id)
                ->where('item_id', $assignment->id)
                ->where('type', WebinarChapterItem::$chapterAssignment)
                ->delete();

            $assignment->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
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
}
