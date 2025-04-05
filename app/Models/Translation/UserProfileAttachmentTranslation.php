<?php

namespace App\Models\Translation;


use Illuminate\Database\Eloquent\Model;

class UserProfileAttachmentTranslation extends Model
{

    protected $table = 'user_profile_attachment_translations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];
}
