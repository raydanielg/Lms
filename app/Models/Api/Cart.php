<?php

namespace App\Models\Api;

use App\Models\Cart as Model;

class Cart extends Model
{

    public function getDetailsAttribute(){
       // dd($this->webinar->brief ) ;
        return [
            'id'=>$this->id  ,
            'user'=>$this->user->brief ,
            'webinar'=>$this->webinar->brief??null ,
            'price'=>$this->price ,
            'discount'=>$this->discount ,
            'meeting'=>$this->reserveMeeting->details??null


        ] ;
    }

    public function getDiscountAttribute(){
        if($this->webinar_id){
        return $this->webinar->price - $this->webinar->getDiscount($this->ticket) ;
        }
        return null ;
      //  $cart->webinar->price - $cart->webinar->getDiscount($cart->ticket), 2, ".", ""
    }
    public function getPriceAttribute(){
        if($this->webinar_id){
            return $this->webinar->price  ;
        }
        return $this->reserveMeeting->paid_amount ;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Api\User', 'creator_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function reserveMeeting()
    {
        return $this->belongsTo('App\Models\Api\ReserveMeeting', 'reserve_meeting_id', 'id');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket', 'ticket_id', 'id');
    }

}
