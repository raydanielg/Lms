<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StoreOrdersExport implements FromCollection, WithHeadings, WithMapping
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
            trans('admin/main.id'),
            trans('update.customer'),
            trans('update.customer_id'),
            trans('admin/main.seller'),
            trans('update.seller_id'),
            trans('admin/main.type'),
            trans('update.quantity'),
            trans('admin/main.paid_amount'),
            trans('admin/main.discount'),
            trans('admin/main.tax'),
            trans('admin/main.date'),
            trans('admin/main.status'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($order): array
    {
        if ($order->status == \App\Models\ProductOrder::$waitingDelivery) {
            $status = trans('update.product_order_status_waiting_delivery');
        } elseif ($order->status == \App\Models\ProductOrder::$success) {
            $status = trans('update.product_order_status_success');
        } elseif ($order->status == \App\Models\ProductOrder::$shipped) {
            $status = trans('update.product_order_status_shipped');
        } elseif ($order->status == \App\Models\ProductOrder::$canceled) {
            $status = trans('update.product_order_status_canceled');
        }

        return [
            $order->id,
            !empty($order->buyer) ? $order->buyer->full_name : '',
            !empty($order->buyer) ? $order->buyer->id : '',
            !empty($order->seller) ? $order->seller->full_name : '',
            !empty($order->seller) ? $order->seller->id : '',
            !empty($order->product) ? trans('update.product_type_' . $order->product->type) : '',
            $order->quantity,
            !empty($order->sale) ? handlePrice($order->sale->total_amount) : '',
            !empty($order->sale) ? handlePrice($order->sale->discount) : '',
            !empty($order->sale) ? handlePrice($order->sale->tax) : '',
            dateTimeFormat($order->created_at, 'j F Y H:i'),
            $status ?? '',
        ];
    }
}
