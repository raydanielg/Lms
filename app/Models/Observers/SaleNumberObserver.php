<?php

namespace App\Models\Observers;

use App\Models\Sale;

class SaleNumberObserver
{
    public function saving(Sale $model)
    {
        // Check if the number is below zero

        if ($model->tax < 0) {
            $model->tax = 0;
        }

        if ($model->commission < 0) {
            $model->commission = 0;
        }

        if ($model->discount < 0) {
            $model->discount = 0;
        }

        if ($model->total_amount < 0) {
            $model->total_amount = 0;
        }
    }
}
