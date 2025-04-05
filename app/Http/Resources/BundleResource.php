<?php

namespace App\Http\Resources;

use App\Mixins\Cashback\CashbackRules;
use App\Models\Api\Bundle;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public $show = false;

    public function toArray($request)
    {
        $user = apiAuth();
        $purchase = $user ? $user->purchases()->where('bundle_id', $this->id)->first() : null;

        $bundle = new Bundle();
        $hasBought = $bundle->checkUserHasBought($user);
        $canSale = ($bundle->canSale() and !$hasBought);
        /* Cashback Rules */
        $cashbackRules = null;
        if ($canSale and !empty($this->price) and getFeaturesSettings('cashback_active') and (empty($user) or !$user->disable_cashback)) {
            $cashbackRulesMixin = new CashbackRules($user);
            $cashbackRules = $cashbackRulesMixin->getRules('bundles', $this->id, "bundle", $this->category->id, $this->teacher->id);
        }

        $duration = $this->getBundleDuration();
        $rate = $this->getRate();

        $hasBought = $this->checkUserHasBought($user);
        $canSale = ($this->canSale() and !$hasBought);
        $can_buy_with_points = ($canSale and $this->price > 0 and !empty($bundle->points));
        $can_buy_with_subscribe = ($canSale and $this->price > 0 and $this->subscribe);

        $isExpired = (!empty($purchase) and $this->access_days and !$this->checkHasExpiredAccessDays($purchase->created_at));

        return [
            'id' => $this->id,
            'image' => url($this->getImage()),
            'image_cover' => url($this->getImageCover()),
            'status' => $this->status,
            'label' => trans('update.bundle'),
            'link' => url($this->getUrl()),
            'title' => $this->title,
            'type' => 'bundle',
            'rate' => ($rate > 0) ? (float)$rate : 0,
            'rates_count' => $this->reviews->pluck('creator_id')->count(),
            'reviews_count' => $this->reviews->count(),
            'price' => convertPriceToUserCurrency($this->price),
            'price_details' => handleCoursePagePrice($this->price),
            'active_special_offer' => $this->activeSpecialOffer() ?: null,
            'best_ticket' => $this->bestTicket(),
            'category' => $this->category->title ?? null,
            'access_days' => $this->access_days,
            'expired' => $isExpired,
            'expire_on' => (!empty($purchase) and $isExpired) ? $this->getExpiredAccessDays($purchase->created_at) : null,

            //  'ex' => $this->checkHasExpiredAccessDays($sale->created_at),
            'duration' => ($duration > 0) ? (float)$duration : 0,
            'webinar_count' => $this->bundleWebinars->where('webinar.status', 'active')->count(),
            'teacher' => $this->teacher->brief,
            'sale_amount' => ($this->sales) ? convertPriceToUserCurrency($this->sales->sum('amount')) : 0,
            'sales_count' => $this->sales->count(),
            'students_count' => $this->sales->count(),
            'cashbackRules' => $cashbackRules,
            'created_at' => $this->created_at,
            'badges' => $this->badges ?? [],
            'auth_has_bought' => $hasBought,
            'can_sale' => $canSale,
            'can_buy_with_points' => $can_buy_with_points,
            'can_buy_with_subscribe' => $can_buy_with_subscribe,
            'is_favorite' => $this->is_favorite,

            $this->mergeWhen($this->show, function () {
                return [
                    'rate_type' => [
                        'content_quality' => $this->reviews->count() > 0 ? round($this->reviews->avg('content_quality'), 1) : 0,
                        'instructor_skills' => $this->reviews->count() > 0 ? round($this->reviews->avg('instructor_skills'), 1) : 0,
                        'purchase_worth' => $this->reviews->count() > 0 ? round($this->reviews->avg('purchase_worth'), 1) : 0,
                        'support_quality' => $this->reviews->count() > 0 ? round($this->reviews->avg('support_quality'), 1) : 0,

                    ],
                    'video_demo' => $this->video_demo,
                    'tickets' => TicketResource::collection($this->tickets),
                    'subscribable' => $this->subscribe,
                    'points' => $this->points,
                    'description' => $this->description,
                    'tags' => TagResource::collection($this->tags),
                    'faqs' => FaqResource::collection($this->faqs),
                    'comments' => CommentResource::collection($this->comments),
                    'reviews' => ReviewResource::collection($this->reviews),
                    // bundleWebinars
                ];
            }),
        ];
    }
}
