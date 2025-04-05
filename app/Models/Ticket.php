<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Ticket extends Model implements TranslatableContract
{
    use Translatable;

    public $timestamps = false;
    protected $table = 'tickets';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function isValid()
    {
        $now = time();
        $ticket = $this;
        $valid = true;

        if ($ticket->start_date > $now or $this->end_date < $now) {
            $valid = false;
        }

        if ($ticket->capacity) {
            $ticketUserCount = TicketUser::where('ticket_id', $ticket->id)->count();

            if ($ticketUserCount and $ticket->capacity <= $ticketUserCount) {
                $valid = false;
            }
        }

        return $valid;
    }

    public function getSubTitle()
    {
        $title = '';

        if (!empty($this->end_date) and !empty($this->capacity)) {
            $title = trans('public.ticket_for_students_until_date', ['students' => $this->capacity, 'date' => dateTimeFormat($this->end_date, 'j F Y')]);
        } elseif (!empty($this->end_date)) {
            $title = trans('public.ticket_until_date', ['date' => dateTimeFormat($this->end_date, 'j F Y')]);
        }

        return $title;
    }

    public function getPriceWithDiscount($price, $activeSpecialOffer = null)
    {
        $percent = $this->discount;

        if (!empty($activeSpecialOffer)) {
            $percent = $percent + $activeSpecialOffer->percent;
        }

        if ($percent > 0) {
            $price = $price - ($price * $percent / 100);
        }

        return $price;
    }
}
