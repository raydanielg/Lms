<?php
namespace App\Models\Api ;
use App\Models\Setting as WebSetting ;

class Setting extends WebSetting {

    public static $register_method ;
    public static $offline_bank_account ;
    public static $user_language ;
    public static $payment_channels ;
    public static $minimum_payout_amount ;
    public static $currency ;

   public function __construct()
   {
       self::$register_method= 'ff' ;
    }

    public static function getRegisterMethodAttribute(){
     return   self::$register_method= 'ff' ;
    }

     
}