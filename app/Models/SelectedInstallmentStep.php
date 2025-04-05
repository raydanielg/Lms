<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SelectedInstallmentStep extends Model
{
    protected $table = 'selected_installment_steps';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function selectedInstallment()
    {
        return $this->belongsTo(SelectedInstallment::class, 'selected_installment_id', 'id');
    }

    public function installmentStep()
    {
        return $this->belongsTo(InstallmentStep::class, 'installment_step_id', 'id');
    }

    public function orderPayment()
    {
        return $this->hasOne(InstallmentOrderPayment::class, 'selected_installment_step_id', 'id');
    }

    /********
     * Helpers
     * */

    public function getPrice($itemPrice = 1)
    {
        if ($this->amount_type == 'percent') {
            return ($itemPrice * $this->amount) / 100;
        } else {
            return $this->amount;
        }
    }

    public function getDeadlineTitle($itemPrice = 1)
    {
        $percentText = ($this->amount_type == 'percent') ? "({$this->amount}%)" : '';

        // $100 after 30 days
        return trans('update.amount_after_n_days', ['amount' => handlePrice($this->getPrice($itemPrice)), 'days' => $this->deadline, 'percent' => $percentText]);
    }
}
