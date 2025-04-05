<?php

namespace App\Models\Api;

use App\Models\Ticket as Model;

class Ticket extends Model
{
    //
    public function getDetailsAttribute()
    {
        if (!empty($this->webinar_id)) {
            $item = $this->webinar;
        } elseif (!empty($this->bundle_id)) {
            $item = $this->bundle;
        }

        $price = !empty($item) ? $item->price - $item->getDiscount($this) : 0;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'sub_title' => $this->getSubTitle(),
            'discount' => $this->discount,
            //  'price_with_ticket_discount'=>$this->price -  ($ticket->discount) * $this->price/100 ,
            //  'price_with_ticket_discount' => $this->price - $this->getDiscount($ticket),
            'price_with_ticket_discount' => $price,

            //  'order' => $ticket->order,
            'is_valid' => $this->isValid(),

        ];
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function bundle()
    {
        return $this->belongsTo('App\Models\Api\Bundle', 'bundle_id', 'id');
    }
}
