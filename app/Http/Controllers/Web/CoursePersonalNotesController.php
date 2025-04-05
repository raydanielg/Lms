<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CoursePersonalNote;
use Illuminate\Http\Request;

class CoursePersonalNotesController extends Controller
{

    public function downloadAttachment($id)
    {
        $user = auth()->user();

        if (!empty($user) and !empty(getFeaturesSettings('course_notes_status'))) {

            $personalNote = CoursePersonalNote::query()->where('user_id', $user->id)
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

}
