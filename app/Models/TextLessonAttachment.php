<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextLessonAttachment extends Model
{
    protected $table = 'text_lessons_attachments';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function file()
    {
        return $this->belongsTo('App\Models\File', 'file_id', 'id');
    }
}
