<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Api\Controller;
use App\Models\Meeting;
use App\Models\MeetingTime;
use App\Models\Quiz;
use App\Models\ReserveMeeting;
use App\Models\Role;
use App\Models\Session;
use App\Models\Translation\SessionTranslation;
use App\User;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Api\Panel\ReserveMeetingsController;

class ReserveMeetingController extends Controller
{
    public function createLink(Request $request)
    {
        validateParam($request->all(), [
            'link' => 'required|url',
            'reserved_meeting_id' => 'required|exists:reserve_meetings,id',
            //  'password'=>
        ]);

        $user = apiAuth();
        $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id');

        $link = $request->input('link');
        $ReserveMeeting = ReserveMeeting::where('id', $request->input('reserved_meeting_id'))
            ->whereIn('meeting_id', $meetingIds)
            ->first();

        if (!empty($ReserveMeeting) and !empty($ReserveMeeting->meeting)) {
            $ReserveMeeting->update([
                'link' => $link,
                'password' => $request->input('password') ?? null,
                'status' => ReserveMeeting::$open,
            ]);

            $notifyOptions = [
                '[link]' => $link,
                '[instructor.name]' => $ReserveMeeting->meeting->creator->full_name,
                '[time.date]' => $ReserveMeeting->day,
            ];
            sendNotification('new_appointment_link', $notifyOptions, $ReserveMeeting->user_id);

            return apiResponse2(1, 'stored', trans('api.public.stored'));
        }


    }

    public function requests(Request $request)
    {
        $controller = new ReserveMeetingsController();
        return $controller->requests( $request);
    }
    public function finish($id)
    {
        $user = apiAuth();

        $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id');

        $ReserveMeeting = \App\Models\Api\ReserveMeeting::where('id', $id)
            ->where(function ($query) use ($user, $meetingIds) {
                $query->where('user_id', $user->id)
                    ->orWhereIn('meeting_id', $meetingIds);
            })
            ->first();

        if (!empty($ReserveMeeting)) {
            $ReserveMeeting->update([
                'status' => ReserveMeeting::$finished
            ]);

            $notifyOptions = [
                '[student.name]' => $ReserveMeeting->user->full_name,
                '[instructor.name]' => $ReserveMeeting->meeting->creator->full_name,
                '[time.date]' => $ReserveMeeting->day,
            ];
            sendNotification('meeting_finished', $notifyOptions, $ReserveMeeting->user_id);
            sendNotification('meeting_finished', $notifyOptions, $ReserveMeeting->meeting->creator_id);

            return apiResponse2(1, 'finished',
                trans('api.meeting.finished'));

        }
        abort(404);

    }


    public function addLiveSession(Request $request, $id)
    {
        $user = apiAuth();

        $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id');

        $ReserveMeeting = ReserveMeeting::where('id', $id)
            ->whereIn('meeting_id', $meetingIds)
            ->first();

        if (!empty($ReserveMeeting)) {
            $agoraSettings = [
                'chat' => true,
                'record' => true,
                'users_join' => true
            ];

            $session = Session::query()->updateOrCreate([
                'creator_id' => $user->id,
                'reserve_meeting_id' => $ReserveMeeting->id,
            ], [
                'date' => time(), // can start now
                'duration' => (($ReserveMeeting->end_at - $ReserveMeeting->start_at) / 60),
                'link' => null,
                'session_api' => 'agora',
                'agora_settings' => json_encode($agoraSettings),
                'check_previous_parts' => false,
                'status' => Session::$Active,
                'created_at' => time()
            ]);

            if (!empty($session)) {
                SessionTranslation::updateOrCreate([
                    'session_id' => $session->id,
                    'locale' => mb_strtolower(app()->getLocale()),
                ], [
                    'title' => trans('update.new_in-app_call_session'),
                    'description' => trans('update.new_in-app_call_session'),
                ]);

                $ReserveMeeting->update([
                    'status' => ReserveMeeting::$open,
                ]);

                $notifyOptions = [
                    '[link]' => $session->getJoinLink(),
                    '[instructor.name]' => $user->full_name,
                    '[time.date]' => dateTimeFormat($session->date, 'j M Y H:i'),
                ];
                sendNotification('new_appointment_session', $notifyOptions, $ReserveMeeting->user_id);

                return response()->json([
                    'code' => 200
                ]);
            }
        }

        return response()->json([], 422);
    }
}
