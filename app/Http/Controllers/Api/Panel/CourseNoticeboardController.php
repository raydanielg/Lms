<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseNoticeboardResource;
use App\Models\Api\Webinar;
use App\Models\CourseNoticeboard;
use Illuminate\Http\Request;

class CourseNoticeboardController extends Controller
{
    public function index($webinar_id)
    {
        $webinar = Webinar::find($webinar_id);
        abort_unless($webinar, 404);
        $user = apiAuth();
        // noticeboards
        if ($webinar->creator_id != $user->id and $webinar->teacher_id != $user->id and !$user->isAdmin()) {
            $unReadCourseNoticeboards = CourseNoticeboard::where('webinar_id', $webinar->id)
                ->whereDoesntHave('noticeboardStatus', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count();

            if ($unReadCourseNoticeboards) {
                $url = $webinar->getNoticeboardsPageUrl();
            //    return redirect($url);
            }
        }
        $noticeboards = $webinar
            ->noticeboards;
        //  dd($noticeboards) ;
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), CourseNoticeboardResource::collection($noticeboards));

    }
}
