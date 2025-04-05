<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mixins\Cart\AbandonedCartReminder;
use App\Mixins\Notifications\SendSMS;
use App\Models\File;
use App\Models\Gift;
use App\Models\InstallmentOrder;
use App\Models\InstallmentOrderPayment;
use App\Models\InstallmentReminder;
use App\Models\InstallmentStep;
use App\Models\ReserveMeeting;
use App\Models\Sale;
use App\Models\SelectedInstallmentStep;
use App\Models\Session;
use App\Models\SessionRemind;
use App\Models\Subscribe;
use App\Models\SubscribeRemind;
use App\Models\TextLesson;
use App\Models\WebinarChapterItem;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class JobsController extends Controller
{
    public function index(Request $request, $methodName)
    {
        return $this->$methodName($request);
    }

    public function sdfds()
    {
        /*Schema::table("ai_content_template_translations", function (Blueprint $table) {

        });*/
    }

    public function sendSessionsReminder($request)
    {
        $buyersCount = 0;
        $hour = getRemindersSettings('webinar_reminder_schedule') ?? 1;
        $time = time();
        $hoursLater = $time + ($hour * 60 * 60);

        $sessions = Session::where('date', '>=', $time)
            ->whereBetween('date', [$time, $hoursLater])
            ->with([
                'webinar'
            ])
            ->get();


        foreach ($sessions as $session) {
            $webinar = $session->webinar;

            $buyers = Sale::whereNull('refund_at')
                ->where('webinar_id', $session->webinar_id)
                ->pluck('buyer_id')
                ->toArray();

            $notifyOptions = [
                '[c.title]' => $webinar->title,
                '[time.date]' => dateTimeFormat($session->date, 'j M Y , H:i'),
            ];

            $buyersCount = count($buyers);

            if (count($buyers)) {
                foreach ($buyers as $buyer) {
                    $check = SessionRemind::where('session_id', $session->id)
                        ->where('user_id', $buyer)
                        ->first();

                    if (empty($check)) {
                        sendNotification('webinar_reminder', $notifyOptions, $buyer); // consultant

                        SessionRemind::create([
                            'session_id' => $session->id,
                            'user_id' => $buyer,
                            'created_at' => time()
                        ]);
                    }
                }
            }

            $check = SessionRemind::where('session_id', $session->id)
                ->where('user_id', $session->creator_id)
                ->first();

            if (empty($check)) {
                sendNotification('webinar_reminder', $notifyOptions, $session->creator_id); // consultant

                SessionRemind::create([
                    'session_id' => $session->id,
                    'user_id' => $session->creator_id,
                    'created_at' => time()
                ]);
            }
        }

        return response()->json([
            'sessions_count' => count($sessions),
            'buyers' => $buyersCount,
            'message' => "Notifications were sent for sessions starting from (" . dateTimeFormat($time, 'j M Y, H:i') . ')  to  (' . dateTimeFormat($hoursLater, 'j M Y, H:i') . ')'
        ]);
    }

    public function sendMeetingsReminder($request)
    {
        $hour = getRemindersSettings('meeting_reminder_schedule') ?? 1;
        $time = time();
        $hoursLater = $time + ($hour * 60 * 60);

        $reserves = ReserveMeeting::whereBetween('start_at', [$time, $hoursLater])
            ->whereNotNull('reserved_at')
            ->whereHas('sale', function ($query) {
                $query->whereNull('refund_at');
            })
            ->with([
                'meeting' => function ($query) {
                    $query->with([
                        'creator' => function ($query) {
                            $query->select('id', 'full_name');
                        }
                    ]);
                }
            ])
            ->get();

        foreach ($reserves as $reserve) {
            try {
                $notifyOptions = [
                    '[instructor.name]' => $reserve->meeting->creator->full_name,
                    '[time.date]' => dateTimeFormat($reserve->start_at, 'j M Y , H:i'),
                ];

                sendNotification('meeting_reserve_reminder', $notifyOptions, $reserve->user_id);
            } catch (\Exception $exception) {

            }
        }

        return response()->json([
            'reserve_count' => count($reserves),
            'message' => "Notifications were sent for meetings starting from (" . dateTimeFormat($time, 'j M Y, H:i') . ')  to  (' . dateTimeFormat($hoursLater, 'j M Y, H:i') . ')'
        ]);
    }


    public function sendSubscribeReminder($request)
    {
        $sendCount = 0;
        $hour = getRemindersSettings('subscribe_reminder_schedule') ?? 1;
        $time = time();
        $hoursLater = $time + ($hour * 60 * 60);

        $bigSubscribeDay = Subscribe::orderBy('days', 'desc')->first();

        $saleTime = $time - ($bigSubscribeDay->days * 24 * 60 * 60);

        $subscribeSale = Sale::where('type', Sale::$subscribe)
            ->whereNull('refund_at')
            ->whereBetween('created_at', [$saleTime, $time])
            ->with([
                'subscribe'
            ])
            ->get();

        foreach ($subscribeSale as $sale) {
            try {
                $subscribe = $sale->subscribe;

                $checkReminder = SubscribeRemind::where('user_id', $sale->buyer_id)
                    ->where('subscribe_id', $subscribe->id)
                    ->first();

                if (empty($checkReminder)) {
                    $expireDate = $sale->created_at + ($subscribe->days * 24 * 60 * 60);

                    $createReminderRecord = false;

                    if ($expireDate >= $time and $expireDate <= $hoursLater) {
                        $sendCount += 1;
                        $createReminderRecord = true;

                        $notifyOptions = [
                            '[time.date]' => dateTimeFormat($expireDate, 'j M Y , H:i'),
                        ];

                        sendNotification('subscribe_reminder', $notifyOptions, $sale->buyer_id);
                    } elseif ($expireDate < $time) {
                        $createReminderRecord = true;
                    }

                    if ($createReminderRecord) {
                        SubscribeRemind::create([
                            'user_id' => $sale->buyer_id,
                            'subscribe_id' => $subscribe->id,
                            'created_at' => $time
                        ]);
                    }
                }
            } catch (\Exception $exception) {

            }
        }

        return response()->json([
            'count' => $sendCount,
            'message' => "Notifications were sent for users expiring subscribe from (" . dateTimeFormat($time, 'j M Y, H:i') . ')  to  (' . dateTimeFormat($hoursLater, 'j M Y, H:i') . ')'
        ]);
    }

    /**
     * run once a day
     * */
    public function sendInstallmentReminders()
    {
        $sendCount = 0;
        $timestamp = time();

        $settings = getInstallmentsSettings();
        $reminderBeforeOverdueDays = $settings['reminder_before_overdue_days'];
        $reminderAfterOverdueDays = $settings['reminder_after_overdue_days'];

        $steps = SelectedInstallmentStep::query()
            ->whereHas('selectedInstallment', function ($query) {
                $query->whereHas('order');
            })
            ->get();

        foreach ($steps as $step) {
            $installment = $step->selectedInstallment;
            $order = $installment->order;

            $checkPayment = InstallmentOrderPayment::query()->where('installment_order_id', $order->id)
                ->where('selected_installment_step_id', $step->id)
                ->where('status', 'paid')
                ->first();

            if (empty($checkPayment)) {
                $itemPrice = $order->getItemPrice();

                $dueAt = ($step->deadline * 86400) + $order->created_at;
                $daysLeft = ($dueAt - $timestamp) / (86400);

                $reminderType = null;
                $template = null;
                $notifyOptions = [
                    '[installment_title]' => $installment->installment->title,
                    '[time.date]' => dateTimeFormat($dueAt, 'j M Y - H:i'),
                    '[amount]' => handlePrice($step->getPrice($itemPrice)),
                ];

                if (!empty($reminderBeforeOverdueDays) and $daysLeft > 0 and $daysLeft < $reminderBeforeOverdueDays) {
                    $template = "reminder_installments_before_overdue";
                    $reminderType = "before_due";
                } else if ($daysLeft < 0) {
                    $template = "installment_due_reminder";
                    $reminderType = "due";

                    if (!empty($reminderAfterOverdueDays) and $daysLeft < (-1 * $reminderAfterOverdueDays)) {
                        $template = "reminder_installments_after_overdue";
                        $reminderType = "after_due";
                    }
                }

                if (!empty($notifyOptions) and !empty($template) and !empty($reminderType)) {
                    $checkReminder = InstallmentReminder::query()->where('installment_order_id', $order->id)
                        ->where('installment_step_id', $step->id)
                        ->where('user_id', $order->user_id)
                        ->where('type', $reminderType)
                        ->first();

                    if (empty($checkReminder)) {
                        InstallmentReminder::query()->create([
                            'installment_order_id' => $order->id,
                            'installment_step_id' => $step->id,
                            'user_id' => $order->user_id,
                            'type' => $reminderType,
                            'created_at' => time()
                        ]);

                        $sendCount += 1;

                        sendNotification($template, $notifyOptions, $order->user_id);
                    }
                }
            }
        }

        return response()->json([
            'count' => $sendCount,
            'message' => "Notifications were sent for users installment overdue"
        ]);
    }

    /*
     * Route => /jobs/checkGiftsDate
     * This jobs is executed every hour
     * */
    public function checkGiftsDate()
    {
        $start = time();
        $end = $start + 3600; // one hour later

        $gifts = Gift::query()->where('status', 'active')
            ->whereNotNull('date')
            ->whereBetween('date', [$start, $end])
            ->get();

        if ($gifts->isNotEmpty()) {
            foreach ($gifts as $gift) {
                $amount = $gift->sale->total_amount;

                $gift->sendReminderToSender($amount, 'gift_sender_notification');

                $gift->sendReminderToSender($amount, 'admin_gift_sending_confirmation');
            }
        }

        return response()->json([
            'count' => $gifts->count(),
            'message' => "Notifications were sent for gifts"
        ]);
    }

    public function sendAbandonedCartReminders()
    {
        $abandonedCartReminder = (new AbandonedCartReminder());
        $abandonedCartReminder->sendAbandonedReminders();

        return response()->json([
            'status' => 200,
            'message' => "Notifications were sent for Abandoned Cart Rules"
        ]);
    }

    public function testSMS(Request $request)
    {
        //"/jobs/testSMS?to=+601140017480&content?=12345";

        $to = "+{$request->get('to')}";
        $content = $request->get('content');

        $sendSMS = (new SendSMS($to, $content));
        $sendSMS->send();
    }
}
