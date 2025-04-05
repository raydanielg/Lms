<?php

namespace App\Http\Controllers\Web\traits;

use App\Models\CourseForum;
use App\Models\CourseForumAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait LearningPageForumTrait
{
    public function forum(Request $request, $slug)
    {
        $user = auth()->user();


        $course = $this->getCourse($slug, $user);

        if ($course == 'not_access') {
            abort(404);
        }

        $query = CourseForum::where('webinar_id', $course->id);


        $forums = $this->handleForumFilters($request, $query)->get();

        $course->forums_count = $forums->count();

        foreach ($forums as $forum) {
            $forum->answer_count = $forum->answers->count();

            $usersAvatars = [];

            if ($forum->answer_count > 0) {
                foreach ($forum->answers as $answer) {
                    if (!empty($answer->user) and count($usersAvatars) < 3 and empty($usersAvatars[$answer->user->id])) {
                        $usersAvatars[$answer->user->id] = $answer->user;
                    }
                }
            }

            $forum->usersAvatars = $usersAvatars;
            $forum->lastAnswer = $forum->answers->last();

            $forum->resolved = $forum->answers->where('resolved', true)->first();
        }

        $questionsCount = $course->forums->count();
        $resolvedCount = $course->forums()->whereHas('answers', function ($query) {
            $query->where('resolved', true);
        })->count();
        $openQuestionsCount = $course->forums()->whereDoesntHave('answers', function ($query) {
            $query->where('resolved', true);
        })->count();

        $courseForumsIds = CourseForum::where('webinar_id', $course->id)->pluck('id')->toArray();
        $commentsCount = CourseForumAnswer::whereIn('forum_id', $courseForumsIds)->count();
        $activeUsersCount = CourseForumAnswer::whereIn('forum_id', $courseForumsIds)->select(DB::raw('count(distinct user_id) as count'))->first()->count;


        $data = [
            'pageTitle' => $course->title,
            'pageDescription' => $course->seo_description,
            'course' => $course,
            'forums' => $forums,
            'isForumPage' => true,
            'dontAllowLoadFirstContent' => true,
            'user' => $user,
            'questionsCount' => $questionsCount,
            'resolvedCount' => $resolvedCount,
            'openQuestionsCount' => $openQuestionsCount,
            'commentsCount' => $commentsCount,
            'activeUsersCount' => $activeUsersCount,
        ];

        return view('web.default.course.learningPage.index', $data);
    }

    private function handleForumFilters(Request $request, $query)
    {
        $search = $request->get('search');

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%");
                $query->orWhere('description', 'like', "%$search%");
                $query->orWhereHas('answers', function ($query) use ($search) {
                    $query->where('description', 'like', "%$search%");
                });
            });
        }

        return $query;
    }

    public function forumStoreNewQuestion(Request $request, $slug)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, null);

        if ($course == 'not_access') {
            abort(404);
        }

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

        CourseForum::create([
            'webinar_id' => $course->id,
            'user_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'attach' => $data['attach'] ?? null,
            'pin' => false,
            'created_at' => time(),
        ]);

        if ($user->id != $course->creator_id and $user->id != $course->teacher_id) {
            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[c.title]' => $course->title,
            ];

            sendNotification('new_question_in_forum', $notifyOptions, $course->creator_id);
            sendNotification('new_question_in_forum', $notifyOptions, $course->teacher_id);
        }

        return response()->json([
            'code' => 200
        ]);
    }

    public function getForumForEdit($slug, $forumId)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, 'forums');

        if ($course == 'not_access') {
            abort(404);
        }

        $courseForum = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if (!empty($courseForum)) {
            $data = [
                'id' => $courseForum->id,
                'title' => $courseForum->title,
                'description' => $courseForum->description,
                'attach' => $courseForum->attach,
            ];

            return response()->json([
                'forum' => $data
            ]);
        }

        return response()->json([], 422);
    }

    public function updateForum(Request $request, $slug, $forumId)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, null);

        if ($course == 'not_access') {
            abort(404);
        }

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

        $courseForum = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if (!empty($courseForum)) {
            $courseForum->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'attach' => $data['attach'] ?? null,
            ]);

            return response()->json([
                'code' => 200
            ]);
        }

        return response()->json([], 422);
    }

    public function forumPinToggle(Request $request, $slug, $forumId)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, null);

        if ($course == 'not_access' or !$course->isOwner($user->id)) {
            return response()->json([], 422);
        }

        $courseForum = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id)
            ->first();

        if (!empty($courseForum)) {
            $courseForum->update([
                'pin' => !$courseForum->pin,
            ]);

            return response()->json([
                'code' => 200
            ]);
        }

        return response()->json([], 422);
    }

    public function forumDownloadAttach($slug, $forumId)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, null);

        if ($course == 'not_access') {
            return response()->json([], 422);
        }

        $courseForum = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id)
            ->first();

        if (!empty($courseForum) and !empty($courseForum->attach)) {
            $filePath = public_path($courseForum->attach);

            if (file_exists($filePath)) {
                $fileInfo = pathinfo($filePath);
                $type = (!empty($fileInfo) and !empty($fileInfo['extension'])) ? $fileInfo['extension'] : '';

                $fileName = str_replace(' ', '-', $courseForum->title);
                $fileName = str_replace('.', '-', $fileName);
                $fileName .= '.' . $type;

                $headers = array(
                    'Content-Type: application/' . $type,
                );

                return response()->download($filePath, $fileName, $headers);
            }
        }

        abort(404);
    }

    public function getForumAnswers($slug, $forumId)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, 'forums');

        if ($course == 'not_access') {
            abort(404);
        }

        $courseForum = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id)
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'full_name', 'avatar', 'avatar_settings', 'role_id', 'role_name');
                },
                'answers' => function ($query) {
                    $query->with([
                        'user' => function ($query) {
                            $query->select('id', 'full_name', 'avatar', 'avatar_settings', 'role_id', 'role_name');
                        }
                    ]);
                    $query->orderBy('pin', 'desc');
                    $query->orderBy('created_at', 'asc');
                }
            ])
            ->first();

        if (!empty($courseForum)) {
            $data = [
                'pageTitle' => $course->title,
                'pageDescription' => $course->seo_description,
                'course' => $course,
                'isForumAnswersPage' => true,
                'dontAllowLoadFirstContent' => true,
                'courseForum' => $courseForum,
                'user' => $user,
            ];

            return view('web.default.course.learningPage.index', $data);
        }

        abort(404);
    }

    public function storeForumAnswers(Request $request, $slug, $forumId)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, 'forums');

        if ($course == 'not_access') {
            return response()->json([], 422);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $courseForum = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id)
            ->first();

        if (!empty($courseForum)) {
            CourseForumAnswer::create([
                'forum_id' => $courseForum->id,
                'user_id' => $user->id,
                'description' => $data['description'],
                'pin' => false,
                'resolved' => false,
                'created_at' => time(),
            ]);

            if ($user->id != $courseForum->user_id) {
                $notifyOptions = [
                    '[u.name]' => $user->full_name,
                    '[c.title]' => $course->title,
                ];

                sendNotification('new_answer_in_forum', $notifyOptions, $courseForum->user_id);
            }

            return response()->json([
                'code' => 200
            ]);
        }

        return response()->json([], 422);
    }

    public function answerEdit(Request $request, $slug, $forumId, $answerId)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, 'forums');

        $courseForumQuery = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id);

        if (!$course->isOwner($user->id)) {
            $courseForumQuery = $courseForumQuery->where('user_id', $user->id);
        }

        $courseForum = $courseForumQuery->first();

        if (!empty($courseForum)) {
            $answer = CourseForumAnswer::where('forum_id', $courseForum->id)
                ->where('user_id', $user->id)
                ->where('id', $answerId)
                ->first();

            if (!empty($answer)) {

                return response()->json([
                    'code' => 200,
                    'answer' => $answer
                ]);
            }
        }

        return response()->json([], 422);
    }

    public function answerUpdate(Request $request, $slug, $forumId, $answerId)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, 'forums');

        if ($course == 'not_access') {
            return response()->json([], 422);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $courseForum = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id)
            ->first();

        if (!empty($courseForum)) {
            $answer = CourseForumAnswer::where('forum_id', $courseForum->id)
                ->where('user_id', $user->id)
                ->where('id', $answerId)
                ->first();

            if (!empty($answer)) {

                $answer->update([
                    'description' => $data['description']
                ]);

                return response()->json([
                    'code' => 200
                ]);
            }
        }

        return response()->json([], 422);
    }

    public function answerTogglePinOrResolved(Request $request, $slug, $forumId, $answerId, $togglePinOrResolved)
    {
        $user = auth()->user();

        $course = $this->getCourse($slug, $user, 'forums');

        if ($course == 'not_access' or (in_array($togglePinOrResolved, ['pin', 'un_pin']) and !$course->isOwner($user->id))) {
            return response()->json([], 422);
        }

        $courseForumQuery = CourseForum::where('id', $forumId)
            ->where('webinar_id', $course->id);

        if (!$course->isOwner($user->id)) {
            $courseForumQuery = $courseForumQuery->where('user_id', $user->id);
        }

        $courseForum = $courseForumQuery->first();

        if (!empty($courseForum)) {
            $answer = CourseForumAnswer::where('forum_id', $courseForum->id)
                ->where('id', $answerId)
                ->first();

            if (!empty($answer)) {
                $updateData = [];

                if ($togglePinOrResolved == 'pin') {
                    $updateData['pin'] = true;
                } else if ($togglePinOrResolved == 'un_pin') {
                    $updateData['pin'] = false;
                } else if ($togglePinOrResolved == 'mark_as_not_resolved') {
                    $updateData['resolved'] = false;
                } else if ($togglePinOrResolved == 'mark_as_resolved') {
                    $updateData['resolved'] = true;
                }

                if (!empty($updateData)) {
                    $answer->update($updateData);
                }

                return response()->json([
                    'code' => 200
                ]);
            }
        }

        return response()->json([], 422);
    }
}
