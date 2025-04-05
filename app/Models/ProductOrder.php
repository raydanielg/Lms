<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    protected $table = 'product_orders';
    public $timestamps = false;
    protected $guarded = ['id'];

    static $status = ['pending', 'waiting_delivery', 'shipped', 'success', 'canceled'];
    static $waitingDelivery = 'waiting_delivery';
    static $shipped = 'shipped';
    static $success = 'success';
    static $canceled = 'canceled';
    static $pending = 'pending';

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo('App\User', 'seller_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_id', 'id');
    }

    public function sale()
    {
        return $this->belongsTo('App\Models\Sale', 'sale_id', 'id');
    }

    public function gift()
    {
        return $this->belongsTo('App\Models\Gift', 'gift_id', 'id');
    }
}
