<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketUser extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public static function useTicket($orderItem)
    {
        if ($orderItem->ticket_id) {
            TicketUser::create([
                'ticket_id' => $orderItem->ticket_id,
                'user_id' => $orderItem->user_id,
                'created_at' => time()
            ]);
        }
    }
}
