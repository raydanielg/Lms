<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public $show = false;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category_title' => $this->category->title ?? null,
            'status' => $this->status,
            'author' => [
                'full_name' => $this->author->full_name
            ],
            'url' => url($this->getUrl()),
            'comments_count' => $this->comments->count(),
            'visit_count' => $this->visit_count,
            'created_at' => $this->created_at,
            'locale' => $this->locale,
            'image' => ($this->image) ? url($this->image) : null,
            'description' => truncate($this->description, 160),
            $this->mergeWhen($this->show, [
                'content' => $this->content
            ]),

        ];
    }
}
