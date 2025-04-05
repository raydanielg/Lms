<?php

namespace App\Models\Observers;

use App\Models\Order;

class OrderNumberObserver
{
    public function saving(Order $model)
    {
        // Check if the number is below zero

        if ($model->tax < 0) {
            $model->tax = 0;
        }

        if ($model->total_amount < 0) {
            $model->total_amount = 0;
        }

        if ($model->total_discount < 0) {
            $model->total_discount = 0;
        }
    }
}
