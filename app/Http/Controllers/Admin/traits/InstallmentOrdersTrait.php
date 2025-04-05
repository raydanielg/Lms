<?php

namespace App\Http\Controllers\Admin\traits;


use App\Mixins\Installment\InstallmentAccounting;
use App\Models\InstallmentOrder;
use Illuminate\Support\Facades\DB;

trait InstallmentOrdersTrait
{
    public function details($orderId)
    {
        $this->authorize('admin_installments_orders');

        $order = InstallmentOrder::query()->findOrFail($orderId);

        $topStats = $this->getDetailsTopStats($order->user);

        $data = [
            'pageTitle' => trans('update.installment_verification') . ' - '.$order->user->full_name,
            'order' => $order,
            'payments' => $order->payments,
            'installment' => $order->selectedInstallment,
            'attachments' => $order->attachments,
            'itemPrice' => $order->getItemPrice(),
        ];

        $data = array_merge($data, $topStats);

        return view('admin.financial.installments.verification_request_details', $data);
    }

    private function getDetailsTopStats($user)
    {
        $query = InstallmentOrder::query()
            ->where('user_id', $user->id)
            ->where('status', '!=', 'paying');

        /* Open Installments */
        $openInstallments = deepClone($query)->where('status', 'open')->get();

        $openInstallmentsCount = count($openInstallments);
        $openInstallmentsAmount = 0;

        $finishedInstallmentsCount = 0;
        $finishedInstallmentsAmount = 0;

        foreach ($openInstallments as $openInstallment) {
            $itemPrice = $openInstallment->getItemPrice();

            $openInstallmentsAmount += $openInstallment->getCompletePrice($itemPrice);

            if ($openInstallment->isCompleted()) {
                $finishedInstallmentsCount += 1;
                $finishedInstallmentsAmount += $openInstallment->getCompletePrice($itemPrice);
            }
        }

        /* Pending Verification Installments */
        $pendingVerifications = deepClone($query)->where('status', 'pending_verification')->count();

        $overdueInstallmentsCount = $this->getDetailsOverdueInstallments($user);


        return [
            'openInstallments' => ['count' => $openInstallmentsCount, 'amount' => $openInstallmentsAmount],
            'finishedInstallments' => ['count' => $finishedInstallmentsCount, 'amount' => $finishedInstallmentsAmount],
            'pendingVerifications' => $pendingVerifications,
            'overdueInstallmentsCount' => $overdueInstallmentsCount,
        ];
    }

    private function getDetailsOverdueInstallments($user)
    {
        $orders = InstallmentOrder::query()
            ->where('user_id', $user->id)
            ->where('installment_orders.status', 'open')
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            if ($order->checkOrderHasOverdue()) {
                $count += 1;
            }
        }

        return $count;
    }


    public function approve($orderId)
    {
        $this->authorize('admin_installments_orders');

        $order = InstallmentOrder::query()->findOrFail($orderId);

        $order->update([
            'status' => 'open'
        ]);


        /* Create Seller Accounting */
        $installmentAccounting = new InstallmentAccounting();
        $installmentAccounting->createAccountingForSeller($order);


        $notifyOptions = [
            '[installment_title]' => $order->installment->main_title,
            '[time.date]' => dateTimeFormat(time(), 'j M Y - H:i'),
        ];

        sendNotification("approve_installment_verification_request", $notifyOptions, $order->user_id);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.order_status_changes_to_approved'),
            'status' => 'success'
        ];

        return back()->with(['toast' => $toastData]);
    }

    public function reject($orderId)
    {
        $this->authorize('admin_installments_orders');

        $order = InstallmentOrder::query()->findOrFail($orderId);

        $installmentRefund = new InstallmentAccounting();
        $installmentRefund->refundOrder($order);

        $order->update([
            'status' => 'rejected'
        ]);

        $notifyOptions = [
            '[installment_title]' => $order->installment->main_title,
            '[time.date]' => dateTimeFormat(time(), 'j M Y - H:i'),
        ];

        sendNotification("reject_installment_verification_request", $notifyOptions, $order->user_id);


        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.order_status_changes_to_rejected'),
            'status' => 'success'
        ];

        return back()->with(['toast' => $toastData]);
    }

    public function cancel($orderId)
    {
        $this->authorize('admin_installments_orders');

        $order = InstallmentOrder::query()->findOrFail($orderId);

        $order->update([
            'status' => 'canceled'
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.order_status_changes_to_canceled'),
            'status' => 'success'
        ];

        return back()->with(['toast' => $toastData]);
    }

    public function refund($orderId)
    {
        $this->authorize('admin_installments_orders');

        $order = InstallmentOrder::query()->findOrFail($orderId);

        $installmentRefund = new InstallmentAccounting();
        $installmentRefund->refundOrder($order);

        $order->update([
            'status' => 'refunded',
            'refund_at' => time(),
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.installment_refunded_successful'),
            'status' => 'success'
        ];

        return back()->with(['toast' => $toastData]);
    }

    public function downloadAttachment($orderId, $attachmentId)
    {
        $this->authorize('admin_installments_orders');

        $order = InstallmentOrder::query()->findOrFail($orderId);
        $attachment = $order->attachments()->where('id', $attachmentId)->first();

        if (!empty($attachment)) {
            $filePath = public_path($attachment->file);

            if (file_exists($filePath)) {
                $extension = \Illuminate\Support\Facades\File::extension($filePath);

                $fileName = str_replace(' ', '-', $attachment->title);
                $fileName = str_replace('.', '-', $fileName);
                $fileName .= '.' . $extension;

                $headers = array(
                    'Content-Type: application/*',
                );

                return response()->download($filePath, $fileName, $headers);
            }

            $toastData = [
                'title' => trans('update.file_not_found'),
                'msg' => trans('update.the_address_entered_for_the_file_is_invalid_or_the_file_has_been_deleted'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }

        abort(404);
    }
}
