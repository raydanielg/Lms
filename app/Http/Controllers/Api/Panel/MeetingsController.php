<?php

namespace App\Http\Controllers\Api\Panel;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Meeting;
use App\Models\MeetingTime;
use App\Models\ReserveMeeting;
use App\User;

class MeetingsController extends Controller
{
    public function list(Request $request, $id)
    {
        $teacher = User::where('id', $id)->first();

        if (!$teacher) {
            abort(404);
        }
        $meeting = Meeting::where('creator_id', $id)
            ->with([
                'meetingTimes'
            ])
            ->first();

        if (!$meeting) {
            return apiResponse2(0, 'no_meeting', 'There is no active meeting for this user');
        }

        return $meeting;
    }

    public function reserve(Request $request)
    {

        validateParam($request->all(), [
            'day' => 'required',
            'time' => 'required|exists:meeting_times,id',
        ]);
        $user = apiAuth();

        $timeIds = $request->input('time');
        $day = $request->input('day');
        $day = dateTimeFormat($day, 'Y-m-d');

        if (!empty($timeIds)) {
            $meetingTimes = MeetingTime::whereIn('id', $timeIds)
                ->with('meeting')
                ->get();
            if ($meetingTimes->isNotEmpty()) {
                $meetingId = $meetingTimes->first()->meeting_id;
                $meeting = Meeting::find($meetingId);

                if (!empty($meeting) and !$meeting->disabled) {
                    if (!empty($meeting->amount) and $meeting->amount > 0) {
                        foreach ($meetingTimes as $meetingTime) {
                            $reserveMeeting = ReserveMeeting::where('meeting_time_id', $meetingTime->id)
                                ->where('day', $day)
                                ->first();
                            if (!empty($reserveMeeting) and $reserveMeeting->user_id == $user->id) {
                                return apiResponse2(0, 'reserved', 'This user has already reserved this time.');

                            }

                            if (!empty($reserveMeeting) and $reserveMeeting->locked_at) {
                                return apiResponse2(0, 'locked', 'This time has been locked');
                            }

                            if (!empty($reserveMeeting) and $reserveMeeting->reserved_at) {
                                return apiResponse2(0, 'reserved', 'This time has been reserved');

                            }

                            $hourlyAmount = $meetingTime->meeting->amount;
                            $explodetime = explode('-', $meetingTime->time);
                            $hours = (strtotime($explodetime[1]) - strtotime($explodetime[0])) / 3600;

                            $reserveMeeting = ReserveMeeting::updateOrCreate([
                                'user_id' => $user->id,
                                'meeting_time_id' => $meetingTime->id,
                                'meeting_id' => $meetingTime->meeting_id,
                                'status' => ReserveMeeting::$pending,
                                'day' => $day,
                                'date' => strtotime($day),
                            ], [
                                'paid_amount' => (!empty($hourlyAmount) and $hourlyAmount > 0) ? $hourlyAmount * $hours : 0,
                                'discount' => $meetingTime->meeting->discount,
                                'created_at' => time(),
                            ]);

                            $cart = Cart::where('creator_id', $user->id)
                                ->where('reserve_meeting_id', $reserveMeeting->id)
                                ->first();

                            if (empty($cart)) {
                                Cart::create([
                                    'creator_id' => $user->id,
                                    'reserve_meeting_id' => $reserveMeeting->id,
                                    'created_at' => time()
                                ]);
                            }
                        }

                        return apiResponse2(1, 'created', 'This time reserved successfully.');
                    } else {
                        return $this->handleFreeMeetingReservation($user, $meeting, $meetingTimes, $day);
                    }
                } else {

                    return apiResponse2(0, 'disabled', 'This time has been disabled');

                }
            }

        }


    }

    private function handleFreeMeetingReservation($user, $meeting, $meetingTimes, $day)
    {
        foreach ($meetingTimes as $meetingTime) {
            $hourlyAmount = $meetingTime->meeting->amount;
            $explodetime = explode('-', $meetingTime->time);
            $hours = (strtotime($explodetime[1]) - strtotime($explodetime[0])) / 3600;

            $reserve = ReserveMeeting::updateOrCreate([
                'user_id' => $user->id,
                'meeting_time_id' => $meetingTime->id,
                'meeting_id' => $meetingTime->meeting_id,
                'status' => ReserveMeeting::$pending,
                'day' => $day,
                'date' => strtotime($day),
            ], [
                'paid_amount' => (!empty($hourlyAmount) and $hourlyAmount > 0) ? $hourlyAmount * $hours : 0,
                'discount' => $meetingTime->meeting->discount,
                'created_at' => time(),
            ]);

            if (!empty($reserve)) {
                $sale = Sale::create([
                    'buyer_id' => $user->id,
                    'seller_id' => $meeting->creator_id,
                    'meeting_id' => $meeting->id,
                    'type' => Sale::$meeting,
                    'payment_method' => Sale::$credit,
                    'amount' => 0,
                    'total_amount' => 0,
                    'created_at' => time(),
                ]);

                if (!empty($sale)) {
                    $reserve->update([
                        'sale_id' => $sale->id,
                        'reserved_at' => time()
                    ]);
                }
            }
        }

        return apiResponse(1, 'created', 'ddd');


    }

