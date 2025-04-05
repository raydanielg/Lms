<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class PurchaseNotificationTranslation extends Model
{

    protected $table = 'purchase_notification_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
