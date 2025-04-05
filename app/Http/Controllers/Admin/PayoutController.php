<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PayoutExport;
use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\OfflineBank;
use App\Models\Payout;
use App\Models\Role;
use App\Models\Setting;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_payouts_list');

        $payoutType = $request->get('payout', 'requests'); //requests or history

        $query = Payout::query();
        if ($payoutType == 'requests') {
            $query->where('status', Payout::$waiting);
        } else {
            $query->where('status', '!=', Payout::$waiting);
        }

        $payouts = $this->filters($query, $request)
            ->paginate(10);

        $roles = Role::all();

        $offlineBanks = OfflineBank::query()
            ->orderBy('created_at', 'desc')
            ->with([
                'specifications'
            ])
            ->get();

        $data = [
            'pageTitle' => ($payoutType == 'requests') ? trans('financial.payouts_requests') : trans('financial.payouts_history'),
            'payouts' => $payouts,
            'roles' => $roles,
            'offlineBanks' => $offlineBanks
        ];

        $user_ids = $request->get('user_ids', []);

        if (!empty($user_ids)) {
            $data['users'] = User::select('id', 'full_name')
                ->whereIn('id', $user_ids)->get();
        }

        return view('admin.financial.payout.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $user_ids = $request->get('user_ids', []);
        $role_id = $request->get('role_id', null);
        $account_type = $request->get('account_type', null);
        $sort = $request->get('sort', null);

        if (!empty($search)) {
            $ids = User::where('full_name', 'like', "%$search%")->pluck('id')->toArray();
            $user_ids = array_merge($user_ids, $ids);
        }

        if (!empty($role_id)) {
            $role = Role::where('id', $role_id)->first();

            if (!empty($role)) {
                $ids = $role->users()->pluck('id')->toArray();
                $user_ids = array_merge($user_ids, $ids);
            }
        }

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($user_ids) and count($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        }

        if (!empty($account_type)) {
            $query->where('account_bank_name', $account_type);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function reject($id)
    {
        $this->authorize('admin_payouts_reject');

        $payout = Payout::findOrFail($id);
        $payout->update(['status' => Payout::$reject]);

        return back();
    }

    public function payout($id)
    {
        $this->authorize('admin_payouts_payout');

        $payout = Payout::findOrFail($id);
        $getFinancialSettings = getFinancialSettings();

        if ($payout->user->getPayout() < $getFinancialSettings['minimum_payout']) {
            return back()->with('msg', trans('public.income_los_then_minimum_payout'));
        }

        Accounting::create([
            'creator_id' => auth()->user()->id,
            'user_id' => $payout->user_id,
            'amount' => $payout->amount,
            'type' => Accounting::$deduction,
            'type_account' => Accounting::$income,
            'description' => trans('financial.payout_request'),
            'created_at' => time(),
        ]);

        $notifyOptions = [
            '[payout.amount]' => $payout->amount,
            '[payout.account]' => $payout->account_bank_name
        ];
        sendNotification('payout_proceed', $notifyOptions, $payout->user_id);

        $payout->update(['status' => Payout::$done]);

        return back();
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_payouts_export_excel');

        $payoutType = $request->get('payout', 'requests'); //requests or history

        $query = Payout::query();
        if ($payoutType == 'requests') {
            $query->where('status', Payout::$waiting);
        } else {
            $query->where('status', '!=', Payout::$waiting);
        }

        $payouts = $this->filters($query, $request)->get();

        $export = new PayoutExport($payouts);

        $filename = ($payoutType == 'requests') ? trans('financial.payouts_requests') : trans('financial.payouts_history');

        return Excel::download($export, $filename . '.xlsx');
    }
}
