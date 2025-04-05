<?php

namespace App\Models;

use App\Models\Traits\SequenceContent;
use Illuminate\Database\Eloquent\Model;
use Spatie\CalendarLinks\Link;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Session extends Model implements TranslatableContract
{
    use Translatable;
    use SequenceContent;

    public $timestamps = false;
    protected $guarded = ['id'];
    protected $table = 'sessions';
    protected $dateFormat = 'U';

    static $Active = 'active';
    static $Inactive = 'inactive';
    static $Status = ['active', 'inactive'];

    static $sessionApis = ['local', 'big_blue_button', 'zoom', 'agora', 'jitsi'];


    public $translatedAttributes = ['title', 'description'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }


    public function creator()
    {
        return $this->hasOne('App\User', 'user_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function sessionReminds()
    {
        return $this->hasMany('App\Models\SessionRemind', 'session_id', 'id');
    }

    public function learningStatus()
    {
        return $this->hasOne('App\Models\CourseLearning', 'session_id', 'id');
    }

    public function chapter()
    {
        return $this->belongsTo('App\Models\WebinarChapter', 'chapter_id', 'id');
    }

    public function agoraHistory()
    {
        return $this->hasOne('App\Models\AgoraHistory', 'session_id', 'id');
    }

    public function personalNote()
    {
        return $this->morphOne('App\Models\CoursePersonalNote', 'targetable');
    }

    public function addToCalendarLink()
    {
        try {
            $date = \DateTime::createFromFormat('j M Y H:i', dateTimeFormat($this->date, 'j M Y H:i', false));

            $link = Link::create($this->title, $date, $date); //->description('Cookies & cocktails!')

            return $link->google();
        } catch (\Exception $exception) {
            return '';
        }
    }

    public function getJoinLink($zoom_start_link = false)
    {
        $link = $this->link;

        if ($this->session_api == 'big_blue_button') {
            $link = url('panel/sessions/' . $this->id . '/joinToBigBlueButton');
        }

        /*if ($zoom_start_link and auth()->check() and auth()->id() == $this->creator_id and $this->session_api == 'zoom') {
            $link = $this->zoom_start_link;
        }*/

        if ($this->session_api == 'agora') {
            $link = url('panel/sessions/' . $this->id . '/joinToAgora');
        }

        if ($this->session_api == 'jitsi') {
            $link = url('panel/sessions/' . $this->id . '/joinToJitsi');
        }

        return $link;
    }

    public function isFinished(): bool
    {
        $agoraHistory = $this->agoraHistory;

        $finished = (!empty($agoraHistory) and !empty($agoraHistory->end_at));

        if (!$finished) {
            $finished = (time() > (($this->duration * 60) + $this->date));
        }

        return $finished;
    }

    public function checkPassedItem()
    {
        $result = false;

        if (auth()->check()) {
            $check = $this->learningStatus()->where('user_id', auth()->id())->count();

            $result = ($check > 0);
        }

        return $result;
    }

    public function getSessionStreamType()
    {
        $setting = null;

        if (!empty($this->reserve_meeting_id)) {
            $setting = getFeaturesSettings('meeting_live_stream_type');
        } else {
            $setting = getFeaturesSettings('course_live_stream_type');
        }

        $sessionStreamType = 'single';

        if (!empty($setting) and in_array($setting, ['single', 'multiple'])) {
            $sessionStreamType = $setting;
        }

        return $sessionStreamType;
    }
}
