<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Affiliate;
use App\Models\Sale;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationBonusController extends Controller
{
    public function index()
    {
        $this->authorize("panel_marketing_registration_bonus");

        $user = auth()->user();
        $registrationBonusSettings = getRegistrationBonusSettings();

        if (empty($registrationBonusSettings['status']) or !$user->enable_registration_bonus) {
            abort(404);
        }

        $accounting = Accounting::query()
            ->where('user_id', $user->id)
            ->where('is_registration_bonus', true)
            ->where('system', false)
            ->first();

        $data = [
            'pageTitle' => trans('update.registration_bonus'),
            'accounting' => $accounting,
            'bonusStatusReferredUsersChart' => $this->bonusStatusReferredUsersChart($user, $registrationBonusSettings),
            'bonusStatusUsersPurchasesChart' => $this->bonusStatusUsersPurchasesChart($user, $registrationBonusSettings),
            'referredUsers' => $this->getReferredUsers($user, $registrationBonusSettings),
        ];

        return view('web.default.panel.marketing.registration_bonus', $data);
    }

    private function bonusStatusReferredUsersChart($user, $registrationBonusSettings)
    {
        if (!empty($registrationBonusSettings['unlock_registration_bonus_with_referral']) and !empty($registrationBonusSettings['number_of_referred_users'])) {
            $condition = $registrationBonusSettings['number_of_referred_users'];
            $userCount = Affiliate::query()->where('affiliate_user_id', $user->id)->count();;

            return [
                'labels' => [trans('update.complete'), trans('update.not_complete')],
                'complete' => ($condition > 0 and $userCount > 0) ? (($userCount / $condition) * 100) : 0,
                'referred_users' => $userCount
            ];
        }

        return null;
    }

    private function bonusStatusUsersPurchasesChart($user, $registrationBonusSettings)
    {
        if (!empty($registrationBonusSettings['enable_referred_users_purchase']) and !empty($registrationBonusSettings['number_of_referred_users'])) {
            $condition = !empty($registrationBonusSettings['purchase_amount_for_unlocking_bonus']) ? $registrationBonusSettings['purchase_amount_for_unlocking_bonus'] : 0;
            $totalUserPurchased = $registrationBonusSettings['number_of_referred_users'];

            $referredUsersId = Affiliate::query()->where('affiliate_user_id', $user->id)
                ->pluck('referred_user_id')
                ->toArray();

            $sales = Sale::query()->select('buyer_id', DB::raw('sum(total_amount) as totalAmount'))
                ->whereIn('buyer_id', $referredUsersId)
                ->whereNull('refund_at')
                ->groupBy('buyer_id')
                ->orderBy('totalAmount', 'desc')
                ->get();

            $userPurchasedCount = 0;

            foreach ($sales as $sale) {
                if (empty($condition) and $sale->totalAmount > 0) {
                    $userPurchasedCount += 1;
                } else if (!empty($condition) and $sale->totalAmount >= $condition) {
                    $userPurchasedCount += 1;
                }
            }


            return [
                'labels' => [trans('update.complete'), trans('update.not_complete')],
                'complete' => ($totalUserPurchased > 0 and $userPurchasedCount > 0) ? (($userPurchasedCount / $totalUserPurchased) * 100) : 0,
                'reached_user_purchased' => $userPurchasedCount,
                'total_user_purchased' => $totalUserPurchased,
            ];
        }

        return null;
    }

    private function getReferredUsers($user, $registrationBonusSettings)
    {
        $users = null;

        if (!empty($registrationBonusSettings['unlock_registration_bonus_with_referral']) and !empty($registrationBonusSettings['number_of_referred_users'])) {
            $referredUsersId = Affiliate::query()->where('affiliate_user_id', $user->id)
                ->pluck('referred_user_id')
                ->toArray();

            if (!empty($referredUsersId)) {
                $users = User::query()->select('id', 'full_name', 'role_name', 'role_id', 'avatar', 'created_at')
                    ->whereIn('users.id', $referredUsersId)
                    ->limit($registrationBonusSettings['number_of_referred_users'])
                    ->get();

                if ($users->isNotEmpty() and !empty($registrationBonusSettings['enable_referred_users_purchase'])) {
                    $sales = Sale::query()->select('buyer_id', DB::raw('sum(total_amount) as totalPurchase'))
                        ->whereIn('buyer_id', $users->pluck('id')->toArray())
                        ->whereNull('refund_at')
                        ->groupBy('buyer_id')
                        ->get();

                    foreach ($users as $user) {
                        $sale = $sales->where('buyer_id', $user->id)->first();

                        if (!empty($sale)) {
                            $user->totalPurchase = $sale->totalPurchase;
                        } else {
                            $user->totalPurchase = 0;
                        }
                    }
                }
            }

        }

        return $users;
    }
}

