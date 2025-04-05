<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CashbackHistoryExport;
use App\Exports\CashbackTransactionsExport;
use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CashbackTransactionsController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('admin_cashback_transactions');

        $query = $this->getQuery();

        $transactions = $this->handleFilters($request, $query)->paginate(10);

        $data = [
            'pageTitle' => trans('update.cashback_transactions'),
            'transactions' => $transactions
        ];

        $user_ids = $request->get('user_ids', null);
        if (!empty($user_ids)) {
            $data['selectedUsers'] = User::query()->whereIn('id', $user_ids)->select('id', 'full_name')->get();
        }

        return view('admin.cashback.transactions', $data);
    }

    private function getQuery()
    {
        return Accounting::query()
            ->leftJoin('order_items', 'accounting.order_item_id', 'order_items.id')
            ->select('accounting.*', DB::raw('order_items.total_amount as purchase_amount'))
            ->where('is_cashback', true)
            //->where('system', false)
            ->where('type', Accounting::$addiction);
    }

    private function handleFilters(Request $request, $query)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $user_ids = $request->get('user_ids', null);
        $target_type = $request->get('target_type');
        $sort = $request->get('sort', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($title)) {

        }

        if (!empty($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        }

        if (!empty($target_type)) {
            $query->where('target_type', $target_type);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'purchase_amount_asc':
                    $query->orderBy('purchase_amount', 'asc');
                    break;
                case 'purchase_amount_desc':
                    $query->orderBy('purchase_amount', 'desc');
                    break;
                case 'cashback_amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'cashback_amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_cashback_transactions');

        $query = $this->getQuery();

        $transactions = $this->handleFilters($request, $query)->get();

        $export = new CashbackTransactionsExport($transactions);

        return Excel::download($export, 'transactions.xlsx');
    }

    public function refund($id)
    {
        $this->authorize('admin_cashback_transactions');

        $transaction = Accounting::query()->findOrFail($id);

        $insert = [
            'user_id' => $transaction->user_id,
            'order_item_id' => $transaction->order_item_id,
            'is_cashback' => true,
            'system' => true,
            'amount' => $transaction->amount,
            'type_account' => Accounting::$asset,
            'type' => Accounting::$addiction,
            'description' => 'Refund ' . $transaction->description,
            'webinar_id' => $transaction->webinar_id,
            'bundle_id' => $transaction->bundle_id,
            'meeting_time_id' => $transaction->meeting_time_id,
            'subscribe_id' => $transaction->subscribe_id,
            'promotion_id' => $transaction->promotion_id,
            'registration_package_id' => $transaction->registration_package_id,
            'product_id' => $transaction->product_id,
            'installment_payment_id' => $transaction->installment_payment_id,
            'installment_order_id' => $transaction->installment_order_id,
            'gift_id' => $transaction->gift_id,
            'created_at' => time()
        ];

        // System
        Accounting::create($insert);

        // User
        $insert['type'] = Accounting::$deduction;
        $insert['system'] = false;
        Accounting::create($insert);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.cashback_transaction_refunded_successful'),
            'status' => 'success'
        ];

        return back()->with(['toast' => $toastData]);
    }


    public function history(Request $request)
    {
        $this->authorize('admin_cashback_history');

        $query = Accounting::query()
            ->leftJoin('order_items', 'accounting.order_item_id', 'order_items.id')
            ->select('accounting.*', DB::raw('sum(accounting.amount) as total_cashback'),
                DB::raw('max(accounting.created_at) as last_cashback'), DB::raw('sum(order_items.total_amount) as purchase_amount'))
            ->where('is_cashback', true)
            ->where('system', false)
            ->where('type', Accounting::$addiction)
            ->groupBy('accounting.user_id');

        $temp = deepClone($query)->get();
        $totalUsers = $temp->count();
        $totalPurchase = $temp->sum('purchase_amount');
        $totalCashback = $temp->sum('total_cashback');

        $transactions = $this->handleFiltersHistory($request, $query)->paginate(10);

        $data = [
            'pageTitle' => trans('update.cashback_transactions_history'),
            'transactions' => $transactions,
            "totalUsers" => $totalUsers,
            "totalPurchase" => $totalPurchase,
            "totalCashback" => $totalCashback,
        ];

        $user_ids = $request->get('user_ids', null);
        if (!empty($user_ids)) {
            $data['selectedUsers'] = User::query()->whereIn('id', $user_ids)->select('id', 'full_name')->get();
        }

        return view('admin.cashback.history', $data);
    }

    private function handleFiltersHistory(Request $request, $query)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $user_ids = $request->get('user_ids', null);
        $min_purchase_amount = $request->get('min_purchase_amount');
        $min_cashback_amount = $request->get('min_cashback_amount');
        $sort = $request->get('sort', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($title)) {

        }

        if (!empty($user_ids)) {
            $query->whereIn('accounting.user_id', $user_ids);
        }

        if (!empty($min_purchase_amount)) {
            $query->where('purchase_amount', '>=', $min_purchase_amount);
        }

        if (!empty($min_cashback_amount)) {
            $query->where('total_cashback', '>=', $min_cashback_amount);
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'purchase_amount_asc':
                    $query->orderBy('purchase_amount', 'asc');
                    break;
                case 'purchase_amount_desc':
                    $query->orderBy('purchase_amount', 'desc');
                    break;
                case 'cashback_amount_asc':
                    $query->orderBy('amount', 'asc');
                    break;
                case 'cashback_amount_desc':
                    $query->orderBy('amount', 'desc');
                    break;
                case 'last_cashback_asc':
                    $query->orderBy('last_cashback', 'asc');
                    break;
                case 'last_cashback_desc':
                    $query->orderBy('last_cashback', 'desc');
                    break;
            }
        } else {
            $query->orderBy('last_cashback', 'desc');
        }

        return $query;
    }

    public function historyExportExcel(Request $request)
    {
        $this->authorize('admin_cashback_history');

        $query = Accounting::query()
            ->leftJoin('order_items', 'accounting.order_item_id', 'order_items.id')
            ->select('accounting.*', DB::raw('sum(accounting.amount) as total_cashback'),
                DB::raw('max(accounting.created_at) as last_cashback'), DB::raw('sum(order_items.total_amount) as purchase_amount'))
            ->where('is_cashback', true)
            ->where('system', false)
            ->where('type', Accounting::$addiction)
            ->groupBy('accounting.user_id');

        $transactions = $this->handleFiltersHistory($request, $query)->get();

        $export = new CashbackHistoryExport($transactions);

        return Excel::download($export, 'history.xlsx');
    }
}
