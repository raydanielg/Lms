<?php

namespace App\Models\Api;

use App\Models\Sale as WebSale;

class Sale extends WebSale
{

    public function getDetailsAttribute()
    {

        return [
            'buyer' => !empty($this->buyer) ? $this->buyer->brief : null,
            'type' => $this->type,
            'payment_method' => $this->payment_method,
            'created_at' => $this->created_at,
            'amount' => $this->amount,
            'discount' => $this->discount,
            'total_amount' => $this->total_amount,
            'income' => $this->getIncomeItem(),
            'webinar' => ($this->webinar_id and !empty($this->webinar)) ? $this->webinar->brief : null,
            'meeting' => ($this->meeting_id and !empty($this->meeting)) ? $this->meeting->details : null,
        ];
    }

    public function scopeHandleFilters($query)
    {

        $request = request();
        $from = $request->input('from');
        $to = $request->input('to');
        $student_id = $request->input('student_id');
        $webinar_id = $request->input('webinar_id');
        $type = $request->input('type');

        if (!empty($from) and !empty($to)) {
            $from = strtotime($from);
            $to = strtotime($to);

            $query->whereBetween('created_at', [$from, $to]);
        } else {
            if (!empty($from)) {
                $from = strtotime($from);
                $query->where('created_at', '>=', $from);
            }

            if (!empty($to)) {
                $to = strtotime($to);

                $query->where('created_at', '<', $to);
            }
        }

        if (isset($type) && $type !== 'all') {
            $query->where('type', $type);
        }

        if (!empty($student_id) and $student_id != 'all') {
            $query->where('buyer_id', $student_id);
        }

        if (!empty($webinar_id) and $webinar_id != 'all') {
            $query->where('webinar_id', $webinar_id);
        }

        return $query;

    }

    public function getItemTypeAttribute()
    {

        if ($this->webinar_id) {
            $type = 'class';
        } elseif ($this->meeting_id) {
            $type = 'meeting';
        } else {
            $type = null;
        }

        return $type;
    }


    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Api\Bundle', 'bundle_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo('App\Models\Api\User', 'buyer_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo('App\Model\Api\User', 'seller_id', 'id');
    }

    public function meeting()
    {
        return $this->belongsTo('App\Models\Api\Meeting', 'meeting_id', 'id');
    }

    public function subscribe()
    {
        return $this->belongsTo('App\Models\Subscribe', 'subscribe_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo('App\Models\Promotion', 'promotion_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket', 'ticket_id', 'id');
    }

    public function saleLog()
    {
        return $this->hasOne('App\Models\SaleLog', 'sale_id', 'id');
    }

    public function gift()
    {
        return $this->belongsTo('App\Models\Api\Gift', 'gift_id', 'id');
    }

}
