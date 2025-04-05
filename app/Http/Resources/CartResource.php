<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {


        $type = null;
        if ($this->webinar_id) {
            $type = 'webinar';
        } elseif ($this->bundle_id) {
            $type = 'bundle';
        } elseif ($this->reserve_meeting_id) {
            $type = 'meeting';
        }
        $info = $this->getItemInfo();
        return [
            'id' => $this->id,
            'type' => $type,
            'image' => url($info['imgPath']) ?? null,
            'title' => $info['title'] ?? null,
            'teacher_name' => $info['teacherName'] ?? null,
            'rate' => $info['rate'] ?? null,
            'price' => isset($info['price']) ? convertPriceToUserCurrency($info['price']) : null,
            'discountPrice' => isset($info['discountPrice']) ? convertPriceToUserCurrency($info['discountPrice']) : null,
            'quantity' => $info['quantity'] ?? null,
            $this->mergeWhen($this->reserve_meeting_id, function ()  {
                $time_exploded = explode('-', $this->reserveMeeting->meetingTime->time);
                return [
                    'day' => $this->reserveMeeting->day,
                    //  'time' => $this->reserveMeeting->meetingTime->time,
                    'time' => [
                        'start' => $time_exploded[0],
                        'end' => $time_exploded[1],
                    ],
                    'time_user' => [
                        'start' => dateTimeFormat(strtotime($this->reserveMeeting->start_at),'h:iA',false),
                        'end' => dateTimeFormat(strtotime($this->reserveMeeting->end_at),'h:iA',false),
                    ],
                    'timezone' => $this->reserveMeeting->meeting->getTimezone(),
                    'timezone_user' => getTimezone(),
                ];
            }),

        ];
    }
}
