<?php

namespace App\Models\Observers;

use App\Models\OrderItem;

class OrderItemNumberObserver
{
    public function saving(OrderItem $model)
    {
        // Check if the number is below zero

        if ($model->total_amount < 0) {
            $model->total_amount = 0;
        }

        if ($model->tax_price < 0) {
            $model->tax_price = 0;
        }

        if ($model->commission_price < 0) {
            $model->commission_price = 0;
        }

        if ($model->discount < 0) {
            $model->discount = 0;
        }
    }
}
