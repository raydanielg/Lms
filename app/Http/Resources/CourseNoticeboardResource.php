<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseNoticeboardResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'color' => $this->color,
            'created_at' => $this->created_at,
            'icon' => $this->getIcon(),
            /*  'seen' => (bool)$this->resource->whereHas('noticeboardStatus', function ($query) {
                  $query->where('user_id', apiAuth()->id);
              }),*/
            'creator' => [
                'id' => $this->creator->id,
                'full_name' => $this->creator->full_name,
                'avatar' => $this->creator->getAvatar() ? url($this->creator->getAvatar()) : null,
            ]


        ];
    }
}
