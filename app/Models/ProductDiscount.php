<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    protected $table = 'product_discounts';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function getRemainingTimes()
    {
        $current_time = time();
        $date = $this->end_date;
        $difference = $date - $current_time;

        return time2string($difference);
    }

    public function discountRemain()
    {
        $count = $this->count;

        $orderItems = ProductOrder::where('discount_id', $this->id)
            ->whereHas('sale', function ($query) {
                $query->whereNull('refund_at');
            })
            ->count();


        return ($count > 0) ? $count - $orderItems : 0;
    }
}
