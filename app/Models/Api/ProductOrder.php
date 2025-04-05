<?php

namespace App\Models\Api;

//use Illuminate\Database\Eloquent\Model;
use App\Models\ProductOrder as Model;

class ProductOrder extends Model
{
    public function scopeHandleFilters($query)
    {
        $request = request();
        $from = $request->input('from');
        $to = $request->input('to');
        $customer_id = $request->input('customer_id');
        $seller_id = $request->input('seller_id');
        $type = $request->input('type');
        $status = $request->input('status');

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($seller_id) and $seller_id != 'all') {
            $query->where('seller_id', $seller_id);
        }

        if (!empty($customer_id) and $customer_id != 'all') {
            $query->where('buyer_id', $customer_id);
        }

        if (isset($type) and $type !== 'all') {
            $query->whereHas('product', function ($query) use ($type) {
                $query->where('type', $type);
            });
        }

        if (isset($status) and $status !== 'all') {
            $query->where('status', $status);
        }

        return $query;
    }

}
