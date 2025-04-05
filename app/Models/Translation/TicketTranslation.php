<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class TicketTranslation extends Model
{
    public $timestamps = false;
    protected $table = 'ticket_translations';
    protected $guarded = ['id'];
}
