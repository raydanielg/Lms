<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class PurchaseNotification extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "purchase_notifications";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title', 'popup_title', 'popup_subtitle'];


    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getPopupTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'popup_title');
    }

    public function getPopupSubtitleAttribute()
    {
        return getTranslateAttributeValue($this, 'popup_subtitle');
    }

    public function getUsersAttribute()
    {
        return getTranslateAttributeValue($this, 'users');
    }

    public function getTimesAttribute()
    {
        return getTranslateAttributeValue($this, 'times');
    }


    /********
     * Relations
     * ******/

    public function allRelatives()
    {
        return $this->hasMany(PurchaseNotificationRoleGroupContent::class, 'purchase_notification_id', 'id');
    }

    public function userGroups()
    {
        return $this->belongsToMany(Group::class, 'purchase_notification_roles_groups_contents', 'purchase_notification_id', 'group_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'purchase_notification_roles_groups_contents', 'purchase_notification_id', 'role_id');
    }

    public function webinars()
    {
        return $this->belongsToMany(Webinar::class, 'purchase_notification_roles_groups_contents', 'purchase_notification_id', 'webinar_id');
    }

    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'purchase_notification_roles_groups_contents', 'purchase_notification_id', 'bundle_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'purchase_notification_roles_groups_contents', 'purchase_notification_id', 'product_id');
    }


}
