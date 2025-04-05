<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "sender_id" => $this->sender_id,
            "group_id" => $this->group_id,
            "webinar_id" => $this->webinar_id,
            "title" => $this->title,
            "message" => $this->message,
            "sender" => $this->sender,
            "type" => $this->type,
            "created_at" => $this->created_at,
        ];
    }
}
