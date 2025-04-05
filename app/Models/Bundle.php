<?php

namespace App\Models;

use App\Mixins\Certificate\MakeCertificate;
use App\Models\Traits\CascadeDeletes;
use App\User;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\DB;
use Jorenvh\Share\ShareFacade;

class Bundle extends Model implements TranslatableContract
{
    use Translatable;
    use Sluggable;
    use CascadeDeletes;

    protected $table = 'bundles';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public $morphsFunctions = ['productBadgeContent', 'relatedCourses', 'deleteRequest'];

    static $active = 'active';
    static $pending = 'pending';
    static $isDraft = 'is_draft';
    static $inactive = 'inactive';

    static $statuses = [
        'active', 'pending', 'is_draft', 'inactive'
    ];

    static $videoDemoSource = ['upload', 'youtube', 'vimeo', 'external_link'];

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

    public function getDurationAttribute()
    {
        return $this->getBundleDuration();
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

    public function filterOptions()
    {
        return $this->hasMany('App\Models\BundleFilterOption', 'bundle_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany('App\Models\Tag', 'bundle_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket', 'bundle_id', 'id');
    }

    public function bundleWebinars()
    {
        return $this->hasMany('App\Models\BundleWebinar', 'bundle_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\Faq', 'bundle_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'bundle_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\WebinarReview', 'bundle_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale', 'bundle_id', 'id')
            ->whereNull('refund_at')
            ->where('type', 'bundle');
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

    public function getUrl()
    {
        return url('/bundles/' . $this->slug);
    }

    public function getImageCover()
    {
        return config('app_url') . $this->image_cover;
    }

    public function getImage()
    {
        return config('app_url') . $this->thumbnail;
    }

    public function getRate()
    {
        $rate = 0;

        if (!empty($this->avg_rates)) {
            $rate = $this->avg_rates;
        } else {
            $reviews = $this->reviews()
                ->where('status', 'active')
                ->get();

            if (!empty($reviews) and $reviews->count() > 0) {
                $rate = number_format($reviews->avg('rates'), 2);
            }
        }


        if ($rate > 5) {
            $rate = 5;
        }

        return $rate > 0 ? number_format($rate, 2) : 0;
    }

    public function bestTicket($with_percent = false)
    {
        $ticketPercent = 0;
        $bestTicket = $this->price;

        $activeSpecialOffer = $this->activeSpecialOffer();

        if ($activeSpecialOffer) {
            $bestTicket = $this->price - ($this->price * $activeSpecialOffer->percent / 100);
            $ticketPercent = $activeSpecialOffer->percent;
        } else if (count($this->tickets)) {
            foreach ($this->tickets as $ticket) {
                if ($ticket->isValid()) {
                    $discount = $this->price - ($this->price * $ticket->discount / 100);

                    if ($bestTicket > $discount) {
                        $bestTicket = $discount;
                        $ticketPercent = $ticket->discount;
                    }
                }
            }
        }

        if ($with_percent) {
            return [
                'bestTicket' => $bestTicket,
                'percent' => $ticketPercent
            ];
        }

        return $bestTicket;
    }

    public function getBundleDuration()
    {
        if (empty($this->bundleDuration)) {
            $this->bundleDuration = $this->newQuery()
                ->where('bundles.id', $this->id)
                ->join('bundle_webinars', 'bundle_webinars.bundle_id', 'bundles.id')
                ->join('webinars', 'webinars.id', 'bundle_webinars.webinar_id')
                ->select('bundles.*', DB::raw('sum(webinars.duration) as duration'))
                ->sum('duration');
        }

        return $this->bundleDuration;
    }

    public function getExpiredAccessDays($purchaseDate, $giftId = null)
    {
        if (!empty($giftId)) {
            $gift = Gift::query()->where('id', $giftId)
                ->where('status', 'active')
                ->first();

            if (!empty($gift) and !empty($gift->date)) {
                $purchaseDate = $gift->date;
            }
        }

        return strtotime("+{$this->access_days} days", $purchaseDate);
    }

    public function checkHasExpiredAccessDays($purchaseDate, $giftId = null)
    {
        // true => has access
        // false => not access (expired)

        if (!empty($giftId)) {
            $gift = Gift::query()->where('id', $giftId)
                ->where('status', 'active')
                ->first();

            if (!empty($gift) and !empty($gift->date)) {
                $purchaseDate = $gift->date;
            }
        }

        $time = time();

        return strtotime("+{$this->access_days} days", $purchaseDate) > $time;
    }

    public function checkUserHasBought($user = null, $checkExpired = true): bool
    {
        $hasBought = false;

        if (empty($user) and auth()->check()) {
            $user = auth()->user();
        }

        if (!empty($user)) {
            $sale = Sale::where('buyer_id', $user->id)
                ->where('bundle_id', $this->id)
                ->where('type', 'bundle')
                ->whereNull('refund_at')
                ->where('access_to_purchased_item', true)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!empty($sale)) {
                $hasBought = true;

                if ($sale->payment_method == Sale::$subscribe) {
                    $subscribe = $sale->getUsedSubscribe($sale->buyer_id, $sale->bundle_id, 'bundle_id');

                    if (!empty($subscribe)) {
                        $subscribeSaleCreatedAt = null;

                        if (!empty($subscribe->installment_order_id)) {
                            $installmentOrder = InstallmentOrder::query()->where('user_id', $user->id)
                                ->where('id', $subscribe->installment_order_id)
                                ->where('status', 'open')
                                ->whereNull('refund_at')
                                ->first();

                            if (!empty($installmentOrder)) {
                                $subscribeSaleCreatedAt = $installmentOrder->created_at;

                                if ($installmentOrder->checkOrderHasOverdue()) {
                                    $overdueIntervalDays = getInstallmentsSettings('overdue_interval_days');

                                    if (empty($overdueIntervalDays) or $installmentOrder->overdueDaysPast() > $overdueIntervalDays) {
                                        $hasBought = false;
                                    }
                                }
                            }
                        } else {
                            $subscribeSale = Sale::where('buyer_id', $user->id)
                                ->where('type', Sale::$subscribe)
                                ->where('subscribe_id', $subscribe->id)
                                ->whereNull('refund_at')
                                ->latest('created_at')
                                ->first();

                            if (!empty($subscribeSale)) {
                                $subscribeSaleCreatedAt = $subscribeSale->created_at;
                            }
                        }

                        if (!empty($subscribeSaleCreatedAt)) {
                            $usedDays = (int)diffTimestampDay(time(), $subscribeSaleCreatedAt);

                            if ($usedDays > $subscribe->days) {
                                $hasBought = false;
                            }
                        }
                    } else {
                        $hasBought = false;
                    }
                }

                if ($hasBought and !empty($this->access_days) and $checkExpired) {
                    $hasBought = $this->checkHasExpiredAccessDays($sale->created_at, $sale->gift_id);
                }
            }

            if (!$hasBought) {
                $hasBought = ($this->creator_id == $user->id or $this->teacher_id == $user->id);
            }

            if (!$hasBought) {
                $hasBought = $user->isAdmin();
            }

            /* Check Installment */
            if (!$hasBought) {
                $installmentOrder = $this->getInstallmentOrder();

                if (!empty($installmentOrder)) {
                    $hasBought = true;

                    if ($installmentOrder->checkOrderHasOverdue()) {
                        $overdueIntervalDays = getInstallmentsSettings('overdue_interval_days');

                        if (empty($overdueIntervalDays) or $installmentOrder->overdueDaysPast() > $overdueIntervalDays) {
                            $hasBought = false;
                        }
                    }
                }
            }

            /* Check Gift */
            if (!$hasBought) {
                $gift = Gift::query()->where('email', $user->email)
                    ->where('status', 'active')
                    ->where('bundle_id', $this->id)
                    ->where(function ($query) {
                        $query->whereNull('date');
                        $query->orWhere('date', '<', time());
                    })
                    ->whereHas('sale')
                    ->first();

                if (!empty($gift)) {
                    $hasBought = true;
                }
            }
        }

        return $hasBought;
    }

    public function getInstallmentOrder()
    {
        $user = auth()->user();

        if (!empty($user)) {
            return InstallmentOrder::query()->where('user_id', $user->id)
                ->where('bundle_id', $this->id)
                ->where('status', 'open')
                ->whereNull('refund_at')
                ->first();
        }

        return null;
    }

    public function isOwner($userId = null)
    {
        if (empty($userId)) {
            $userId = auth()->id();
        }

        return (($this->creator_id == $userId) or ($this->teacher_id == $userId));
    }

    public function activeSpecialOffer()
    {
        $activeSpecialOffer = SpecialOffer::where('bundle_id', $this->id)
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

    public function canSale()
    {
        // TODO:: If there was a sales restriction like the courses, we apply here

        return true;
    }

    public function getShareLink($social)
    {
        $link = ShareFacade::page($this->getUrl())
            ->facebook()
            ->twitter()
            ->whatsapp()
            ->telegram()
            ->getRawLinks();

        return !empty($link[$social]) ? $link[$social] : '';
    }

    public function getDiscount($ticket = null, $user = null)
    {
        $activeSpecialOffer = $this->activeSpecialOffer();

        $discountOut = $activeSpecialOffer ? $this->price * $activeSpecialOffer->percent / 100 : 0;

        if (!empty($user) and !empty($user->getUserGroup()) and isset($user->getUserGroup()->discount) and $user->getUserGroup()->discount > 0) {
            $discountOut += $this->price * $user->getUserGroup()->discount / 100;
        }

        if (!empty($ticket) and $ticket->isValid()) {
            $discountOut += $this->price * $ticket->discount / 100;
        }

        return $discountOut;
    }

    public function getDiscountPercent()
    {
        $percent = 0;

        $activeSpecialOffer = $this->activeSpecialOffer();

        if (!empty($activeSpecialOffer)) {
            $percent += $activeSpecialOffer->percent;
        }

        $tickets = Ticket::where('webinar_id', $this->id)->get();

        foreach ($tickets as $ticket) {
            if (!empty($ticket) and $ticket->isValid()) {
                $percent += $ticket->discount;
            }
        }

        return $percent;
    }

    public function getStudentsIds()
    {
        $studentsIds = Sale::where('bundle_id', $this->id)
            ->whereNull('refund_at')
            ->pluck('buyer_id')
            ->toArray();

        // get users by installments
        $installmentOrders = InstallmentOrder::query()
            ->where('bundle_id', $this->id)
            ->where('status', 'open')
            ->whereNull('refund_at')
            ->get();

        foreach ($installmentOrders as $installmentOrder) {
            if (!empty($installmentOrder)) {
                $hasBought = true;

                if ($installmentOrder->checkOrderHasOverdue()) {
                    $overdueIntervalDays = getInstallmentsSettings('overdue_interval_days');

                    if (empty($overdueIntervalDays) or $installmentOrder->overdueDaysPast() > $overdueIntervalDays) {
                        $hasBought = false;
                    }
                }

                if ($hasBought) {
                    $studentsIds[] = $installmentOrder->user_id;
                }
            }
        }

        // get users by gifts
        $gifts = Gift::query()
            ->where('status', 'active')
            ->where('bundle_id', $this->id)
            ->where(function ($query) {
                $query->whereNull('date');
                $query->orWhere('date', '<', time());
            })
            ->whereHas('sale')
            ->get();

        foreach ($gifts as $gift) {
            $user = User::query()->select('id', 'email')->where('email', $gift->email)->first();

            if (!empty($user)) {
                $studentsIds[] = $user->id;
            }
        }

        return array_unique($studentsIds);
    }

    public function checkShowProgress()
    {
        return false;
    }

    public function makeCertificateForUser($user)
    {
        if (!empty($user) and $this->certificate) {
            $check = Certificate::where('type', 'bundle')
                ->where('student_id', $user->id)
                ->where('bundle_id', $this->id)
                ->first();

            if (empty($check)) {
                $makeCertificate = new MakeCertificate();
                $userCertificate = $makeCertificate->saveBundleCertificate($user, $this);

                $certificateReward = RewardAccounting::calculateScore(Reward::CERTIFICATE);
                RewardAccounting::makeRewardAccounting($userCertificate->student_id, $certificateReward, Reward::CERTIFICATE, $userCertificate->id, true);
            }
        }
    }

}
