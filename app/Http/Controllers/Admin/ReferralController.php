<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReferralHistoryExport;
use App\Exports\ReferralUsersExport;
use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Affiliate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReferralController extends Controller
{
    public function history($export = false)
    {
        $this->authorize('admin_referrals_history');

        $affiliatesQuery = Affiliate::query();

        $affiliateUsersCount = deepClone($affiliatesQuery)->groupBy('affiliate_user_id')->count();

        $allAffiliateAmounts = Accounting::where('is_affiliate_amount', true)
            ->where('system', false)
            ->sum('amount');

        $allAffiliateCommissionAmounts = Accounting::where('is_affiliate_commission', true)
            ->where('system', false)
            ->sum('amount');

        $affiliates = $affiliatesQuery
            ->with([
                'affiliateUser' => function ($query) {
                    $query->select('id', 'full_name', 'role_id', 'role_name');
                },
                'referredUser' => function ($query) {
                    $query->select('id', 'full_name', 'role_id', 'role_name');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.referrals_history'),
            'affiliatesCount' => $affiliates->count(),
            'affiliateUsersCount' => $affiliateUsersCount,
            'allAffiliateAmounts' => $allAffiliateAmounts,
            'allAffiliateCommissionAmounts' => $allAffiliateCommissionAmounts,
            'affiliates' => $affiliates,
        ];

        if ($export) {
            return $affiliates;
        }

        return view('admin.referrals.history', $data);
    }

    public function users($export = false)
    {
        $this->authorize('admin_referrals_users');


        $affiliates = Affiliate::query()
            ->with([
                'affiliateUser' => function ($query) {
                    $query->select('id', 'full_name', 'role_id', 'role_name', 'affiliate');
                    $query->with([
                        'affiliateCode',
                        'userGroup'
                    ]);
                },
            ])
            ->groupBy('affiliate_user_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.users'),
            'affiliates' => $affiliates
        ];

        if ($export) {
            return $affiliates;
        }

        return view('admin.referrals.users', $data);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_referrals_export');

        $type = $request->get('type', 'history');

        if ($type == 'users') {
            $referrals = $this->users(true);

            $export = new ReferralUsersExport($referrals);
        } else {
            $referrals = $this->history(true);

            $export = new ReferralHistoryExport($referrals);
        }

        return Excel::download($export, 'referrals_' . $type . '.xlsx');
    }
}
