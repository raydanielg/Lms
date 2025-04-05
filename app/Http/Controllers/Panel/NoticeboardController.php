<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Noticeboard;
use App\Models\NoticeboardStatus;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticeboardController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize("panel_noticeboard_history");

        $user = auth()->user();

        if ($user->isOrganization() || $user->isTeacher()) {

            $query = Noticeboard::where(function ($query) use ($user) {
                $query->where('organ_id', $user->id)
                    ->orWhere('instructor_id', $user->id);
            });

            $totalNoticeboards = deepClone($query)->count();
            $totalCourseNotices = deepClone($query)
                ->whereNotNull('webinar_id')
                ->count();
            $totalGeneralNotices = $totalNoticeboards - $totalCourseNotices;


            $noticeboards = $this->handleFilters($request, $query)->orderBy('created_at', 'desc')->paginate(10);

            $webinars = Webinar::select('id')
                ->where('status', Webinar::$active)
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere('teacher_id', $user->id);
                })
                ->get();

            $data = [
                'pageTitle' => trans('panel.noticeboards'),
                'noticeboards' => $noticeboards,
                'webinars' => $webinars,
                'totalNoticeboards' => $totalNoticeboards,
                'totalCourseNotices' => $totalCourseNotices,
                'totalGeneralNotices' => $totalGeneralNotices,
            ];

            return view(getTemplate() . '.panel.noticeboard.index', $data);
        }

        abort(404);
    }

    public function handleFilters(Request $request, $query)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $webinarId = $request->get('webinar_id');
        $title = $request->get('title');

        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($webinarId)) {
            $query->where('webinar_id', $webinarId);
        }

        if (!empty($title)) {
            $query->where('title', 'like', "%$title%");
        }

        return $query;
    }

    public function create()
    {
        $this->authorize("panel_noticeboard_create");

        $user = auth()->user();

        if ($user->isOrganization() || $user->isTeacher()) {

            if ($user->isTeacher()) {
                $webinars = Webinar::select('id')
                    ->where('status', Webinar::$active)
                    ->where(function ($query) use ($user) {
                        $query->where('creator_id', $user->id);
                        $query->orWhere('teacher_id', $user->id);
                    })
                    ->get();
            }

            $data = [
                'pageTitle' => trans('panel.new_noticeboard'),
                'webinars' => $webinars ?? null,
            ];

            return view(getTemplate() . '.panel.noticeboard.create', $data);
        }

        abort(404);
    }

    public function store(Request $request)
    {
        $this->authorize("panel_noticeboard_create");

        $user = auth()->user();

        if ($user->isOrganization() || $user->isTeacher()) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'title' => 'required|string|max:255',
                'type' => 'required',
                'message' => 'required',
                'webinar_id' => 'required_if:type,course'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }

            $storeData = [
                'type' => $data['type'],
                'sender' => $user->full_name,
                'title' => $data['title'],
                'message' => $data['message'],
                'created_at' => time()
            ];

            if ($user->isOrganization()) {
                $storeData['organ_id'] = $user->id;
            } else {
                $storeData['type'] = 'students';
                $storeData['instructor_id'] = $user->id;
                $storeData['webinar_id'] = $data['webinar_id'] ?? null;
            }

            Noticeboard::create($storeData);

            return response()->json([
                'code' => 200,
            ]);
        }

        abort(404);
    }

    public function edit($noticeboard_id)
    {
        $this->authorize("panel_noticeboard_create");

        $user = auth()->user();

        if ($user->isOrganization() || $user->isTeacher()) {
            $noticeboard = Noticeboard::where(function ($query) use ($user) {
                $query->where('organ_id', $user->id)
                    ->orWhere('instructor_id', $user->id);
            })->where('id', $noticeboard_id)
                ->first();

            if (!empty($noticeboard)) {

                if ($user->isTeacher()) {
                    $webinars = Webinar::select('id')
                        ->where('status', Webinar::$active)
                        ->where(function ($query) use ($user) {
                            $query->where('creator_id', $user->id);
                            $query->orWhere('teacher_id', $user->id);
                        })
                        ->get();
                }

                $data = [
                    'pageTitle' => trans('panel.noticeboards'),
                    'noticeboard' => $noticeboard,
                    'webinars' => $webinars ?? null,
                ];

                return view(getTemplate() . '.panel.noticeboard.create', $data);
            }
        }

        abort(404);
    }

    public function update(Request $request, $noticeboard_id)
    {
        $this->authorize("panel_noticeboard_create");

        $user = auth()->user();

        if ($user->isOrganization() || $user->isTeacher()) {
            $noticeboard = Noticeboard::where(function ($query) use ($user) {
                $query->where('organ_id', $user->id)
                    ->orWhere('instructor_id', $user->id);
            })->where('id', $noticeboard_id)
                ->first();

            if (!empty($noticeboard)) {
                $data = $request->all();

                $validator = Validator::make($data, [
                    'title' => 'required|string|max:255',
                    'type' => 'required',
                    'message' => 'required',
                    'webinar_id' => 'required_if:type,course'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'code' => 422,
                        'errors' => $validator->errors()
                    ], 422);
                }

                $updateData = [
                    'type' => $data['type'],
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'created_at' => time()
                ];

                if ($user->isTeacher()) {
                    $updateData['type'] = 'students';
                    $updateData['instructor_id'] = $user->id;
                    $updateData['webinar_id'] = $data['webinar_id'] ?? null;
                }

                $noticeboard->update($updateData);

                NoticeboardStatus::where('noticeboard_id', $noticeboard->id)->delete();

                return response()->json([
                    'code' => 200,
                ]);
            }
        }

        return response()->json([], 422);
    }

    public function delete($noticeboard_id)
    {
        $this->authorize("panel_noticeboard_delete");

        $user = auth()->user();

        if ($user->isOrganization() || $user->isTeacher()) {
            $noticeboard = Noticeboard::where(function ($query) use ($user) {
                $query->where('organ_id', $user->id)
                    ->orWhere('instructor_id', $user->id);
            })->where('id', $noticeboard_id)
                ->first();

            if (!empty($noticeboard)) {
                $noticeboard->delete();

                return response()->json([
                    'code' => 200,
                ]);
            }
        }

        return response()->json([], 422);
    }

    public function saveStatus($noticeboard_id)
    {
        $user = auth()->user();

        $status = NoticeboardStatus::where('user_id', $user->id)
            ->where('noticeboard_id', $noticeboard_id)
            ->first();

        if (empty($status)) {
            NoticeboardStatus::create([
                'user_id' => $user->id,
                'noticeboard_id' => $noticeboard_id,
                'seen_at' => time()
            ]);
        }

        return response()->json([], 200);
    }

}
