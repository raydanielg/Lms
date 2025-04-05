<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentChannel extends Model
{
    protected $table = 'payment_channels';
    protected $guarded = ['id'];
    public $timestamps = false;

    static $classes = [
        'Paypal', 'Payu', 'Razorpay'
    ];

    static $gatewayIgnoreRedirect = [
        'Paypal', 'Payu', 'Razorpay'
    ];

    static $paypal = 'Paypal';
    static $payu = 'Payu';
    static $razorpay = 'Razorpay';
   

    public function getCredentialsAttribute()
    {
        $credentials = $this->attributes['credentials'];

        if (!empty($credentials)) {
            $credentials = json_decode($credentials, true);
        }

        return $credentials;
    }

    public function getCurrenciesAttribute()
    {
        if (!empty($this->attributes['currencies'])) {
            return json_decode($this->attributes['currencies'], true);
        }

        return [];
    }
}
