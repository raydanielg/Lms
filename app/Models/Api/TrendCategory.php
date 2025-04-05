<?php

namespace App\Models\Api;

use App\Models\TrendCategory as Model;

class TrendCategory extends Model
{
    //

    public function getDetailsAttribute(){

        return [
            'id' => $this->category->id,
            'title' => $this->category->title,
            'color' =>$this->color,
            'icon' =>($this->icon)? url($this->icon):null,
            'sub_categories' => $this->category->subCategories->map(function ($sub_category) use (&$all_webinar_count) {
                $all_webinar_count += $sub_category->webinars->count();
                return [
                    'id' => $sub_category->id,
                    'title' => $sub_category->title,
                    'icon' =>($sub_category->icon)? url($sub_category->icon):null,
                    'webinars_count' => $sub_category->webinars->count(),
                ];
            }),
            'webinars_count' => $all_webinar_count ?? $this->category->webinars->count()

        ];
    }
}
