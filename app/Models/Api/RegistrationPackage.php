<?php

namespace App\Models\Api;

use App\Models\RegistrationPackage as Model;

class RegistrationPackage extends Model
{

    public function getDetailsAttribute()
    {
        return [
            'id' => $this->id,
            'days' => $this->days??'unlimited',
            'price' => $this->price,
            'icon'=>($this->icon)?url($this->icon):null ,
            'role' => $this->role,
            'instructors_count' => $this->instructors_count??'unlimited',
            'students_count' => $this->students_count??"unlimited",
            'courses_capacity' => $this->courses_capacity??"unlimited",
            'courses_count' => $this->courses_count??'unlimited',
            'meeting_count' => $this->meeting_count??'unlimited',
            'status' => $this->status,
            'created_at' => $this->created_at,
            'title' => $this->title,
            'description' => $this->description,

        ];
    }
}
