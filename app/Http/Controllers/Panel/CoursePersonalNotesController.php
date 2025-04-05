<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\CoursePersonalNote;

class CoursePersonalNotesController extends Controller
{
    public function index()
    {
        if (!empty(getFeaturesSettings('course_notes_status'))) {
            $user = auth()->user();

            $personalNotes = CoursePersonalNote::query()->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $data = [
                'pageTitle' => trans('update.course_notes'),
                'personalNotes' => $personalNotes,
            ];

            return view('web.default.panel.webinar.personal_notes', $data);
        }

        abort(404);
    }



    public function delete($id)
    {
        if (!empty(getFeaturesSettings('course_notes_status'))) {
            $user = auth()->user();

            $personalNote = CoursePersonalNote::query()->where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!empty($personalNote)) {
                $personalNote->delete();

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        abort(404);
    }
}
