<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'user' => [
                'full_name' => $this->creator->full_name,
                'avatar' => url($this->creator->getAvatar()),
            ],
            'can' => [
                'delete' => (bool)(apiAuth() and apiAuth()->id == $this->creator_id),
               // 'report' => (bool)apiAuth(),
                'reply' => (bool)apiAuth(),
            ],
            'rate' => $this->rates,
            'rate_type' => [
                'content_quality' => $this->content_quality,
                'instructor_skills' => $this->instructor_skills,
                'purchase_worth' => $this->purchase_worth,
                'support_quality' => $this->support_quality,
            ],
            'created_at' => $this->created_at,
            'comments' => CommentResource::collection($this->comments) ,
            'replies' => $this->comments->where('status', 'active')->map(function ($reply) {
                return [
                    'id' => $this->id,
                    'user' => [
                        'full_name' => $reply->user->full_name,
                        'avatar' => url($reply->user->getAvatar()),
                    ],
                    'created_at' => $reply->created_at,
                    'comment' => $reply->comment,
                ];

            })

        ];
    }
}
