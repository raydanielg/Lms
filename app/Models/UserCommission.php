<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCommission extends Model
{
    protected $table = "user_commissions";
    public $timestamps = false;
    protected $guarded = ['id'];


    static $sources = [
        'courses',
        'bundles',
        'virtual_products',
        'physical_products',
        'meetings',
    ];

    public function calculatePrice($price)
    {
        if ($this->type == "percent") {
            $commissionPrice = $price > 0 ? (($price * $this->value) / 100) : 0;
        } else {
            $commissionPrice = $this->value;
        }

        return $commissionPrice;
    }
}
