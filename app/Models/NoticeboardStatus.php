<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeboardStatus extends Model
{
    protected $table = 'noticeboards_status';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];//
}
