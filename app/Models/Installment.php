<?php

namespace App\Models;

use App\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'installments';
    public $timestamps = false;
    protected $guarded = ['id'];

    static $optionsExplodeKey = "8888";
    // Enums
    static $targetTypes = ['all', 'courses', 'store_products', 'bundles', 'meetings', 'registration_packages', 'subscription_packages'];
    static $courseTargets = ['all_courses', 'live_classes', 'video_courses', 'text_courses', 'specific_categories', 'specific_instructors', 'specific_courses'];
    static $productTargets = ['all_products', 'virtual_products', 'physical_products', 'specific_categories', 'specific_sellers', 'specific_products'];
    static $bundleTargets = ['all_bundles', 'specific_categories', 'specific_instructors', 'specific_bundles'];
    static $meetingTargets = ['all_meetings', 'specific_instructors'];
    static $subscriptionTargets = ['all_packages', 'specific_packages'];
    static $registrationPackagesTargets = ['all_packages', 'specific_packages'];


    public $translatedAttributes = ['title', 'main_title', 'description', 'banner', 'options', 'verification_description', 'verification_banner', 'verification_video'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getMainTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'main_title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    public function getBannerAttribute()
    {
        return getTranslateAttributeValue($this, 'banner');
    }

    public function getOptionsAttribute()
    {
        return getTranslateAttributeValue($this, 'options');
    }

    public function getVerificationDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'verification_description');
    }

    public function getVerificationBannerAttribute()
    {
        return getTranslateAttributeValue($this, 'verification_banner');
    }

    public function getVerificationVideoAttribute()
    {
        return getTranslateAttributeValue($this, 'verification_video');
    }

    public function getUpfrontAttribute()
    {
        return $this->attributes['upfront'] + 0;
    }

    // #############
    // Relations
    // ############

    public function userGroups()
    {
        return $this->hasMany(InstallmentUserGroup::class, 'installment_id', 'id');
    }

    public function specificationItems() // used just in query
    {
        return $this->hasMany(InstallmentSpecificationItem::class, 'installment_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'installment_specification_items', 'installment_id', 'category_id');
    }

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'installment_specification_items', 'installment_id', 'instructor_id');
    }

    public function sellers()
    {
        return $this->belongsToMany(User::class, 'installment_specification_items', 'installment_id', 'seller_id');
    }

    public function webinars()
    {
        return $this->belongsToMany(Webinar::class, 'installment_specification_items', 'installment_id', 'webinar_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'installment_specification_items', 'installment_id', 'product_id');
    }

    public function bundles()
    {
        return $this->belongsToMany(Bundle::class, 'installment_specification_items', 'installment_id', 'bundle_id');
    }

    public function subscribes()
    {
        return $this->belongsToMany(Subscribe::class, 'installment_specification_items', 'installment_id', 'subscribe_id');
    }

    public function registrationPackages()
    {
        return $this->belongsToMany(RegistrationPackage::class, 'installment_specification_items', 'installment_id', 'registration_package_id');
    }

    public function steps()
    {
        return $this->hasMany(InstallmentStep::class, 'installment_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(InstallmentOrder::class, 'installment_id', 'id');
    }


    // #############
    // Helpers
    // ############

    public function reachedCapacityPercent()
    {
        $orders = InstallmentOrder::query()
            ->where('installment_id', $this->id)
            ->whereIn('status', ['open', 'pending_verification'])
            ->count();

        $percent = 0;

        if ($orders > 0 and $this->capacity > 0) {
            $percent = ($orders / $this->capacity) * 100;
        }

        return $percent;
    }

    public function hasCapacity()
    {
        $result = true;

        if (!empty($this->capacity)) {
            $orders = InstallmentOrder::query()
                ->where('installment_id', $this->id)
                ->whereIn('status', ['open', 'pending_verification'])
                ->count();

            if ($orders >= $this->capacity) {
                $result = false;
            }
        }

        return $result;
    }


    public function getUpfront($itemPrice = 1)
    {
        $result = 0;

        if (!empty($this->upfront) and $this->upfront > 0) {
            if ($this->upfront_type == 'percent') {
                $result = ($itemPrice * $this->upfront) / 100;
            } else {
                $result = $this->upfront;
            }
        }

        return $result;
    }

    public function totalPayments($itemPrice = 1, $withUpfront = true)
    {
        $total = 0;

        if (!empty($this->upfront) and $withUpfront) {
            if ($this->upfront_type == 'percent') {
                $total += ($itemPrice * $this->upfront) / 100;
            } else {
                $total += $this->upfront;
            }
        }

        foreach ($this->steps as $step) {
            $total += $step->getPrice($itemPrice);
        }

        return $total;
    }

    public function totalInterest($itemPrice = 1, $totalPayments = null)
    {
        if (empty($totalPayments)) {
            $totalPayments = $this->totalPayments($itemPrice);
        }

        $result = 0;
        $tmp = ($totalPayments - $itemPrice);

        if ($tmp > 0) {
            $tmp2 = ($tmp / $itemPrice) * 100;

            if ($tmp2 > 0) {
                $result = number_format($tmp2, 2);
            }
        }

        return $result;
    }

    public function needToVerify($user = null)
    {
        $result = false;

        if (empty($user)) {
            $user = auth()->user();
        }

        if ($this->verification) {
            $result = true;

            if (!empty($user) and $this->bypass_verification_for_verified_users and $user->installment_approval) {
                $result = false;
            }
        }

        return $result;
    }
}
