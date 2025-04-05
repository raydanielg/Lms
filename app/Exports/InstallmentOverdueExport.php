<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InstallmentOverdueExport implements FromCollection, WithHeadings, WithMapping
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
            trans('admin/main.amount'),
            trans('update.overdue_date')
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

        if ($order->amount_type == 'percent') {
            $amount = "{$order->amount}% (" . handlePrice(($order->getItemPrice() * $order->amount) / 100) . ")";
        } else {
            $amount = handlePrice($order->amount);
        }


        return [
            $order->user->id . ' - ' . $order->user->full_name,
            $order->user->mobile,
            $order->user->email,
            $order->installment->title,
            trans('update.target_types_'.$order->installment->target_type),
            $product,
            $productType,
            $amount,
            dateTimeFormat($order->overdue_date, 'j M Y') . ' (' . dateTimeFormatForHumans($order->overdue_date) . ')',
        ];
    }
}
