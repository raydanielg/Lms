<?php

namespace App\Models\Observers;

use App\Models\Accounting;

class AccountingNumberObserver
{
    public function saving(Accounting $model)
    {
        // Check if the number is below zero

        if ($model->amount < 0) {
            $model->amount = 0;
        }
    }
}
