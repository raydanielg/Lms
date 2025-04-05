<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuizResource;
use App\Models\Api\Quiz;
use App\Models\Api\Webinar;
use Illuminate\Http\Request;

class WebinarContentController extends Controller
{
    public function quizzes($webinar_id)
    {
        $quizzes = Quiz::where('webinar_id', $webinar_id)->where('status', 'active')->get();

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), QuizResource::collection($quizzes));
    }

    public function certificates($webinar_id)
    {
        $webinar = Webinar::find($webinar_id);

        $quizzes = Quiz::with('webinar')->where('webinar_id', $webinar_id)->where('status', 'active')
            ->where('certificate', 1)->get();
        $certificates = $quizzes->map(function ($quiz) {
            return [
                'type' => 'quiz',
                'link' => route('quiz.show', $quiz->id),
                'title' => $quiz->title,
                'created_at' => $quiz->created_at,

            ];
        });
        if ($webinar->certificate == 1) {
            $certificates->push([
                'type' => 'completion',
                'title' => $webinar->title,
                'created_at' => $webinar->created_at,

            ]);
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $certificates);
    }
}
