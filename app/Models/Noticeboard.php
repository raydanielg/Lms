<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticeboard extends Model
{
    protected $table = 'noticeboards';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    static $types = ['students', 'instructors', 'students_and_instructors'];
    static $adminTypes = ['organizations', 'students', 'instructors', 'students_and_instructors'];
    static $migrateTypes = ['all', 'organizations', 'students', 'instructors', 'students_and_instructors'];

    public function noticeboardStatus()
    {
        return $this->hasOne('App\Models\NoticeboardStatus', 'noticeboard_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }
}
