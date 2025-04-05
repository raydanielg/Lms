<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class InstallmentOrderPayment extends Model
{
    protected $table = 'installment_order_payments';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function installmentOrder()
    {
        return $this->belongsTo(InstallmentOrder::class, 'installment_order_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'id');
    }

    public function step()
    {
        return $this->belongsTo(SelectedInstallmentStep::class, 'selected_installment_step_id', 'id');
    }
}
