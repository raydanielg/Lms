<?php

namespace App\Models;

use App\Models\Traits\CascadeDeletes;
use App\User;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;
use Jorenvh\Share\ShareFacade;

class Product extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;
    use CascadeDeletes;

    protected $table = 'products';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $morphsFunctions = ['productBadgeContent', 'relatedCourses', 'deleteRequest'];

    static $productTypes = ['virtual', 'physical'];
    static $productStatus = ['active', 'pending', 'draft', 'inactive'];
    static $physical = 'physical';
    static $virtual = 'virtual';
    static $active = 'active';
    static $pending = 'pending';
    static $draft = 'draft';
    static $inactive = 'inactive';


    public $translatedAttributes = ['title', 'seo_description', 'summary', 'description'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getSeoDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'seo_description');
    }

    public function getSummaryAttribute()
    {
        return getTranslateAttributeValue($this, 'summary');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    public function getThumbnailAttribute()
    {
        $media = $this->media()->where('type', ProductMedia::$thumbnail)->first();

        return !empty($media) ? $media->path : null;
    }

    public function getImagesAttribute()
    {
        return $this->media()->where('type', ProductMedia::$image)->get();
    }

    public function getVideoDemoAttribute()
    {
        $media = $this->media()->where('type', ProductMedia::$video)->first();

        return !empty($media) ? $media->path : null;
    }

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

    public function getImage()
    {
        return $this->thumbnail;
    }


    /*
     * Relations
     * */
    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id', 'id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\ProductFile', 'product_id', 'id');
    }

    public function media()
    {
        return $this->hasMany('App\Models\ProductMedia', 'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'category_id', 'id');
    }

    public function selectedFilterOptions()
    {
        return $this->hasMany('App\Models\ProductSelectedFilterOption', 'product_id', 'id');
    }

    public function selectedSpecifications()
    {
        return $this->hasMany('App\Models\ProductSelectedSpecification', 'product_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\ProductFaq', 'product_id', 'id');
    }

    public function discounts()
    {
        return $this->hasMany('App\Models\ProductDiscount', 'product_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'product_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\ProductReview', 'product_id', 'id');
    }

    public function productOrders()
    {
        return $this->hasMany('App\Models\ProductOrder', 'product_id', 'id');
    }

    public function productBadgeContent()
    {
        return $this->morphMany(ProductBadgeContent::class, 'targetable');
    }

    public function relatedCourses()
    {
        return $this->morphMany('App\Models\RelatedCourse', 'targetable');
    }

    public function deleteRequest()
    {
        return $this->morphOne(ContentDeleteRequest::class, 'targetable');
    }



    public function sales($withoutRefunds = true)
    {
        if (empty($this->salesCache)) {
            $ordersIds = $this->productOrders->pluck('id')->toArray();

            $query = Sale::whereIn('product_order_id', $ordersIds);

            if ($withoutRefunds) {
                $query->whereNull('refund_at');
            }

            $query->orderBy('created_at', 'desc');

            $this->salesCache = $query->get();
        }

        return $this->salesCache;
    }

    public function salesCount($inventoryUpdatedAt = false)
    {
        if (empty($this->salesCountCache)) {
            $query = $this->productOrders()
                ->whereNotNull('sale_id')
                ->whereHas('sale', function ($query) {
                    $query->whereNull('refund_at');
                })
                ->whereNotIn('status', [ProductOrder::$canceled, ProductOrder::$pending]);

            if ($inventoryUpdatedAt and !empty($this->inventory_updated_at)) {
                $query->where('created_at', '>=', $this->inventory_updated_at);
            }

            $this->salesCountCache = $query->sum('quantity');
        }

        return $this->salesCountCache;
    }

    /*
     * .\  Relations
     * */

    public function isVirtual(): bool
    {
        return ($this->type == self::$virtual);
    }

    public function isPhysical(): bool
    {
        return ($this->type == self::$physical);
    }

    public function getActiveDiscount()
    {
        $activeDiscount = ProductDiscount::where('product_id', $this->id)
            ->where('status', 'active')
            ->where('start_date', '<', time())
            ->where('end_date', '>', time())
            ->first();

        if (!empty($activeDiscount) and !empty($activeDiscount->count)) {
            $usedCount = ProductOrder::where('product_id', $this->id)
                ->where('discount_id', $activeDiscount->id)
                ->whereHas('sale', function ($query) {
                    $query->whereNull('refund_at');
                })
                ->count();

            if ($usedCount >= $activeDiscount->count) {
                return false;
            }
        }

        return $activeDiscount ?? false;
    }

    public function getPriceWithActiveDiscountPrice()
    {
        $activeDiscount = $this->getActiveDiscount();

        $price = $this->price ?? 0;

        if (!empty($activeDiscount)) {
            $price = $price - ($price * $activeDiscount->percent / 100);
        }

        return $price;
    }

    public function getDiscountPrice()
    {
        $price = 0;

        $activeDiscount = $this->getActiveDiscount();

        if (!empty($activeDiscount)) {
            $price = $this->price * $activeDiscount->percent / 100;
        }

        return $price;
    }

    public function getPrice()
    {
        // this method used in installment

        return $this->getPriceWithActiveDiscountPrice();
    }

    public function getAvailability()
    {
        if (empty($this->availabilityCount)) {
            if ($this->unlimited_inventory) {
                $this->availabilityCount = 99999;
            } else {
                $availabilityCount = $this->inventory - $this->salesCount(true);

                if ($availabilityCount < 0) {
                    $availabilityCount = 0;
                }

                $this->availabilityCount = $availabilityCount;
            }
        }

        return $this->availabilityCount;
    }

    public function getUrl()
    {
        return url('/products/' . $this->slug);
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

    public function getRate()
    {
        $rate = 0;

        $reviews = $this->reviews()
            ->where('status', 'active')
            ->get();

        if (!empty($reviews) and $reviews->count() > 0) {
            $rate = number_format($reviews->avg('rates'), 2);
        }

        if ($rate > 5) {
            $rate = 5;
        }

        return $rate > 0 ? number_format($rate, 2) : 0;
    }

    public function getCommission()
    {
        $commission = 0;

        if (!empty($this->commission)) {
            $commission = $this->commission;
        } else {
            $getStoreSettings = getStoreSettings();

            if ($this->type == self::$virtual and !empty($getStoreSettings) and !empty($getStoreSettings['virtual_product_commission'])) {
                $commission = $getStoreSettings['virtual_product_commission'];
            } elseif ($this->type == self::$physical and !empty($getStoreSettings) and !empty($getStoreSettings['physical_product_commission'])) {
                $commission = $getStoreSettings['physical_product_commission'];
            } else {
                $financialSettings = getFinancialSettings();

                if (!empty($financialSettings['commission'])) {
                    $commission = $financialSettings['commission'];
                }
            }
        }

        return $commission;
    }

    public function getTax()
    {
        $tax = 0;

        if (!empty($this->tax)) {
            $tax = $this->tax;
        } else {
            $getStoreSettings = getStoreSettings();

            if (!empty($getStoreSettings) and isset($getStoreSettings['store_tax'])) {
                $tax = $getStoreSettings['store_tax'];
            } else {
                $financialSettings = getFinancialSettings();

                if (!empty($financialSettings['tax'])) {
                    $tax = $financialSettings['tax'];
                }
            }
        }

        return $tax;
    }

    public function checkUserHasBought($user = null): bool
    {
        $hasBought = false;

        if (empty($user)) {
            $user = auth()->user();
        }

        if (!empty($user)) {
            $giftsIds = Gift::query()->where('email', $user->email)
                ->where('status', 'active')
                ->whereNotNull('product_id')
                ->where(function ($query) {
                    $query->whereNull('date');
                    $query->orWhere('date', '<', time());
                })
                ->whereHas('sale')
                ->pluck('id')
                ->toArray();

            $order = ProductOrder::query()->where('product_id', $this->id)
                ->where(function ($query) use ($user, $giftsIds) {
                    $query->where('buyer_id', $user->id);
                    $query->orWhereIn('gift_id', $giftsIds);
                })
                ->whereHas('sale', function ($query) use ($user) {
                    $query->whereIn('type', ['product', 'gift'])
                        ->where('access_to_purchased_item', true)
                        ->whereNull('refund_at');
                })->first();

            $hasBought = !empty($order);
        }

        return $hasBought;
    }
}
