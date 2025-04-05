<?php

namespace App\Models\Api;

use App\Models\Api\Traits\CheckForSaleTrait;
use App\Models\Favorite;
use App\Models\Bundle as Model;

class Bundle extends Model
{
    use CheckForSaleTrait;

    public function getIsFavoriteAttribute()
    {
        if (!apiAuth()) {
            return null;
        }
        return (bool)Favorite::where('bundle_id', $this->id)
            ->where('user_id', apiAuth()->id)
            ->first();
    }

    public function badges()
    {
        return $this->hasMany('App\Models\Api\ProductBadgeContent', 'targetable_id', 'id');
    }
    public function bundleWebinars()
    {
        return $this->hasMany('App\Models\Api\BundleWebinar', 'bundle_id', 'id');
    }

    public function webinars()
    {
        //  return $this->hasManyThrough('App\Models\Webinar', 'App\Models\BundleWebinar', 'bundle_id', 'id');
    }
    public function teacher()
    {
        return $this->belongsTo('App\Models\Api\User', 'teacher_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Api\Ticket', 'bundle_id', 'id');
    }

}