    public function reservation(Request $request)
    {
        $user = auth()->user();
        $reserveMeetingsQuery = ReserveMeeting::where('user_id', $user->id)
            ->whereHas('sale');

        $openReserveCount = deepClone($reserveMeetingsQuery)->where('status', \App\models\ReserveMeeting::$open)->count();
        $totalReserveCount = deepClone($reserveMeetingsQuery)->count();

        $meetingIds = deepClone($reserveMeetingsQuery)->pluck('meeting_id')->toArray();
        $teacherIds = Meeting::whereIn('id', array_unique($meetingIds))
            ->pluck('creator_id')
            ->toArray();
        $instructors = User::select('id', 'full_name')
            ->whereIn('id', array_unique($teacherIds))
            ->get();


        $reserveMeetingsQuery = $this->filters($reserveMeetingsQuery, $request);
        $reserveMeetingsQuery = $reserveMeetingsQuery->with([
            'meetingTime',
            'meeting' => function ($query) {
                $query->with([
                    'creator' => function ($query) {
                        $query->select('id', 'full_name', 'avatar', 'email');
                    }
                ]);
            },
            'user' => function ($query) {
                $query->select('id', 'full_name', 'avatar', 'email', 'avatar_settings');
            },
            'sale'
        ]);

        $reserveMeetings = $reserveMeetingsQuery
            ->orderBy('created_at', 'desc')
            ->get();

        $activeMeetingTimeIds = ReserveMeeting::where('user_id', $user->id)->where('status', ReserveMeeting::$open)->pluck('meeting_time_id');

        $activeMeetingTimes = MeetingTime::whereIn('id', $activeMeetingTimeIds)->get();

        $activeHoursCount = 0;
        foreach ($activeMeetingTimes as $time) {
            $explodetime = explode('-', $time->time);
            $activeHoursCount += strtotime($explodetime[1]) - strtotime($explodetime[0]);
        }

        $data = [
            'pageTitle' => trans('meeting.meeting_list_page_title'),
            'instructors' => $instructors,
            'reserveMeetings' => $reserveMeetings,
            'openReserveCount' => $openReserveCount,
            'totalReserveCount' => $totalReserveCount,
            'activeHoursCount' => round($activeHoursCount / 3600, 2),
        ];

        return apiResponse2(1, 'list', null, $reserveMeetings);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $day = $request->get('day');
        $instructor_id = $request->get('instructor_id');
        $student_id = $request->get('student_id');
        $status = $request->get('status');
        $openMeetings = $request->get('open_meetings');

        if (!empty($from) and !empty($to)) {
            $from = strtotime($from);
            $to = strtotime($to);

            $query->whereBetween('created_at', [$from, $to]);
        } else {
            if (!empty($from)) {
                $from = strtotime($from);
                $query->where('created_at', '>=', $from);
            }

            if (!empty($to)) {
                $to = strtotime($to);

                $query->where('created_at', '<', $to);
            }
        }

        if (!empty($day) and $day != 'all') {
            $meetingTimeIds = $query->pluck('meeting_time_id');
            $meetingTimeIds = MeetingTime::whereIn('id', $meetingTimeIds)
                ->where('day_label', $day)
                ->pluck('id');

            $query->whereIn('meeting_time_id', $meetingTimeIds);
        }

        if (!empty($instructor_id) and $instructor_id != 'all') {

            $meetingsIds = Meeting::where('creator_id', $instructor_id)
                ->where('disabled', false)
                ->pluck('id')
                ->toArray();

            $query->whereIn('meeting_id', $meetingsIds);
        }

        if (!empty($student_id) and $student_id != 'all') {
            $query->where('user_id', $student_id);
        }


        if (!empty($status) and $status != 'All') {
            $query->where('status', strtolower($status));
        }

        if (!empty($openMeetings) and $openMeetings == 'on') {
            $query->where('status', 'open');
        }

        return $query;
    }


}
