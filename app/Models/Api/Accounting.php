<?php

namespace App\Models\Api;

use App\Models\Accounting as Model;

class Accounting extends Model
{
    public function getDetailsAttribute(){

        return [
            'type'=>$this->item ,
            'balance_type' => $this->balance_type,
            'webinar' => ($this->item == 'webinar') ?$this->webinar->brief:null,
            'subscribe' => ($this->item == 'subscribe') ? $this->subscribe->details : null,
            'promotion' => ($this->item == 'promotion') ? $this->promotion: null,
            'registration_package' => ($this->item == 'registration_package') ? $this->registrationPackage: null,
            'description' => $this->description,
            'amount' => number_format($this->amount, 2, ".", "") + 0,
            'created_at' => $this->created_at


        ] ;

    }

    public function getBalanceTypeAttribute(){

        if ($this->type == Accounting::$addiction) {
            $balance_type = 'addition';
        } elseif ($this->type = Accounting::$deduction) {
            $balance_type = 'deduction';
        }

        return $balance_type ;

    }

    public function getItemAttribute(){
        if ($this->webinar_id and $this->webinar) {
            $type = 'webinar';
            $title = $this->webinar->title;
        } elseif ($this->meeting_time_id) {
            $type = 'meeting';
            $title = 'meeting book';
        } elseif ($this->subscribe_id && $this->subscribe) {
            $type = 'subscribe';
        } elseif ($this->promotion_id && $this->promotion) {
            $type = 'promotion';
        }
        elseif ($this->registration_package_id and $this->registrationPackage){
            $type = 'registration_package';
        }
        elseif ($this->store_type == Accounting::$storeManual) {
            $type = 'manual_document';

        } elseif ($this->type == Accounting::$addiction and $this->type_account == self::$asset) {
            $type = 'manual_document';
        } elseif ($this->type == Accounting::$deduction and $this->type_account == self::$income) {
            $type = 'charge_account';

        } else {
            $type = '---';
        }

        return $type ;
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Api\User', 'user_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\Api\User', 'creator_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo('App\Models\Api\Promotion', 'promotion_id', 'id');
    }

    public function subscribe()
    {
        return $this->belongsTo('App\Models\Api\Subscribe', 'subscribe_id', 'id');
    }

    public function meetingTime()
    {
        return $this->belongsTo('App\Models\Api\MeetingTime', 'meeting_time_id', 'id');
    }


}
