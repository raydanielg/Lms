<?php

namespace App\Http\Controllers\Admin\traits;


use App\Exports\InstallmentOverdueExport;
use App\Exports\InstallmentOverdueHistoriesExport;
use App\Models\Accounting;
use App\Models\InstallmentOrder;
use App\Models\InstallmentOrderPayment;
use App\Models\InstallmentStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

trait InstallmentOverdueTrait
{
    public function overdueLists(Request $request)
    {
        $this->authorize('admin_installments_overdue_lists');

        $orders = $this->getOverdueListsQuery($request)
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.overdue_installments'),
            'orders' => $orders
        ];

        return view('admin.financial.installments.overdue_installments', $data);
    }

    private function getOverdueListsQuery(Request $request)
    {
        $time = time();

        $query = InstallmentOrder::query()
            ->join('selected_installments', 'installment_orders.id', 'selected_installments.installment_order_id')
            ->join('selected_installment_steps', 'selected_installments.id', 'selected_installment_steps.selected_installment_id')
            ->leftJoin('installment_order_payments', 'installment_order_payments.selected_installment_step_id', 'selected_installment_steps.id')
            ->select('installment_orders.*', 'selected_installment_steps.amount', 'selected_installment_steps.amount_type',
                DB::raw('((selected_installment_steps.deadline * 86400) + installment_orders.created_at) as overdue_date')
            )
            ->whereRaw("((selected_installment_steps.deadline * 86400) + installment_orders.created_at) < {$time}")
            ->where(function ($query) { // Where Doesnt Have payment
                $query->whereRaw("installment_order_payments.id < 1");
                $query->orWhereRaw("installment_order_payments.id is null");
            })
            ->where('installment_orders.status', 'open')
            ->orderBy('overdue_date', 'asc');

        return $query;
    }

    public function overdueListsExportExcel(Request $request)
    {
        $this->authorize('admin_installments_overdue_lists');

        $orders = $this->getOverdueListsQuery($request)->get();

        $export = new InstallmentOverdueExport($orders);
        return Excel::download($export, 'InstallmentOverdue.xlsx');
    }

    public function overdueHistories(Request $request)
    {
        $this->authorize('admin_installments_overdue_lists');

        $orders = $this->getOverdueHistoriesQuery($request)
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.overdue_installments'),
            'orders' => $orders
        ];

        return view('admin.financial.installments.overdue_history', $data);
    }

    private function getOverdueHistoriesQuery(Request $request)
    {
        $time = time();

        $query = InstallmentOrder::query()
            ->join('selected_installments', 'installment_orders.id', 'selected_installments.installment_order_id')
            ->join('selected_installment_steps', 'selected_installments.id', 'selected_installment_steps.selected_installment_id')
            ->leftJoin('installment_order_payments', 'installment_order_payments.selected_installment_step_id', 'selected_installment_steps.id')
            ->select('installment_orders.*', 'selected_installment_steps.amount', 'selected_installment_steps.amount_type',
                DB::raw('((selected_installment_steps.deadline * 86400) + installment_orders.created_at) as overdue_date'),
                DB::raw('installment_order_payments.created_at as paid_at'),
                DB::raw('selected_installment_steps.deadline as deadline')
            )
            ->whereRaw("((selected_installment_steps.deadline * 86400) + installment_orders.created_at) < {$time}")
            ->where('installment_orders.status', 'open')
            ->where(function ($query) {
                $query->where(function ($query) { // Where Doesnt Have payment
                    $query->whereRaw("installment_order_payments.id < 1");
                    $query->orWhereRaw("installment_order_payments.id is null");
                });
                $query->orWhereRaw('installment_order_payments.created_at > ((selected_installment_steps.deadline * 86400) + installment_orders.created_at)');
            })
            ->orderBy('overdue_date', 'asc');

        return $query;
    }

    public function overdueHistoriesExportExcel(Request $request)
    {
        $this->authorize('admin_installments_overdue_lists');

        $orders = $this->getOverdueHistoriesQuery($request)
            ->paginate(10);

        $export = new InstallmentOverdueHistoriesExport($orders);
        return Excel::download($export, 'InstallmentOverdueHistories.xlsx');
    }
}
