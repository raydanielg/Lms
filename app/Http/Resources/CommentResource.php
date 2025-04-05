<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public $panel = false;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'comment_user_type' => $this->comment_user_type,
            'create_at' => $this->created_at,
            'comment' => $this->comment,
            $this->mergeWhen(1, function () {
                return [
                    'can' => [
                        'delete' => (bool)(apiAuth() and apiAuth()->id == $this->user_id),
                        'report' => (bool)apiAuth(),
                        'reply' => (bool)apiAuth(),
                    ]
                ];
            }),

            $this->mergeWhen($this->panel, function () {


            }),

            $this->mergeWhen($this->blog_id, function () {
                return [
                    'blog' => [
                        'id' => $this->blog->id,
                        'title' => $this->blog->title,
                        'url' => url($this->blog->getUrl()),
                    ]
                ];
            }),
            $this->mergeWhen($this->product, function () {
                return [
                    'product' => [
                        'id' => $this->product->id,
                        'title' => $this->product->title,
                    ]
                ];
            }),
            $this->mergeWhen($this->webinar, function () {
                return [
                    'product' => [
                        'id' => $this->webinar->id,
                        'title' => $this->webinar->title,
                    ]
                ];
            }),

            'user' => $this->user->brief ?? null,
            //     'webinar' => $this->when($this->webinar, $this->webinar),
            //    'product' => $this->when($this->product, $this->product),
            //   'replies'=>
            'replies' => $this->replies->where('status', 'active')->map(function ($reply) {
                return [
                    'id' => $reply->id,
                    'comment_user_type' => $reply->comment_user_type,
                    'user' => [
                        'full_name' => $this->user->full_name,
                        'avatar' => url($this->user->getAvatar()),
                    ],
                    'create_at' => $reply->created_at,
                    'comment' => $reply->comment,
                ];
            })
        ];
    }

}
