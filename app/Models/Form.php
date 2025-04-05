<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Form extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "forms";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title', 'heading_title', 'description', 'welcome_message_title', 'welcome_message_description', 'tank_you_message_title', 'tank_you_message_description'];


    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getHeadingTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'heading_title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    public function getWelcomeMessageTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'welcome_message_title');
    }

    public function getWelcomeMessageDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'welcome_message_description');
    }

    public function getTankYouMessageTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'tank_you_message_title');
    }

    public function getTankYouMessageDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'tank_you_message_description');
    }


    /********
     * Relations
     * ******/
    public function rolesAndUersAndGroups()
    {
        return $this->hasMany(FormRoleUserGroup::class, 'form_id', 'id');
    }

    public function userGroups()
    {
        return $this->belongsToMany(Group::class, 'form_roles_users_groups', 'form_id', 'group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'form_roles_users_groups', 'form_id', 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'form_roles_users_groups', 'form_id', 'role_id');
    }

    public function fields()
    {
        return $this->hasMany(FormField::class, 'form_id', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class, 'form_id', 'id');
    }

}
