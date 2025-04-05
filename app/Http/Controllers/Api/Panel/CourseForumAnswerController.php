<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseForumAnswerResource;
use App\Models\CourseForum;
use App\Models\Api\CourseForumAnswer;
use Illuminate\Http\Request;

class CourseForumAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *
     */

    public function index(CourseForum $forum)
    {
        $this->authorizeForUser(apiAuth(), 'view', $forum->webinar);
        $courseForum = CourseForumAnswer::where('forum_id', $forum->id)->get();

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'answers' => CourseForumAnswerResource::collection($courseForum)
        ]);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CourseForum $forum)
    {
        $user = apiAuth();
        $this->authorizeForUser(apiAuth(), 'view', $forum->webinar);

        validateParam($request->all(), [
            'description' => 'required'
        ]);

        CourseForumAnswer::create([
            'forum_id' => $forum->id,
            'user_id' => $user->id,
            'description' => $request->input('description'),
            'pin' => false,
            'resolved' => false,
            'created_at' => time(),
        ]);

        if ($user->id != $forum->user_id) {
            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[c.title]' => $forum->webinar->title,
            ];

            sendNotification('new_answer_in_forum', $notifyOptions, $forum->user_id);
        }
        return apiResponse2(1, 'stored', trans('api.public.stored'));

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd('f');
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CourseForumAnswer $answer)
    {
        $user = apiAuth();
        $this->authorizeForUser(apiAuth(), 'view', $answer->course_forum->webinar);
        $this->authorizeForUser($user, 'update', $answer);
        validateParam($request->all(), [
            'description' => 'required'
        ]);
        $answer->update([
            'description' => $request->input('description')
        ]);
        return apiResponse2(1, 'updated', trans('api.public.updated'));
    }

    public function pin(CourseForumAnswer $answer)
    {
        $user = apiAuth();
        $this->authorizeForUser($user, 'pin', $answer);
        $this->authorizeForUser(apiAuth(), 'view', $answer->course_forum->webinar);

        $answer->update([
            'pin' => !$answer->pin
        ]);
        $status = ($answer->pin) ? 'pinned' : 'unpinned';

        return apiResponse2(1, $status, trans('api.public.status', ['status' => $status, 'item' => 'answer']));

    }

    public function resolve(CourseForumAnswer $answer)
    {
        $user = apiAuth();
        $this->authorizeForUser($user, 'resolve', $answer);
        $this->authorizeForUser(apiAuth(), 'view', $answer->course_forum->webinar);

        $answer->update([
            'resolved' => !$answer->resolved
        ]);

        $status = ($answer->resolved) ? 'resolved' : 'un_resolved';

        return apiResponse2(1, $status, trans('api.public.status', ['status' => $status, 'item' => 'answer']));

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
