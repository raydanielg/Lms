<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class ForumTranslation extends Model
{
    protected $table = 'forum_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
