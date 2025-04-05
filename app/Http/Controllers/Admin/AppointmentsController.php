<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Meeting;
use App\Models\Order;
use App\Models\ReserveMeeting;
use App\Models\Sale;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AppointmentsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_appointments_lists');

        $query = ReserveMeeting::whereNotNull('reserved_at');

        $totalAppointments = deepClone($query)->count();
        $openAppointments = deepClone($query)->where('status', ReserveMeeting::$open)->count();
        $finishedAppointments = deepClone($query)->where('status', ReserveMeeting::$finished)->count();
        $totalConsultants = Meeting::where('disabled', false)
            ->whereHas('meetingTimes')
            ->groupBy('creator_id')
            ->count();

        $query = $this->filters($query, $request);

        $appointments = $query->with([
            'meeting' => function ($query) {
                $query->with([
                    'creator' => function ($query) {
                        $query->select('id', 'full_name');
                    }
                ]);
            },
            'user' => function ($query) {
                $query->select('id', 'full_name');
            },
            'sale'
        ])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.meetings_list_title'),
            'totalAppointments' => $totalAppointments,
            'openAppointments' => $openAppointments,
            'finishedAppointments' => $finishedAppointments,
            'totalConsultants' => $totalConsultants,
            'appointments' => $appointments,
        ];

        $consultant_ids = $request->get('consultant_ids', []);
        $user_ids = $request->get('user_ids', []);
        if (!empty($consultant_ids)) {
            $data['consultants'] = User::select('id', 'full_name')
                ->whereIn('id', $consultant_ids)->get();
        }

        if (!empty($user_ids)) {
            $data['users'] = User::select('id', 'full_name')
                ->whereIn('id', $user_ids)->get();
        }

        return view('admin.appointments.lists', $data);
    }

    private function filters($query, $request)
    {
        $search = $request->get('search');
        $from = $request->get('from');
        $to = $request->get('to');
        $status = $request->get('status');
        $sort = $request->get('sort');
        $consultant_ids = $request->get('consultant_ids', []);
        $user_ids = $request->get('user_ids', []);

        if (!empty($search)) {
            $userIds = User::where('full_name', 'like', "%$search%")->pluck('id')->toArray();
            $meetingIds = Meeting::where('disabled', false)
                ->whereIn('creator_id', $userIds)
                ->pluck('id')
                ->toArray();

            $query->where(function ($query) use ($userIds, $meetingIds) {
                $query->whereIn('user_id', $userIds)
                    ->orWhereIn('meeting_id', $meetingIds);
            });
        }

        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'date');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'has_discount':
                    $query->whereNotNull('discount')
                        ->where('discount', '>', '0');
                    break;
                case 'free':
                    $query->where('paid_amount', '=', '0');
                    break;
                case 'amount_asc':
                    $query->orderBy('paid_amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('paid_amount', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('date', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('date', 'desc');
                    break;
            }
        }

        if (!empty($consultant_ids)) {
            $meetingIds = Meeting::where('disabled', false)
                ->whereIn('creator_id', $consultant_ids)
                ->pluck('id')
                ->toArray();

            $query->whereIn('meeting_id', $meetingIds);
        }

        if (!empty($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        }

        return $query;
    }

    public function join($id)
    {
        $this->authorize('admin_appointments_join');

        $ReserveMeeting = ReserveMeeting::where('id', $id)->first();

        if (!empty($ReserveMeeting)) {
            return Redirect::away($ReserveMeeting->link);
        }

        abort(404);
    }

    public function getReminderDetails($id)
    {
        $this->authorize('admin_appointments_send_reminder');

        $appointment = ReserveMeeting::where('id', $id)->first();

        $templateId = getNotificationTemplates('appointment_reminder');
        $notificationTemplate = \App\Models\NotificationTemplate::where('id', $templateId)->first();

        if (!empty($appointment) and $notificationTemplate) {

            $notifyOptions = [
                '[time.date]' => dateTimeFormat(strtotime($appointment->day), 'j M Y') . ' (' . $appointment->meetingTime->time . ')',
            ];

            $data = [
                'consultant' => $appointment->meeting->creator->full_name,
                'reservatore' => $appointment->user->full_name,
                'title' => $notificationTemplate->title,
                'content' => str_replace(array_keys($notifyOptions), array_values($notifyOptions), $notificationTemplate->template)
            ];

            return response()->json($data, 200);
        }

        return response()->json([], 422);
    }

    public function sendReminder($id)
    {
        $this->authorize('admin_appointments_send_reminder');

        $appointment = ReserveMeeting::where('id', $id)->first();

        if (!empty($appointment)) {
            $notifyOptions = [
                '[time.date]' => dateTimeFormat(strtotime($appointment->day), 'j M Y') . ' (' . $appointment->meetingTime->time . ')',
            ];

            sendNotification('appointment_reminder', $notifyOptions, $appointment->meeting->creator->id); // consultant
            sendNotification('appointment_reminder', $notifyOptions, $appointment->user_id); // reservatore

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => 'Reminder send successful',
                'status' => 'success'
            ];

            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }

    public function cancel($id)
    {
        $this->authorize('admin_appointments_cancel');

        $appointment = ReserveMeeting::where('id', $id)->first();

        if (!empty($appointment)) {

            $sale = Sale::where('id', $appointment->sale_id)
                ->whereNull('refund_at')
                ->first();

            if (!empty($sale)) {

                Accounting::refundAccounting($sale);

                $sale->update([
                    'refund_at' => time()
                ]);

                $appointment->update([
                    'status' => ReserveMeeting::$canceled
                ]);

                $toastData = [
                    'title' => trans('public.request_success'),
                    'msg' => 'Appointment canceled successful',
                    'status' => 'success'
                ];

                return back()->with(['toast' => $toastData]);
            }
        }

        abort(404);
    }
}
