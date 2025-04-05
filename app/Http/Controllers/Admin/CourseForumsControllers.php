<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseForum;
use App\Models\CourseForumAnswer;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseForumsControllers extends Controller
{
    public function index()
    {
        $this->authorize('admin_course_question_forum_list');

        $webinars = Webinar::select('id', 'category_id', 'teacher_id', 'slug')
            ->where('forum', true)
            ->withCount('forums')
            ->with([
                'teacher' => function ($query) {
                    $query->select('id', 'full_name');
                }
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.course_forum'),
            'webinars' => $webinars
        ];

        return view('admin.webinars.forum.course_lists', $data);
    }

    public function forums(Request $request, $webinar_id)
    {
        $this->authorize('admin_course_question_forum_list');

        $webinar = Webinar::findOrFail($webinar_id);


        $query = CourseForum::where('webinar_id', $webinar_id);

        $totalQuestions = deepClone($query)->count();
        $resolvedCount = deepClone($query)
            ->whereHas('answers', function ($query) {
                $query->where('resolved', true);
            })->count();
        $notResolvedCount = deepClone($query)
            ->whereDoesntHave('answers', function ($query) {
                $query->where('resolved', true);
            })->count();


        $forums = $this->handleForumFilters($request, $query)
            ->with([
                'answers' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'user' => function ($query) {
                    $query->select('id', 'full_name');
                }
            ])
            ->withCount('answers')
            ->orderBy('pin', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($forums as $forum) {
            if (!empty($forum->answers) and count($forum->answers)) {
                $forum->last_answer = $forum->answers->first();
                $forum->resolved = $forum->answers->where('resolved', true)->first();
            }
        }

        $data = [
            'pageTitle' => trans('update.course_forum'),
            'forums' => $forums,
            'webinar' => $webinar,
            'totalQuestions' => $totalQuestions,
            'resolvedCount' => $resolvedCount,
            'notResolvedCount' => $notResolvedCount,
        ];

        return view('admin.webinars.forum.question_lists', $data);
    }

    private function handleForumFilters($request, $query)
    {
        $title = $request->get('title');
        $date = $request->get('date');
        $status = $request->get('status');
        $userId = $request->get('user_id');


        if (!empty($title)) {
            $query->where('title', 'like', "%$title%");
        }

        if (!empty($date)) {
            $timestamp = strtotime($date);
            $beginOfDay = strtotime("today", $timestamp);
            $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;

            $query->whereBetween('created_at', [$beginOfDay, $endOfDay]);
        }

        if (!empty($userId)) {
            $query->where('user_id', $userId);
        }

        if (!empty($status)) {
            if ($status == 'pined') {
                $query->where('pin', true);
            } elseif ($status == 'not_resolved') {
                $query->whereDoesntHave('answers', function ($query) {
                    $query->where('resolved', true);
                });
            } elseif ($status == 'resolved') {
                $query->whereHas('answers', function ($query) {
                    $query->where('resolved', true);
                });
            }
        }

        return $query;
    }

    public function answers($webinar_id, $forum_id)
    {
        $this->authorize('admin_course_question_forum_answers');

        $course = Webinar::findOrFail($webinar_id);

        $question = CourseForum::findOrFail($forum_id);

        $answers = CourseForumAnswer::where('forum_id', $forum_id)
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'full_name');
                }
            ])->orderBy('pin', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'pageTitle' => trans('public.answers'),
            'question' => $question,
            'answers' => $answers,
            'course' => $course,
        ];

        return view('admin.webinars.forum.answers_lists', $data);
    }

    public function forumEdit($webinar_id, $forum_id)
    {
        $this->authorize('admin_course_question_forum_list');

        $question = CourseForum::findOrFail($forum_id);

        return response()->json([
            'code' => 200,
            'post' => $question
        ]);
    }

    public function forumDelete($webinar_id, $forum_id)
    {
        $this->authorize('admin_course_question_forum_list');

        $question = CourseForum::findOrFail($forum_id);

        $question->delete();

        return redirect(getAdminPanelUrl("/webinars/$webinar_id/forums"));
    }

    public function forumUpdate(Request $request, $webinar_id, $forum_id)
    {
        $this->authorize('admin_course_question_forum_list');

        $question = CourseForum::findOrFail($forum_id);

        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $question->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'attach' => $data['attach'] ?? null,
        ]);

        return response()->json([
            'code' => 200,
        ]);
    }

    public function answerEdit($webinar_id, $forum_id, $id)
    {
        $this->authorize('admin_course_question_forum_list');

        $answer = CourseForumAnswer::where('forum_id', $forum_id)
            ->where('id', $id)
            ->first();

        if (!empty($answer)) {
            return response()->json([
                'code' => 200,
                'post' => $answer
            ]);
        }

        abort(404);
    }

    public function answerDelete($webinar_id, $forum_id, $id)
    {
        $this->authorize('admin_course_question_forum_list');

        $answer = CourseForumAnswer::where('forum_id', $forum_id)
            ->where('id', $id)
            ->first();

        if (!empty($answer)) {
            $answer->delete();

            return redirect()->back();
        }

        abort(404);
    }

    public function answerUpdate(Request $request, $webinar_id, $forum_id, $id)
    {
        $this->authorize('admin_course_question_forum_list');

        $answer = CourseForumAnswer::where('forum_id', $forum_id)
            ->where('id', $id)
            ->first();

        if (!empty($answer)) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'description' => 'required',
            ]);

            if ($validator->fails()) {
                return response([
                    'code' => 422,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $answer->update([
                'description' => $data['description'],
            ]);

            return response()->json([
                'code' => 200,
            ]);
        }

        abort(404);
    }
}
