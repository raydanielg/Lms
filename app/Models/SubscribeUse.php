<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribeUse extends Model
{
    protected $table = 'subscribe_uses';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    public function subscribe()
    {
        return $this->belongsTo('App\Models\Subscribe', 'subscribe_id', 'id');
    }

    public function sale()
    {
        return $this->hasOne('App\Models\Sale', 'id', 'sale_id');
    }

    public function installmentOrder()
    {
        return $this->belongsTo('App\Models\InstallmentOrder', 'installment_order_id', 'id');
    }
}
