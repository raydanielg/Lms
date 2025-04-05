<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Affiliate;
use App\Models\AffiliateCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function affiliates()
    {
        $this->authorize("panel_marketing_affiliates");

        $user = auth()->user();

        $affiliateCode = $user->affiliateCode;

        if (empty($affiliateCode)) {
            $affiliateCode = $this->makeUserAffiliateCode($user);
        }

        $referredUsersCount = Affiliate::where('affiliate_user_id', $user->id)->count();

        $registrationBonus = Accounting::where('is_affiliate_amount', true)
            ->where('system', false)
            ->where('user_id', $user->id)
            ->sum('amount');

        $affiliateBonus = Accounting::where('is_affiliate_commission', true)
            ->where('system', false)
            ->where('user_id', $user->id)
            ->sum('amount');

        $affiliates = Affiliate::where('affiliate_user_id', $user->id)
            ->with([
                'referredUser',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $data = [
            'pageTitle' => trans('panel.affiliates_page'),
            'affiliateCode' => $affiliateCode,
            'registrationBonus' => $registrationBonus,
            'affiliateBonus' => $affiliateBonus,
            'referredUsersCount' => $referredUsersCount,
            'affiliates' => $affiliates,
        ];

        return view('web.default.panel.marketing.affiliates', $data);
    }

    private function makeUserAffiliateCode($user)
    {
        $code = mt_rand(100000, 999999);

        $check = AffiliateCode::where('code', $code)->first();

        if (!empty($check)) {
            return $this->makeUserAffiliateCode($user);
        }

        $affiliateCode = AffiliateCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'created_at' => time()
        ]);

        return $affiliateCode;
    }
}
