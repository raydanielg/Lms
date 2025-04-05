<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\RewardAccounting;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function index()
    {
        $this->authorize("panel_rewards_lists");

        $rewardsSettings = getRewardsSettings();

        if (empty($rewardsSettings) or ($rewardsSettings and $rewardsSettings['status'] != '1')) {
            abort(404);
        }

        $user = auth()->user();

        $query = RewardAccounting::where('user_id', $user->id);

        $addictionPoints = deepClone($query)->where('status', RewardAccounting::ADDICTION)
            ->sum('score');

        $spentPoints = deepClone($query)->where('status', RewardAccounting::DEDUCTION)
            ->sum('score');

        $availablePoints = $addictionPoints - $spentPoints;

        $rewards = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $mostPointsUsers = RewardAccounting::selectRaw('*, SUM(CASE WHEN status = "addiction" THEN score ELSE 0 END) as total_points')
            ->groupBy('user_id')
            ->whereHas('user')
            ->with([
                'user'
            ])
            ->orderBy('total_points','desc')
            ->limit(4)
            ->get();

        $earnByExchange = 0;
        if (!empty($rewardsSettings) and !empty($rewardsSettings['exchangeable']) and $rewardsSettings['exchangeable'] == '1') {
            $earnByExchange = $availablePoints / $rewardsSettings['exchangeable_unit'];
        }

        $data = [
            'pageTitle' => trans('update.rewards'),
            'availablePoints' => $availablePoints,
            'totalPoints' => $addictionPoints,
            'spentPoints' => $spentPoints,
            'rewards' => $rewards,
            'rewardsSettings' => $rewardsSettings,
            'earnByExchange' => $earnByExchange,
            'mostPointsUsers' => $mostPointsUsers,
        ];

        return view('web.default.panel.rewards.index', $data);
    }

    public function exchange(Request $request)
    {
        $rewardsSettings = getRewardsSettings();

        if (empty($rewardsSettings) or ($rewardsSettings and $rewardsSettings['status'] != '1')) {
            abort(403);
        }

        $user = auth()->user();

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

        return response()->json([
            'code' => 200
        ]);
    }
}
