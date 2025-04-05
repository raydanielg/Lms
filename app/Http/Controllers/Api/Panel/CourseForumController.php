<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\WebinarForumResource;
use App\Models\Api\Webinar;
use App\Models\Api\CourseForum;
use App\Models\CourseForumAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseForumController extends Controller
{

    public function index(Webinar $webinar)
    {
        $user = apiAuth();
        $this->authorizeForUser($user, 'view', $webinar);

        $webinar->load(['forums', 'forums.answers']);

        $forums_query = CourseForum::withCount('answers')->with('answers')->where('webinar_id', $webinar->id);
        $forums = $forums_query->get();
        $filterd = $forums_query->handleFilters()->get();

        $courseForumsIds = CourseForum::where('webinar_id', $webinar->id)->pluck('id')->toArray();
        $commentsCount = CourseForumAnswer::whereIn('forum_id', $courseForumsIds)->count();
        $activeUsersCount = CourseForumAnswer::whereIn('forum_id', $courseForumsIds)->select(DB::raw('count(distinct user_id) as count'))->first()->count;


        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'forums' => WebinarForumResource::collection($filterd),
            'questions_count' => $webinar->forums->count(),
            'resolved_count' => $webinar->forums()->whereHas('answers', function ($query) {
                $query->where('resolved', true);
            })->count(),
            'open_questions_count' => $webinar->forums()->whereDoesntHave('answers', function ($query) {
                $query->where('resolved', true);
            })->count(),
            'comments_count' => $webinar->forums()->has('answers')->count(),
            'active_users_count' => $activeUsersCount,


        ]);

//        $course->forums_count = $forums->count();


    }

    public function store(Request $request, Webinar $webinar)
    {
        $user = apiAuth();
        $this->authorizeForUser($user, 'view', $webinar);

        validateParam($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        CourseForum::create([
            'webinar_id' => $webinar->id,
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'attach' => $request->file('attachment'),
            'pin' => false,
            'created_at' => time(),
        ]);

        if ($user->id != $webinar->creator_id and $user->id != $webinar->teacher_id) {
            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[c.title]' => $webinar->title,
            ];

            sendNotification('new_question_in_forum', $notifyOptions, $webinar->creator_id);
            sendNotification('new_question_in_forum', $notifyOptions, $webinar->teacher_id);
        }
        return apiResponse2(1, 'stored', trans('api.public.stored'));

    }

    public function update(Request $request, CourseForum $forum)
    {
        $this->authorizeForUser(apiAuth(), 'view', $forum->webinar);
        $this->authorizeForUser(apiAuth(), 'update', $forum);

        validateParam($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        $forum->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'attach' => $request->file('attachment'),
        ]);
        return apiResponse2(1, 'updated', trans('api.public.updated'));

    }

    public function pin(CourseForum $forum)
    {
        $this->authorizeForUser(apiAuth(), 'view', $forum->webinar);
        $this->authorizeForUser(apiAuth(), 'pin', $forum);

        $forum->update([
            'pin' => !$forum->pin,
        ]);
        $status = ($forum->pin) ? 'pinned' : 'unpinned';
        return apiResponse2(1, $status, trans('api.public.status', ['status' => $status, 'item' => 'forum']));
    }


}
