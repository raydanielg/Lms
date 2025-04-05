<?php

namespace App\Mixins\RegistrationBonus;

use App\Models\Accounting;
use App\Models\Affiliate;
use App\Models\Sale;
use App\User;
use Illuminate\Support\Facades\DB;

class RegistrationBonusAccounting
{
    public function __construct()
    {

    }

    public function storeRegistrationBonusInstantly($user)
    {
        $registrationBonusSettings = getRegistrationBonusSettings();

        if (!$user->enable_registration_bonus or empty($registrationBonusSettings['status']) or empty($registrationBonusSettings['registration_bonus_amount'])) {
            return false;
        }

        $bonusAmount = !empty($user->registration_bonus_amount) ? $user->registration_bonus_amount : $registrationBonusSettings['registration_bonus_amount'];
        $bonusWallet = $registrationBonusSettings['bonus_wallet'];

        $typeAccount = ($bonusWallet == 'income_wallet') ? Accounting::$income : Accounting::$asset;

        if (!empty($registrationBonusSettings['unlock_registration_bonus_instantly'])) {
            // As soon as the user registers, the bonus will be activated.

            Accounting::createRegistrationBonusUserAmountAccounting($user->id, $bonusAmount, $typeAccount);
        }
    }


    public function storeRegistrationBonus($user)
    {
        $registrationBonusSettings = getRegistrationBonusSettings();

        if (!$user->enable_registration_bonus or empty($registrationBonusSettings['status']) or empty($registrationBonusSettings['registration_bonus_amount'])) {
            return false;
        }

        $bonusAmount = !empty($user->registration_bonus_amount) ? $user->registration_bonus_amount : $registrationBonusSettings['registration_bonus_amount'];
        $bonusWallet = $registrationBonusSettings['bonus_wallet'];
        $typeAccount = ($bonusWallet == 'income_wallet') ? Accounting::$income : Accounting::$asset;

        if (empty($registrationBonusSettings['unlock_registration_bonus_instantly'])) {
            $numberOfReferredUsers = 0; // How many people must register through the link or individual code to unlock the prize
            $purchaseAmountForUnlockingBonus = 0;
            $checkJustHasPurchase = false;

            if (!empty($registrationBonusSettings['unlock_registration_bonus_with_referral']) and !empty($registrationBonusSettings['number_of_referred_users'])) {
                $numberOfReferredUsers = $registrationBonusSettings['number_of_referred_users'];
            }

            if (!empty($registrationBonusSettings['enable_referred_users_purchase']) and !empty($registrationBonusSettings['purchase_amount_for_unlocking_bonus'])) {
                $purchaseAmountForUnlockingBonus = $registrationBonusSettings['purchase_amount_for_unlocking_bonus'];

                /*
                * Users who are referred by the individual link must buy that amount in order for the condition of money release to be established
                * (this amount is calculated separately for each user).
                * Also, if this field is empty, it means that the amount is not a criterion for us,
                * the only thing that matters is that the user has made a purchase.
                *  with any amount (the amount charged to the purchase account is not taken into account)
                * */
            } elseif (!empty($registrationBonusSettings['enable_referred_users_purchase'])) {
                $checkJustHasPurchase = true;
            }

            $unlockedBonus = true;

            if (!empty($numberOfReferredUsers)) {
                $referredUsersCount = Affiliate::query()->where('affiliate_user_id', $user->id)->count();

                if ($referredUsersCount < $numberOfReferredUsers) {
                    $unlockedBonus = false;
                }

                if ($unlockedBonus and (!empty($purchaseAmountForUnlockingBonus) or $checkJustHasPurchase)) {
                    $referredUsersId = Affiliate::query()->where('affiliate_user_id', $user->id)
                        ->pluck('referred_user_id')
                        ->toArray();

                    if (!empty($referredUsersId)) {
                        $sales = Sale::query()->select('buyer_id', DB::raw('sum(total_amount) as totalAmount'))
                            ->whereIn('buyer_id', $referredUsersId)
                            ->whereNull('refund_at')
                            ->groupBy('buyer_id')
                            ->orderBy('totalAmount', 'desc')
                            ->get();

                        $reachedCount = 0;

                        foreach ($sales as $sale) {
                            if ($checkJustHasPurchase and $sale->totalAmount > 0) {
                                $reachedCount += 1;
                            } else if (!empty($purchaseAmountForUnlockingBonus) and $sale->totalAmount >= $purchaseAmountForUnlockingBonus) {
                                $reachedCount += 1;
                            }
                        }

                        if ($reachedCount < $numberOfReferredUsers) {
                            $unlockedBonus = false;
                        }
                    } else {
                        $unlockedBonus = false;
                    }
                }
            } else {
                $unlockedBonus = false;
            }


            if ($unlockedBonus) {
                Accounting::createRegistrationBonusUserAmountAccounting($user->id, $bonusAmount, $typeAccount);

                $notifyOptions = [
                    '[u.name]' => $user->full_name,
                    '[amount]' => handlePrice($bonusAmount),
                ];
                sendNotification("registration_bonus_unlocked", $notifyOptions, $user->id);
                sendNotification("registration_bonus_unlocked_for_admin", $notifyOptions, 1);
            }
        }
    }

    public function checkBonusAfterSale($buyerId)
    {
        $checkReferred = Affiliate::query()
            ->where('referred_user_id', $buyerId)
            ->first();

        if (!empty($checkReferred)) {
            $affiliateUser = User::query()->where('id', $checkReferred->affiliate_user_id)->first();

            if (!empty($affiliateUser)) {
                $this->storeRegistrationBonus($affiliateUser);
            }
        }
    }
}
