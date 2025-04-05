<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class TestimonialTranslation extends Model
{
    protected $table = 'testimonial_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
