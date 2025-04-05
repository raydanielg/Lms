<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Subscribe extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'subscribes';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $translatedAttributes = ['title', 'description'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale', 'subscribe_id', 'id');
    }

    public function uses()
    {
        return $this->hasMany('App\Models\SubscribeUse', 'subscribe_id', 'id');
    }

    public static function getActiveSubscribe($userId)
    {
        $activePlan = null;
        $subscribe = null;
        $saleCreatedAt = null;

        $lastSubscribeSale = Sale::where('buyer_id', $userId)
            ->where('type', Sale::$subscribe)
            ->whereNull('refund_at')
            ->latest('created_at')
            ->first();

        if ($lastSubscribeSale) {
            $subscribe = $lastSubscribeSale->subscribe;
            $saleCreatedAt = $lastSubscribeSale->created_at;
        }

        /* check installment */
        if (empty($subscribe)) {
            $installmentOrder = InstallmentOrder::query()->where('user_id', $userId)
                ->whereNotNull('subscribe_id')
                ->where('status', 'open')
                ->whereNull('refund_at')
                ->latest('created_at')
                ->first();

            if (!empty($installmentOrder)) {
                $subscribe = $installmentOrder->subscribe;
                $subscribe->installment_order_id = $installmentOrder->id;
                $saleCreatedAt = $installmentOrder->created_at;

                if ($installmentOrder->checkOrderHasOverdue()) {
                    $overdueIntervalDays = getInstallmentsSettings('overdue_interval_days');

                    if (empty($overdueIntervalDays) or $installmentOrder->overdueDaysPast() > $overdueIntervalDays) {
                        $subscribe = null;
                    }
                }
            }
        }

        if (!empty($subscribe) and !empty($saleCreatedAt)) {
            $useCount = SubscribeUse::where('user_id', $userId)
                ->where('subscribe_id', $subscribe->id)
                ->whereHas('sale', function ($query) use ($saleCreatedAt) {
                    $query->where('created_at', '>', $saleCreatedAt);
                    $query->whereNull('refund_at');
                })
                ->count();

            $subscribe->used_count = $useCount;

            $countDayOfSale = (int)diffTimestampDay(time(), $saleCreatedAt);

            if (
                ($subscribe->usable_count > $useCount or $subscribe->infinite_use)
                and
                $subscribe->days >= $countDayOfSale
            ) {
                $activePlan = $subscribe;
            }
        }

        return $activePlan;
    }

    public static function getDayOfUse($userId)
    {
        $lastSubscribeSale = Sale::where('buyer_id', $userId)
            ->where('type', Sale::$subscribe)
            ->whereNull('refund_at')
            ->latest('created_at')
            ->first();

        return $lastSubscribeSale ? (int)diffTimestampDay(time(), $lastSubscribeSale->created_at) : 0;
    }

    public function activeSpecialOffer()
    {
        $activeSpecialOffer = SpecialOffer::where('subscribe_id', $this->id)
            ->where('status', SpecialOffer::$active)
            ->where('from_date', '<', time())
            ->where('to_date', '>', time())
            ->first();

        return $activeSpecialOffer ?? false;
    }

    public function getPrice()
    {
        $price = $this->price;

        $specialOffer = $this->activeSpecialOffer();
        if (!empty($specialOffer)) {
            $price = $price - ($price * $specialOffer->percent / 100);
        }

        return $price;
    }
}
