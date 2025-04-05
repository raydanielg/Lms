<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoursePersonalNote;
use Illuminate\Http\Request;

class CoursePersonalNotesController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize("admin_course_personal_notes");

        if (!empty(getFeaturesSettings('course_notes_status'))) {
            $query = CoursePersonalNote::query()
                ->whereNotNull('note');

            $personalNotes = $this->handleFilters($request, $query)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $data = [
                'pageTitle' => trans('update.course_notes'),
                'personalNotes' => $personalNotes,
            ];

            return view('admin.webinars.personal_notes.index', $data);
        }

        abort(404);
    }

    private function handleFilters(Request $request, $query)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $search = $request->get('search');
        $content_type = $request->get('content_type');

        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {
            $query->whereHas('course', function ($query) use ($search) {
                $query->whereTranslationLike('title', "%$search%");
            });
        }

        if (!empty($content_type)) {
            $query->whereHas('course', function ($query) use ($content_type) {
                $query->where('type', $content_type);
            });
        }

        return $query;
    }

    public function downloadAttachment($id)
    {
        $this->authorize("admin_course_personal_notes");

        if (!empty(getFeaturesSettings('course_notes_status'))) {

            $personalNote = CoursePersonalNote::query()
                ->where('id', $id)
                ->first();

            if (!empty($personalNote) and !empty($personalNote->attachment)) {
                $attachment = $personalNote->attachment;
                $filePath = public_path($attachment);

                if (file_exists($filePath)) {
                    $extension = \Illuminate\Support\Facades\File::extension($filePath);
                    $fileName = "personal_note_{$personalNote->id}." . $extension;

                    $headers = array(
                        'Content-Type: application/*',
                    );

                    return response()->download($filePath, $fileName, $headers);
                }
            }
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("admin_course_personal_notes");

        $personalNote = CoursePersonalNote::query()
            ->where('id', $id)
            ->first();

        if (!empty($personalNote)) {
            $data = $request->all();

            $personalNote->update([
                'note' => $data['note'] ?? null,
                'attachment' => $data['attachment'] ?? null,
            ]);

            return response()->json([
                'code' => 200,
                'title' => trans('public.request_success'),
                'msg' => trans('update.personal_note_stored_successfully'),
            ]);
        }

        return response()->json([], 422);
    }

    public function delete($id)
    {
        $this->authorize("admin_course_personal_notes");

        if (!empty(getFeaturesSettings('course_notes_status'))) {

            $personalNote = CoursePersonalNote::query()
                ->where('id', $id)
                ->first();

            if (!empty($personalNote)) {
                $personalNote->delete();

                return back();
            }
        }

        abort(404);
    }
}
