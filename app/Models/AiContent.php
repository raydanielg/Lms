<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class AiContent extends Model
{
    protected $table = "ai_contents";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];


    /********
     * Relations
     * ******/

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function template()
    {
        return $this->belongsTo(AiContentTemplate::class, 'service_id', 'id');
    }


}
