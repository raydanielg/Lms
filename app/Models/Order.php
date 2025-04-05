<?php

namespace App\Models;

use App\Models\Observers\OrderNumberObserver;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //status
    public static $pending = 'pending';
    public static $paying = 'paying';
    public static $paid = 'paid';
    public static $fail = 'fail';

    //types
    public static $webinar = 'webinar';
    public static $meeting = 'meeting';
    public static $charge = 'charge';
    public static $subscribe = 'subscribe';
    public static $promotion = 'promotion';
    public static $registrationPackage = 'registration_package';
    public static $product = 'product';
    public static $bundle = 'bundle';
    public static $installmentPayment = 'installment_payment';
    public static $gift = 'gift';

    public static $addiction = 'addiction';
    public static $deduction = 'deduction';

    public static $income = 'income';
    public static $asset = 'asset';

    //paymentMethod
    public static $credit = 'credit';
    public static $paymentChannel = 'payment_channel';

    public $timestamps = false;

    protected $guarded = ['id'];


    protected static function boot()
    {
        parent::boot();

        Order::observe(OrderNumberObserver::class);
    }


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItem', 'order_id', 'id');
    }
}
