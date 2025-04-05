<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class AbandonedCartRule extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = "abandoned_cart_rules";
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title'];


    // Enums
    static $targetTypes = ['all', 'courses', 'store_products', 'bundles', 'meetings'];
    static $courseTargets = ['all_courses', 'live_classes', 'video_courses', 'text_courses', 'specific_categories', 'specific_instructors', 'specific_courses'];
    static $productTargets = ['all_products', 'virtual_products', 'physical_products', 'specific_categories', 'specific_sellers', 'specific_products'];
    static $bundleTargets = ['all_bundles', 'specific_categories', 'specific_instructors', 'specific_bundles'];
    static $meetingTargets = ['all_meetings', 'specific_instructors'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    // #############
    // Relations
    // ############

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }

    public function usersAndGroups()
    {
        return $this->hasMany(AbandonedCartRuleUserGroup::class, 'abandoned_cart_rule_id', 'id');
    }

    public function userGroups()
    {
        return $this->belongsToMany(Group::class, 'abandoned_cart_rule_users_groups', 'abandoned_cart_rule_id', 'group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'abandoned_cart_rule_users_groups', 'abandoned_cart_rule_id', 'user_id');
    }

    public function specificationItems() // used just in query
    {
        return $this->hasMany(AbandonedCartRuleSpecificationItem::class, 'abandoned_cart_rule_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'abandoned_cart_rule_specification_items', 'abandoned_cart_rule_id', 'category_id');
    }

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'abandoned_cart_rule_specification_items', 'abandoned_cart_rule_id', 'instructor_id');
    }

    public function sellers()
    {
        return $this->belongsToMany(User::class, 'abandoned_cart_rule_specification_items', 'abandoned_cart_rule_id', 'seller_id');
    }

    public function webinars()
    {
        return $this->belongsToMany(Webinar::class, 'abandoned_cart_rule_specification_items', 'abandoned_cart_rule_id', 'webinar_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'abandoned_cart_rule_specification_items', 'abandoned_cart_rule_id', 'product_id');
    }

    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'abandoned_cart_rule_specification_items', 'abandoned_cart_rule_id', 'bundle_id');
    }

}
