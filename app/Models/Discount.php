<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];
    static $discountUserTypes = ['all_users', 'special_users'];

    static $discountSource = ['all', 'course', 'bundle', 'category', 'meeting', 'product'];
    static $panelDiscountSource = ['all', 'course', 'bundle', 'meeting', 'product'];
    static $discountSourceAll = 'all';
    static $discountSourceCourse = 'course';
    static $discountSourceCategory = 'category';
    static $discountSourceMeeting = 'meeting';
    static $discountSourceProduct = 'product';
    static $discountSourceBundle = 'bundle';

    static $discountTypes = ['percentage', 'fixed_amount'];
    static $discountTypePercentage = 'percentage';
    static $discountTypeFixedAmount = 'fixed_amount';

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function discountUsers()
    {
        return $this->hasOne('App\Models\DiscountUser', 'discount_id', 'id');
    }

    public function discountCourses()
    {
        return $this->hasMany('App\Models\DiscountCourse', 'discount_id', 'id');
    }

    public function discountBundles()
    {
        return $this->hasMany('App\Models\DiscountBundle', 'discount_id', 'id');
    }

    public function discountCategories()
    {
        return $this->hasMany('App\Models\DiscountCategory', 'discount_id', 'id');
    }

    public function discountGroups()
    {
        return $this->hasMany('App\Models\DiscountGroup', 'discount_id', 'id');
    }

    public function discountRemain()
    {
        $count = $this->count;

        $orderItems = OrderItem::where('discount_id', $this->id)
            ->groupBy('order_id')
            ->get();

        foreach ($orderItems as $orderItem) {
            if (!empty($orderItem) and !empty($orderItem->order) and $orderItem->order->status == 'paid') {
                $count = $count - 1;
            }
        }

        return ($count > 0) ? $count : 0;
    }

    public function salesStats()
    {
        $count = 0;
        $amount = 0;

        $orderItems = OrderItem::where('discount_id', $this->id)
            ->groupBy('order_id')
            ->get();

        foreach ($orderItems as $orderItem) {
            if (!empty($orderItem) and !empty($orderItem->order) and $orderItem->order->status == 'paid') {
                $count = $count + 1;
                $amount = $amount + $orderItem->total_amount;
            }
        }

        return [
            'count' => $count,
            'amount' => $amount,
        ];
    }

    public function checkValidDiscount()
    {
        if ($this->expired_at < time()) {
            return trans('update.discount_code_has_expired'); // expired
        }

        $user = auth()->user();
        $carts = Cart::where('creator_id', $user->id)->get();


        if (($this->source == self::$discountSourceCourse or $this->source == self::$discountSourceCategory)) {
            $webinarCount = array_filter($carts->pluck('webinar_id')->toArray());

            if (empty($webinarCount) or count($webinarCount) < 1) {
                return trans('update.discount_code_is_for_courses_error');
            }
        } elseif ($this->source == self::$discountSourceBundle) {
            $bundleCount = array_filter($carts->pluck('bundle_id')->toArray());

            if (empty($bundleCount) or count($bundleCount) < 1) {
                return trans('update.discount_code_is_for_bundles_error');
            }
        } elseif ($this->source == self::$discountSourceProduct) {
            $productCount = array_filter($carts->pluck('product_order_id')->toArray());

            if (empty($productCount) or count($productCount) < 1) {
                return trans('update.discount_code_is_for_products_error');
            }
        } elseif ($this->source == self::$discountSourceMeeting) {
            $meetingCount = array_filter($carts->pluck('reserve_meeting_id')->toArray());

            if (empty($meetingCount) or count($meetingCount) < 1) {
                return trans('update.discount_code_is_for_meetings_error');
            }
        }

        if ($this->source == self::$discountSourceCourse and count($this->discountCourses)) {
            $discountWebinarsIds = $this->discountCourses()->pluck('course_id')->toArray();
            $hasSpecialWebinars = false;

            foreach ($carts as $cart) {
                $webinar = $cart->webinar;
                if (!empty($webinar) and in_array($webinar->id, $discountWebinarsIds)) {
                    $hasSpecialWebinars = true;
                }
            }

            if (!$hasSpecialWebinars) {
                return trans('update.your_coupon_is_valid_for_another_course');
            }
        }

        if ($this->source == self::$discountSourceBundle and count($this->discountBundles)) {
            $discountBundlesIds = $this->discountBundles()->pluck('bundle_id')->toArray();
            $hasSpecialBundles = false;

            foreach ($carts as $cart) {
                $bundle = $cart->bundle;
                if (!empty($bundle) and in_array($bundle->id, $discountBundlesIds)) {
                    $hasSpecialBundles = true;
                }
            }

            if (!$hasSpecialBundles) {
                return trans('update.your_coupon_is_valid_for_another_bundle');
            }
        }

        if ($this->source == self::$discountSourceProduct) {
            $hasSpecialProducts = false;

            foreach ($carts as $cart) {
                if (!empty($cart->productOrder)) {
                    $product = $cart->productOrder->product;

                    if (!empty($product) and ($this->product_type == 'all' or $this->product_type == $product->type)) {
                        $hasSpecialProducts = true;
                    }
                }
            }

            if (!$hasSpecialProducts) {
                return trans('update.your_coupon_is_valid_for_another_products_type');
            }
        }

        if ($this->source == self::$discountSourceCategory) {
            $categoriesIds = ($this->discountCategories) ? $this->discountCategories()->pluck('category_id')->toArray() : [];
            $hasSpecialCategories = false;

            foreach ($carts as $cart) {
                $webinar = $cart->webinar;
                if (!empty($webinar) and in_array($webinar->category_id, $categoriesIds)) {
                    $hasSpecialCategories = true;
                }
            }

            if (!$hasSpecialCategories) {
                return trans('update.your_coupon_is_valid_for_another_category');
            }
        }

        if ($this->type == 'special_users') {
            $userDiscount = DiscountUser::where('user_id', $user->id)
                ->where('discount_id', $this->id)
                ->first();

            if (empty($userDiscount)) {
                return trans('cart.coupon_invalid'); // not for this user
            }
        }


        if (!empty($this->minimum_order)) { // check user orders minimum amounts
            $totalCartsPrice = Cart::getCartsTotalPrice($carts);

            if ($this->minimum_order > $totalCartsPrice) {
                return trans('update.discount_code_minimum_order_error', ['min_order' => $this->minimum_order]); // the minimum order is less than the discount amount
            }
        }

        if (!empty($this->discountGroups) and count($this->discountGroups)) {
            $groupsIds = $this->discountGroups()->pluck('group_id')->toArray();

            if (empty($user->userGroup) or !in_array($user->userGroup->group_id, $groupsIds)) {
                return trans('update.discount_code_group_error'); // this user is not in specific group
            }
        }

        if ($this->for_first_purchase) {
            $checkIsFirstPurchase = Sale::where('buyer_id', $user->id)
                ->whereNull('refund_at')
                ->count();

            if ($checkIsFirstPurchase > 0) {
                return trans('update.discount_code_for_first_purchase_error'); // This discount code for first purchase.
            }
        }

        $usedCount = 0;
        $orderItems = OrderItem::where('discount_id', $this->id)
            ->groupBy('order_id')
            ->get();

        foreach ($orderItems as $orderItem) {
            if (!empty($orderItem) and !empty($orderItem->order) and $orderItem->order->status == 'paid') {
                $usedCount += 1;
            }
        }

        if ($usedCount >= $this->count) {
            return trans('update.discount_code_used_count_error'); // The number of uses of this code has expired.
        }

        return 'ok';
    }
}
