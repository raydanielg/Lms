<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebinarForumResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'avatar' => url($this->user->getAvatar()),
                'full_name' => $this->user->full_name,
            ],
            'pin' => (bool)$this->pin,
            'description' => $this->description,
            'answers_count' => $this->answers_count,
            'resolved' => (bool)$this->answers->where('resolved', true)->first(),
            'attachment' => $this->attach ? url($this->attach) : null,
            'can' => [
                'pin' => apiAuth()->can('pin', $this->resource),
                'update' => apiAuth()->can('update', $this->resource),
            ],
            'created_at' => $this->created_at,
          //  'active_users_count'=>
            $this->mergeWhen($this->answers->count(), function () {
                $last_answer = $this->answers->last();
                $active_users = $this->answers->unique('user_id')->take(3);
                return [
                    'active_users' => $active_users->map(function ($item) {
                        return url($item->user->getAvatar());
                    }),
                    'more' => $this->answers_count - $active_users->count(),
                    'last_activity' => $last_answer->created_at,
                    'last_answer' => [
                        'description' => $last_answer->description,
                        'user' => [
                            'full_name' => $last_answer->user->full_name,
                            'avatar' => url($last_answer->user->getAvatar())
                        ]
                    ]

                ];
            }),

            //   'more'=>


        ];
    }
}
