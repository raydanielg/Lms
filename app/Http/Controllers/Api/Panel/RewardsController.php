<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Models\Api\Accounting;
use App\Models\Api\RewardAccounting;
use App\Models\Api\Webinar;
use App\Models\Sale;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    public function index()
    {
        $rewardsSettings = getRewardsSettings();

        if (empty($rewardsSettings) or ($rewardsSettings and $rewardsSettings['status'] != '1')) {
            abort(404);
        }

        $user = apiAuth();

        $query = RewardAccounting::where('user_id', $user->id);

        $addictionPoints = deepClone($query)->where('status', RewardAccounting::ADDICTION)
            ->sum('score');

        $spentPoints = deepClone($query)->where('status', RewardAccounting::DEDUCTION)
            ->sum('score');

        $availablePoints = $addictionPoints - $spentPoints;
        $rewards = $query->orderBy('created_at', 'desc')
            ->get()->map(function ($reward) {
                return $reward->details;
            });

        $mostPointsUsers = RewardAccounting::selectRaw('*,sum(score) as total_points')
            ->groupBy('user_id')
            ->whereHas('user')
            ->orderBy('total_points', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($query) {

                return array_merge(
                    $query->details, ['total_points' => $query->total_points]
                );
            });

        $earnByExchange = 0;
        if (!empty($rewardsSettings) and !empty($rewardsSettings['exchangeable']) and $rewardsSettings['exchangeable'] == '1') {
            $earnByExchange = $availablePoints / $rewardsSettings['exchangeable_unit'];
        }

        $data = [
            // 'pageTitle' => trans('update.rewards'),
            'leader_board' => $mostPointsUsers->shift(),
            'available_points' => (int)$availablePoints,
            'total_points' => (int)$addictionPoints,
            'spent_points' => (int)$spentPoints,
            'rewards' => $rewards,
            'exchangeable' => (int)$rewardsSettings['exchangeable'],
            'earn_by_exchange' => (int)$earnByExchange,
            'most_points_users' => $mostPointsUsers,

        ];

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $data);


    }

    public function exchange(Request $request)
    {
        $rewardsSettings = getRewardsSettings();

        if (empty($rewardsSettings) or ($rewardsSettings and $rewardsSettings['status'] != '1')) {
            abort(403);
        }

        $user = apiAuth();

        $availablePoints = $user->getRewardPoints();
        $earnByExchange = 0;
        if (!empty($rewardsSettings) and !empty($rewardsSettings['exchangeable']) and $rewardsSettings['exchangeable'] == '1') {
            $earnByExchange = $availablePoints / $rewardsSettings['exchangeable_unit'];
        }

        if ($availablePoints > 0 and $earnByExchange > 0) {
            RewardAccounting::makeRewardAccounting($user->id, $availablePoints, 'withdraw', null, false, RewardAccounting::DEDUCTION);

            Accounting::create([
                'user_id' => $user->id,
                'amount' => $earnByExchange,
                'type' => Accounting::$addiction,
                'type_account' => Accounting::$asset,
                'description' => trans('update.exchange_reward_points_to_wallet'),
                'created_at' => time(),
            ]);
        }

        return apiResponse2(1, 'stored', 'api.public.exchange');

    }

    public function courses(Request $request)
    {
        $webinars = Webinar::where('webinars.status', 'active')
            ->where('private', false)->whereNotNull('points')
            ->get()->map(function ($webinar) {
                return $webinar->brief;
            });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'count' => count($webinars),
            'webinars' => $webinars
        ]);


    }

    public function buyWithPoint(Request $request, $id)
    {
        $user = apiAuth();

        $course = Webinar::where('id', $id)
            ->where('status', 'active')
            ->first();
        if (!$course) {
            abort(404);
        }

        if (empty($course->points)) {
            return apiResponse2(0, 'no_points', trans('update.can_not_buy_this_course_with_point'));
        }

        $availablePoints = $user->getRewardPoints();

        if ($availablePoints < $course->points) {
            return   apiResponse2(0, 'no_enough_points', trans('update.you_have_no_enough_points_for_this_course'));

        }

        $checkCourseForSale = $course->canAddToCart($user);

        if ($checkCourseForSale == 'free') {
            return apiResponse2(0, 'free',
                trans('api.cart.free')

            );
        }

        if ($checkCourseForSale != 'ok') {
            return apiResponse2(0, $checkCourseForSale,
                $course->checkCourseForSaleMsg(), null,
                trans('public.request_failed')

            );
        }

        Sale::create([
            'buyer_id' => $user->id,
            'seller_id' => $course->creator_id,
            'webinar_id' => $course->id,
            'type' => Sale::$webinar,
            'payment_method' => Sale::$credit,
            'amount' => 0,
            'total_amount' => 0,
            'created_at' => time(),
        ]);

        RewardAccounting::makeRewardAccounting($user->id, $course->points, 'withdraw', null, false, RewardAccounting::DEDUCTION);
     return   apiResponse2(1, 'paid',trans('update.success_pay_course_with_point_msg'));



    }


}
