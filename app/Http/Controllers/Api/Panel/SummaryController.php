<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Comment;
use App\Models\Meeting;
use App\Models\ReserveMeeting;
use App\Models\Sale;
use App\Models\Support;
use App\Models\Webinar;
use App\Models\Cart;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SummaryController extends Controller
{



    public function list(Request $request)
    {
        $user = apiAuth();
        //  $user = User::find(869);
        $data['offline'] = $user->offline;
        $data['spent_points'] = $user->spent_points;
        $data['total_points'] = $user->total_points;
        $data['available_points'] = $user->available_points;
        $data['role_name'] = $user->role_name;
        $data['full_name'] = $user->full_name;
        $data['organ_id'] = $user->organ_id;
        $data['financial_approval'] = $user->financial_approval;
        // $nextBadge = $user->getBadges(true, true);

        $unreadNotification = $user->getUnReadNotifications();

        $data['unread_notifications'] = [
            'count' => count($unreadNotification),
            'notifications' => NotificationResource::collection($unreadNotification),
        ];

        $data['unread_noticeboards'] = $user->getUnreadNoticeboards();
        //  $data['unread_notifications_count'] = count($user->getUnReadNotifications());

        $data['balance'] = $user->getAccountingBalance();
        $drawable = $user->getPayout();
        $can_drawable = ($drawable > ((!empty($getFinancialSettings) and !empty($getFinancialSettings['minimum_payout'])) ? (int)$getFinancialSettings['minimum_payout'] : 0));
        $data['can_drawable'] = $can_drawable;

        $nextBadge = $user->getBadges(true, true);
        $data['badges'] = [
            'next_badge' => (!empty($nextBadge) and !empty($nextBadge['badge'])) ? $nextBadge['badge']->title : '',
            'percent' => !empty($nextBadge) ? $nextBadge['percent'] : 0,
            'earned' => (!empty($nextBadge) and !empty($nextBadge['earned'])) ? $nextBadge['earned']->title : '',
        ];

        $carts = Cart::where('creator_id', $user->id)->count();
        $data['count_cart_items'] = $carts;


        $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id')->toArray();

        $pendingAppointments = ReserveMeeting::whereIn('meeting_id', $meetingIds)
            ->whereHas('sale')
            ->where('status', ReserveMeeting::$pending)
            ->count();

        $data['pendingAppointments'] = $pendingAppointments;


        $time = time();
        $firstDayMonth = strtotime(date('Y-m-01', $time));// First day of the month.
        $lastDayMonth = strtotime(date('Y-m-t', $time));// Last day of the month.

        $monthlySales = Sale::where('seller_id', $user->id)
            ->whereNull('refund_at')
            ->whereBetween('created_at', [$firstDayMonth, $lastDayMonth])
            ->get();

        $data['monthlySalesCount'] = count($monthlySales) ? $monthlySales->sum('total_amount') : 0;
        $data['monthlyChart'] = $this->getMonthlySalesOrPurchase($user);

        $webinarsIds = $user->getPurchasedCoursesIds();
        $webinars = Webinar::whereIn('id', $webinarsIds)
            ->where('status', 'active')
            ->get();
        $data['webinarsCount'] = count($webinars);

        $reserveMeetings = ReserveMeeting::where('user_id', $user->id)
            ->whereHas('sale')
            ->where('status', ReserveMeeting::$open)
            ->get();
        $data['reserveMeetingsCount'] = count($reserveMeetings);

        if (!$user->isUser()) {

            $userWebinarsIds = $user->webinars->pluck('id')->toArray();
            $supports = Support::whereIn('webinar_id', $userWebinarsIds)->where('status', 'open')->get();

            $comments = Comment::whereIn('webinar_id', $userWebinarsIds)
                ->where('status', 'active')
                ->whereNull('viewed_at')
                ->get();

            $data['supportsCount'] = count($supports);
            $data['commentsCount'] = count($comments);

        } else {


            $supports = Support::where('user_id', $user->id)
                ->whereNotNull('webinar_id')
                ->where('status', 'open')
                ->get();
            $comments = Comment::where('user_id', $user->id)
                ->whereNotNull('webinar_id')
                ->where('status', 'active')
                ->get();


            $data['supportsCount'] = count($supports);
            $data['commentsCount'] = count($comments);
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $data);
    }

    private function getMonthlySalesOrPurchase($user)
    {
        $months = [];
        $data = [];

        // all 12 months
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(date('Y'), $month);

            $start_date = $date->timestamp;
            $end_date = $date->copy()->endOfMonth()->timestamp;

            $months[] = trans('panel.month_' . $month);

            if (!$user->isUser()) {
                $monthlySales = Sale::where('seller_id', $user->id)
                    ->whereNull('refund_at')
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->sum('total_amount');

                $data[] = round($monthlySales, 0);
            } else {
                $monthlyPurchase = Sale::where('buyer_id', $user->id)
                    ->whereNull('refund_at')
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->count();

                $data[] = $monthlyPurchase;
            }
        }

        return [
            'months' => $months,
            'data' => $data
        ];
    }


}

?>
