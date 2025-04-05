<?php

namespace App\Models;

use App\Mixins\RegistrationBonus\RegistrationBonusAccounting;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    protected $table = 'affiliates';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function affiliateUser()
    {
        return $this->belongsTo('App\User', 'affiliate_user_id', 'id');
    }

    public function referredUser()
    {
        return $this->belongsTo('App\User', 'referred_user_id', 'id');
    }

    public function getTotalAffiliateRegistrationAmounts()
    {
        $amount = Accounting::where('user_id', $this->affiliate_user_id)
            ->where('is_affiliate_amount', true)
            ->where('system', false)
            ->sum('amount');

        return $amount;
    }

    public function getTotalAffiliateCommissions()
    {
        $amount = Accounting::where('user_id', $this->affiliate_user_id)
            ->where('is_affiliate_commission', true)
            ->where('system', false)
            ->sum('amount');

        return $amount;
    }

    public function getAffiliateRegistrationAmountsOfEachReferral()
    {
        $amount = Accounting::where('user_id', $this->affiliate_user_id)
            ->where('referred_user_id', $this->referred_user_id)
            ->where('is_affiliate_amount', true)
            ->where('system', false)
            ->sum('amount');

        return $amount;
    }

    public function getTotalAffiliateCommissionOfEachReferral()
    {
        $amount = Accounting::where('user_id', $this->affiliate_user_id)
            ->where('referred_user_id', $this->referred_user_id)
            ->where('is_affiliate_commission', true)
            ->where('system', false)
            ->sum('amount');

        return $amount;
    }

    public function getReferredAmount()
    {
        $amount = Accounting::where('user_id', $this->referred_user_id)
            ->where('referred_user_id', null)
            ->where('is_affiliate_amount', true)
            ->where('system', false)
            ->sum('amount');

        return $amount;
    }

    public static function storeReferral($user, $code)
    {
        $referralSettings = getReferralSettings();
        $affiliateStatus = (!empty($referralSettings) and !empty($referralSettings['status']));

        if ($affiliateStatus) {
            $affiliateCode = AffiliateCode::where('code', $code)->first();

            $affiliateUser = User::find($affiliateCode->user_id);

            $checkAffiliate = self::where('referred_user_id', $user->id)->first();

            if (empty($checkAffiliate) and !empty($affiliateCode) and !empty($affiliateUser) and $affiliateUser->affiliate) {
                self::create([
                    'affiliate_user_id' => $affiliateUser->id,
                    'referred_user_id' => $user->id,
                    'created_at' => time(),
                ]);

                $affiliate_user_amount = (!empty($referralSettings) and !empty($referralSettings['affiliate_user_amount'])) ? $referralSettings['affiliate_user_amount'] : 0;
                $referred_user_amount = (!empty($referralSettings) and !empty($referralSettings['referred_user_amount'])) ? $referralSettings['referred_user_amount'] : 0;

                if ($affiliate_user_amount) {
                    Accounting::createAffiliateUserAmountAccounting($affiliateUser->id, $user->id, $affiliate_user_amount);
                }

                if ($referred_user_amount) {
                    Accounting::createAffiliateUserAmountAccounting($user->id, null, $referred_user_amount);
                }

                $rewardScore = RewardAccounting::calculateScore(Reward::REFERRAL);
                RewardAccounting::makeRewardAccounting($affiliateUser->id, $rewardScore, Reward::REFERRAL, $user->id, true);

                $registrationBonusAccounting = new RegistrationBonusAccounting();
                $registrationBonusAccounting->storeRegistrationBonus($affiliateUser);


                $notifyOptions = [
                    '[u.name]' => $user->full_name,
                    '[time.date]' => dateTimeFormat(time(), 'j M Y')
                ];
                sendNotification("new_referral_user", $notifyOptions, $affiliateUser->id);
            }
        }


    }
}
