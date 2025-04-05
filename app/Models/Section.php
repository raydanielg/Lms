<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function children() {
        return $this->hasMany($this, 'section_group_id', 'id');
    }
}
