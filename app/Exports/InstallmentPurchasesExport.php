<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InstallmentPurchasesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->orders;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/main.user'),
            trans('admin/main.mobile'),
            trans('admin/main.email'),
            trans('admin/main.title'),
            trans('update.target'),
            trans('update.product'),
            trans('update.target_type'),
            trans('panel.purchase_date'),
            trans('financial.total_amount'),
            trans('update.upfront'),
            trans('update.installments_count'),
            trans('update.installments_amount'),
            trans('update.overdue'),
            trans('update.overdue_amount'),
            trans('update.first_unpaid_installment_date'),
            trans('update.days_left'),
            trans('admin/main.status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($order): array
    {
        $product = '';
        $productType = '';

        if (!empty($order->webinar_id)) {
            $product = $order->webinar->title;
            $productType = trans('update.target_types_courses');
        } elseif (!empty($order->bundle_id)) {
            $product = $order->bundle->title;
            $productType = trans('update.target_types_bundles');
        } elseif (!empty($order->product_id)) {
            $product = $order->product->title;
            $productType = trans('update.target_types_store_products');
        } elseif (!empty($order->subscribe_id)) {
            $product = trans('admin/main.purchased_subscribe');
            $productType = trans('update.target_types_subscription_packages');
        } elseif (!empty($order->registration_package_id)) {
            $product = trans('update.purchased_registration_package');
            $productType = trans('update.target_types_registration_packages');
        }

        $upfront = '--';

        $selectedInstallment = $order->selectedInstallment;

        if (!empty($selectedInstallment->upfront)) {
            $upfront = ($selectedInstallment->upfront_type == 'percent') ? $selectedInstallment->upfront . '%' : handlePrice($selectedInstallment->upfront);
        }

        $stepsFixedAmount = $selectedInstallment->steps->where('amount_type', 'fixed_amount')->sum('amount');
        $stepsPercents = $selectedInstallment->steps->where('amount_type', 'percent')->sum('amount');
        $installmentsAmount = ($stepsFixedAmount ? handlePrice($stepsFixedAmount) : '') . ($stepsPercents ? (($stepsFixedAmount ? ' + ' : '') . $stepsPercents . '%') : '');

        $status = "";
        if ($order->status == "pending_verification") {
            $status = trans('update.pending_verification');
        } elseif ($order->status == "open") {
            $status = trans('admin/main.open');
        } elseif ($order->status == "rejected") {
            $status = trans('public.rejected');
        } elseif ($order->status == "canceled") {
            $status = trans('public.canceled');
        } elseif ($order->status == "refunded") {
            $status = trans('update.refunded');
        }

        return [
            $order->user->id . ' - ' . $order->user->full_name,
            $order->user->mobile,
            $order->user->email,
            $selectedInstallment->installment->title,
            trans('update.target_types_'.$selectedInstallment->installment->target_type),
            $product,
            $productType,
            dateTimeFormat($order->created_at, 'j M Y'),
            handlePrice($order->getCompletePrice()),
            $upfront,
            $selectedInstallment->steps_count,
            $installmentsAmount,
            $order->overdue_count,
            handlePrice($order->overdue_amount),
            $order->upcoming_date,
            $order->days_left,
            $status
        ];
    }
}
