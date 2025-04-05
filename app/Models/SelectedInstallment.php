<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SelectedInstallment extends Model
{
    protected $table = 'selected_installments';
    public $timestamps = false;
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class, 'installment_id', 'id');
    }

    public function steps()
    {
        return $this->hasMany(SelectedInstallmentStep::class, 'selected_installment_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(InstallmentOrder::class, 'installment_order_id', 'id');
    }

    /********
     * Helpers
     * */

    public function reachedCapacityPercent()
    {
        $orders = InstallmentOrder::query()
            ->where('id', $this->installment_order_id)
            ->whereIn('status', ['open', 'pending_verification'])
            ->count();

        $percent = 0;

        if ($orders > 0 and $this->installment->capacity > 0) {
            $percent = ($orders / $this->installment->capacity) * 100;
        }

        return $percent;
    }

    public function hasCapacity()
    {
        $result = true;

        if (!empty($this->installment->capacity)) {
            $orders = InstallmentOrder::query()
                ->where('id', $this->installment_order_id)
                ->whereIn('status', ['open', 'pending_verification'])
                ->count();

            if ($orders >= $this->installment->capacity) {
                $result = false;
            }
        }

        return $result;
    }


    public function getUpfront($itemPrice = 1)
    {
        $result = 0;

        if (!empty($this->upfront) and $this->upfront > 0) {
            if ($this->upfront_type == 'percent') {
                $result = ($itemPrice * $this->upfront) / 100;
            } else {
                $result = $this->upfront;
            }
        }

        return $result;
    }

    public function totalPayments($itemPrice = 1, $withUpfront = true)
    {
        $total = 0;

        if (!empty($this->upfront) and $withUpfront) {
            if ($this->upfront_type == 'percent') {
                $total += ($itemPrice * $this->upfront) / 100;
            } else {
                $total += $this->upfront;
            }
        }

        foreach ($this->steps as $step) {
            $total += $step->getPrice($itemPrice);
        }

        return $total;
    }

    public function totalInterest($itemPrice = 1, $totalPayments = null)
    {
        if (empty($totalPayments)) {
            $totalPayments = $this->totalPayments($itemPrice);
        }

        $result = 0;
        $tmp = ($totalPayments - $itemPrice);

        if ($tmp > 0) {
            $tmp2 = ($tmp / $itemPrice) * 100;

            if ($tmp2 > 0) {
                $result = number_format($tmp2, 2);
            }
        }

        return $result;
    }

}
