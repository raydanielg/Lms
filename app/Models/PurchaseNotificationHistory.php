<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class PurchaseNotificationHistory extends Model
{
    protected $table = 'purchase_notification_histories';
    public $timestamps = false;
    protected $guarded = ['id'];



}
