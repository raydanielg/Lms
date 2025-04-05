<?php
namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\CoursePersonalNote;
use Illuminate\Http\Request;

class CoursePersonalNotesController extends Controller
{
    public function show(Request $request , $id)
    {
        if (!empty(getFeaturesSettings('course_notes_status'))) {

            $user = apiAuth();

            $personalNote = CoursePersonalNote::query()
                ->where("user_id",$user->id)
                ->where('targetable_id', $id)
                ->first();

            if (!empty($personalNote)) {
                if (!empty($personalNote->attachment)) {
                    $attachment = $personalNote->attachment;
                    $filePath = public_path($attachment);

                    if (file_exists($filePath)) {
                        $extension = \Illuminate\Support\Facades\File::extension($filePath);
                        $fileName = "personal_note_{$personalNote->id}." . $extension;

                        $personalNote->attachment = url( $filePath . $fileName );
                    }
                }
                return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),$personalNote);
            }
        }
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),[]);
    }
    public function destroy( $id)
    {
        if (!empty(getFeaturesSettings('course_notes_status'))) {

            $personalNote = CoursePersonalNote::query()
                ->where('id', $id)
                ->first();

            if (!empty($personalNote)) {
                $personalNote->delete();

                return apiResponse2(1, 'retrieved', trans('api.public.retrieved'));
            }
        }

        return apiResponse2(0, 'error', trans('api.public.error'));
    }
    public function store(Request $request , $id)
    {
        $user = apiAuth();

        $data = $request->all();

        $type = "";

        switch ($data['item_type']) {
            case "session":
                $type = "App\Models\Session";
                break;

            case "file":
                $type = "App\Models\File";
                break;

            case "quiz":
                $type = "App\Models\Quiz";
                break;

            case "text_lesson":
                $type = "App\Models\TextLesson";
                break;

            case "assignment":
                $type = "App\Models\WebinarAssignment";
                break;
        }


        CoursePersonalNote::query()->updateOrCreate([
            'user_id' => $user->id,
            'course_id' => $data['course_id'],
            'targetable_id' => $data['item_id'],
            'targetable_type' => $type,
        ], [
            'note' => $data['note'] ?? null,
            'attachment' => null,
            'created_at' => time()
        ]);

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'));
    }
}
