<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebinarAssignmentHistoryMessageResource extends JsonResource
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
            'sender' => ($this->sender->id == apiAuth()->id) ?
                ['id' => $this->sender->id,
                    'full_name' => $this->sender->full_name,
                    'avatar' => $this->sender->getAvatar() ? url($this->sender->getAvatar()) : null,
                ] : null,

            'supporter' => ($this->sender->id != apiAuth()->id) ?
                ['id' => $this->sender->id,
                    'full_name' => $this->sender->full_name,
                    'avatar' => $this->sender->getAvatar() ? url($this->sender->getAvatar()) : null,
                ] : null,

            'message' => $this->message,
            'file_title' => $this->file_title,
            'file_path' => $this->file_path ? url($this->file_path) : null,
            'created_at' => $this->created_at,
        ];
    }
}
