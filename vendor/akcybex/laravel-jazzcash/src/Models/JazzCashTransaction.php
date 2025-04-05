<?php

namespace AKCybex\JazzCash\Models;

use Illuminate\Database\Eloquent\Model;

class JazzCashTransaction extends Model
{
    protected $table = 'jazzcash_transactions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'txn_ref_no', 'order', 'request', 'response'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'order' => 'array',
        'request' => 'array',
        'response' => 'array',
    ];
}
