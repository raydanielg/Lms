<?php

namespace App\Models;

use App\Models\Traits\CascadeDeletes;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Jorenvh\Share\ShareFacade;
use Spatie\CalendarLinks\Link;

class UpcomingCourse extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;
    use CascadeDeletes;

    protected $table = "upcoming_courses";
    public $timestamps = false;
    protected $guarded = ['id'];

    public $morphsFunctions = ['productBadgeContent', 'relatedCourses'];

    static $active = 'active';
    static $pending = 'pending';
    static $isDraft = 'is_draft';
    static $inactive = 'inactive';

    static $webinar = 'webinar';
    static $course = 'course';
    static $textLesson = 'text_lesson';

    public $translatedAttributes = ['title', 'description', 'seo_description'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    public function getSeoDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'seo_description');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function teacher()
    {
        return $this->belongsTo('App\User', 'teacher_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Webinar', 'webinar_id', 'id');
    }

    public function filterOptions()
    {
        return $this->hasMany('App\Models\UpcomingCourseFilterOption', 'upcoming_course_id', 'id');
    }

    public function followers()
    {
        return $this->hasMany('App\Models\UpcomingCourseFollower', 'upcoming_course_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'upcoming_course_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany('App\Models\Tag', 'upcoming_course_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\Faq', 'upcoming_course_id', 'id');
    }

    public function favorite()
    {
        return $this->hasMany('App\Models\Favorite', 'upcoming_course_id', 'id');
    }

    public function extraDescriptions()
    {
        return $this->hasMany('App\Models\WebinarExtraDescription', 'upcoming_course_id', 'id');
    }

    public function relatedCourses()
    {
        return $this->morphMany('App\Models\RelatedCourse', 'targetable');
    }

    public function productBadgeContent()
    {
        return $this->morphMany(ProductBadgeContent::class, 'targetable');
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public static function makeSlug($title)
    {
        return SlugService::createSlug(self::class, 'slug', $title);
    }

    public function canAccess($user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!empty($user)) {
            return ($this->creator_id == $user->id or $this->teacher_id == $user->id);
        }

        return false;
    }

    public function getImageCover()
    {
        return $this->image_cover;
    }

    public function getImage()
    {
        return $this->thumbnail;
    }

    public function getUrl()
    {
        return url('/upcoming_courses/' . $this->slug);
    }

    public function addToCalendarLink()
    {

        $date = \DateTime::createFromFormat('j M Y H:i', dateTimeFormat($this->publish_date, 'j M Y H:i', false));

        $link = Link::create($this->title, $date, $date); //->description('Cookies & cocktails!')

        return $link->google();
    }

    public function getShareLink($social)
    {
        $link = ShareFacade::page($this->getUrl(), $this->title)
            ->facebook()
            ->twitter()
            ->whatsapp()
            ->telegram()
            ->getRawLinks();

        return !empty($link[$social]) ? $link[$social] : '';
    }
}
