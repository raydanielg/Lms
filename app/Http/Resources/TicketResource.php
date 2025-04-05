<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!empty($this->webinar_id)) {
            $item = $this->webinar;
        } elseif (!empty($this->bundle_id)) {
            $item = $this->bundle;
        }

        $price = !empty($item) ? $item->price - $item->getDiscount($this) : 0;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'sub_title' => $this->getSubTitle(),
            'discount' => $this->discount,
            'price_with_ticket_discount' => $price ,
            'is_valid' => $this->isValid(),
        ];
    }
}
