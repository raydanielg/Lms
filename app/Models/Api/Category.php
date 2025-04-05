<?php

namespace App\Models\Api;

use App\Models\Category as Model;

class Category extends Model
{
    //

    public function getDetailsAttribute()
    {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'color' => TrendCategory::where('category_id', $this->id)->first()->color ?? null,
            'icon' =>($this->icon)? url($this->icon):null,
            'sub_categories' => $this->subCategories->map(function ($sub_category) use (&$all_webinar_count) {
                $all_webinar_count += $sub_category->webinars->count();
                return [
                    'id' => $sub_category->id,
                    'title' => $sub_category->title,
                    'icon' => ($sub_category->icon) ? url($sub_category->icon) : null,
                    'webinars_count' => $sub_category->webinars->count(),
                ];
            }),
            'webinars_count' => $all_webinar_count ?? $this->webinars->count()

        ];
    }
}
