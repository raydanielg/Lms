<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseForumAnswerResource extends JsonResource
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
            'description' => $this->description,
            'pin' => (bool)$this->pin,
            'resolved' => (bool)$this->resolved,
            'user' => [
                'id' => $this->user->id,
                'full_name' => $this->user->full_name,
                'avatar' => url($this->user->getAvatar()),
                'role_name' => $this->user->role_name
            ],
            'created_at' => $this->created_at,
            'can' => [
                'pin' => apiAuth()->can('pin', $this->resource),
                'resolve' => apiAuth()->can('resolve', $this->resource),
                'update' => apiAuth()->can('update', $this->resource),
            ] ,

        ];
    }
}
