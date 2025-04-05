<?php


namespace App\Http\Controllers\Api\Web;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Models\Cart;
use App\Models\Meeting;
use App\Models\MeetingTime;
use App\Models\ReserveMeeting;

class MeetingsController extends Controller
{
    public function reserve(Request $request)
    {
        validateParam($request->all(), [
            'time_id' => 'required|exists:meeting_times,id',
            'date' => 'required|date',
            'meeting_type' => 'required|in:in_person,online',
            //  'with_group_meeting' ,
            'description'
        ]);

        //dd('f');
        $user = apiAuth();
        $timeId = $request->input('time_id');
        $day = $request->input('date');
        $studentCount = $request->get('student_count', 1);
        $selectedMeetingType = $request->get('meeting_type', 'online');
        $description = $request->get('description');

        if (empty($studentCount)) {
            $studentCount = 1;
        }

        if (!in_array($selectedMeetingType, ['in_person', 'online'])) {
            $selectedMeetingType = 'online';
        }

        if (!empty($timeId)) {
            $meetingTime = MeetingTime::where('id', $timeId)->first();

            if (!empty($meetingTime)) {
                $meeting = $meetingTime->meeting;
                if (!$meeting->group_meeting){
                    validateParam($request->all(), [
                        'student_count' =>"in:0",
                    ]);
                }

                if (!empty($meeting) and !$meeting->disabled) {
                    if (!empty($meeting->amount) and $meeting->amount > 0) {

                        $reserveMeeting = ReserveMeeting::where('meeting_time_id', $meetingTime->id)
                            ->where('day', $day)
                            ->first();

                        if (!empty($reserveMeeting) and $reserveMeeting->locked_at) {

                            return apiResponse2(0, 'locked',
                                trans('meeting.locked_time'), null,
                                trans('public.request_failed')
                            );

                        }

                        if (!empty($reserveMeeting) and $reserveMeeting->reserved_at) {

                            return apiResponse2(0, 'reserved', trans('meeting.reserved_time')
                                , null, trans('public.request_failed')
                            );

                        }

                        $hourlyAmountResult = $this->handleHourlyMeetingAmount($meeting, $meetingTime, $studentCount, $selectedMeetingType);

                        //dd($hourlyAmountResult);
                        if (!is_array($hourlyAmountResult)) {
                            return $hourlyAmountResult;
                            return $hourlyAmountResult['result']; // json response
                        }

                        $hourlyAmount = $hourlyAmountResult['result'];

                        $explodetime = explode('-', $meetingTime->time);

                        $hours = (strtotime($explodetime[1]) - strtotime($explodetime[0])) / 3600;

                        $instructorTimezone = $meeting->getTimezone();

                        $startAt = $this->handleUtcDate($day, $explodetime[0], $instructorTimezone);
                        $endAt = $this->handleUtcDate($day, $explodetime[1], $instructorTimezone);

                        $reserveMeeting = ReserveMeeting::updateOrCreate([
                            'user_id' => $user->id,
                            'meeting_time_id' => $meetingTime->id,
                            'meeting_id' => $meetingTime->meeting_id,
                            'status' => ReserveMeeting::$pending,
                            'day' => $day,
                            'meeting_type' => $selectedMeetingType,
                            'student_count' => $studentCount
                        ], [
                            'date' => strtotime($day),
                            'start_at' => $startAt,
                            'end_at' => $endAt,
                            'paid_amount' => (!empty($hourlyAmount) and $hourlyAmount > 0) ? $hourlyAmount * $hours : 0,
                            'discount' => $meetingTime->meeting->discount,
                            'description' => $description,
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
                        return apiResponse2(1, 'stored',

                            trans('update.meeting_added_to_cart'), null,
                            trans('public.request_success')
                        );

                    } else {
                        return $this->handleFreeMeetingReservation($user, $meeting, $meetingTime, $day, $selectedMeetingType, $studentCount);
                    }
                } else {

                    return apiResponse2(0, 'disabled',

                        trans('meeting.meeting_disabled'), null,
                        trans('public.request_failed')
                    );


                }
            }
        }

        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('meeting.select_time_to_reserve'),
            'status' => 'error'
        ];
        return response()->json($toastData);
    }

    private function handleHourlyMeetingAmount(Meeting $meeting, MeetingTime $meetingTime, $studentCount, $selectedMeetingType)
    {
        if (empty($studentCount)) {
            $studentCount = 1;
        }

        $status = true;
        $hourlyAmount = $meeting->amount;

        if ($selectedMeetingType == 'in_person' and in_array($meetingTime->meeting_type, ['in_person', 'all'])) {
            if ($meeting->in_person) {
                $hourlyAmount = $meeting->in_person_amount;
            } else {
                return apiResponse2(0, 'unavailable_in_person',
                    trans('update.in_person_meetings_unavailable')
                    , null, trans('public.request_failed')
                );

                /*  $toastData = [
                      'status' => 'error',
                      'title' => trans('public.request_failed'),
                      'msg' => trans('update.in_person_meetings_unavailable'),
                  ];*/
                $hourlyAmount = response()->json($toastData);
                $status = false;
            }
        }

        if ($meeting->group_meeting and $status) {
            $types = ['in_person', 'online'];

            foreach ($types as $type) {
                if ($selectedMeetingType == $type and in_array($meetingTime->meeting_type, ['all', $type])) {

                    $meetingMaxVar = $type . '_group_max_student';
                    $meetingMinVar = $type . '_group_min_student';
                    $meetingAmountVar = $type . '_group_amount';

                    if ($studentCount < $meeting->$meetingMinVar) {
                        $hourlyAmount = $hourlyAmount * $studentCount;
                    } else if ($studentCount > $meeting->$meetingMaxVar) {
                        $toastData = [
                            'status' => 'error',
                            'title' => trans('public.request_failed'),
                            'msg' => trans('update.group_meeting_max_student_count_hint', ['max' => $meeting->$meetingMaxVar]),
                        ];
                        $hourlyAmount = response()->json($toastData);
                        $status = false;
                    } else if ($studentCount >= $meeting->$meetingMinVar and $studentCount <= $meeting->$meetingMaxVar) {
                        $hourlyAmount = $meeting->$meetingAmountVar * $studentCount;
                    }
                }
            }
        }

        return [
            'status' => $status,
            'result' => $hourlyAmount
        ];
    }

    private function handleFreeMeetingReservation($user, $meeting, $meetingTime, $day, $selectedMeetingType, $studentCount)
    {
        $instructorTimezone = $meeting->getTimezone();
        $explodetime = explode('-', $meetingTime->time);

        $startAt = $this->handleUtcDate($day, $explodetime[0], $instructorTimezone);
        $endAt = $this->handleUtcDate($day, $explodetime[1], $instructorTimezone);

        $reserve = ReserveMeeting::updateOrCreate([
            'user_id' => $user->id,
            'meeting_time_id' => $meetingTime->id,
            'meeting_id' => $meetingTime->meeting_id,
            'status' => ReserveMeeting::$pending,
            'day' => $day,
            'meeting_type' => $selectedMeetingType,
            'student_count' => $studentCount
        ], [
            'date' => strtotime($day),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'paid_amount' => 0,
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

        return apiResponse2(1, 'stored',

            trans('cart.success_pay_msg_for_free_meeting'), null,
            trans('public.request_success')
        );


    }

    private function handleUtcDate($day, $clock, $instructorTimezone)
    {
        $date = $day . ' ' . $clock;

        $utcDate = convertTimeToUTCzone($date, $instructorTimezone);

        return $utcDate->getTimestamp();
    }
}
