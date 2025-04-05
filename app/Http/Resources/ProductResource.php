<?php

namespace App\Http\Resources;

use App\Mixins\Cashback\CashbackRules;
use Illuminate\Http\Resources\Json\JsonResource;
use \App\Models\Product;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public $show = true;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'has_discount' => (bool)$this->getActiveDiscount(),
            'discount_percent' => ($this->getActiveDiscount()) ? (int)$this->getActiveDiscount()->percent : 0,
            'thumbnail' => url($this->thumbnail),
            'label' => $this->label,
            'url' => $this->getUrl(),
            'category_title' => $this->category->title ?? null,
            'title' => $this->title,
            'rate' => $this->getRate(),
            'reviews_count' => $this->reviews->pluck('creator_id')->count(),
            'type' => $this->type,
            'unlimited_inventory' => (bool)$this->unlimited_inventory,
            'availability' => ($this->unlimited_inventory) ? trans('update.unlimited') : $this->getAvailability(),
            'point' => $this->point,
            'sales_count' => (int)$this->salesCount() ?? 0,
            'sales_amount' => convertPriceToUserCurrency($this->sales()->sum('total_amount')) ?? 0,
            'shipping_cost' => convertPriceToUserCurrency($this->delivery_fee) ?? null,
            'delivery_estimated_time' => $this->delivery_estimated_time ?? null,
            'waiting_orders' => $this->waiting_orders,
            'price' => convertPriceToUserCurrency($this->price),
            'price_with_discount' => convertPriceToUserCurrency($this->getPriceWithActiveDiscountPrice()),
            'cashback_rules' => $this->cashbackRules,
            $this->mergeWhen($this->show, function () {
                return [
                    'video_demo' => $this->video_demo ? url($this->video_demo) : null,
                    'images' => $this->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'title' => $image->title,
                            'url' => $image->path ? url($image->path) : null];
                    }),
                    'selectable_specifications' => $this->dde(),
                    'selected_specifications' => $this->getPrettySpecification(),
                    'description' => $this->description,
                    'faqs' => FaqResource::collection($this->faqs),
                    'seller' => new UserResource($this->creator),
                    $this->mergeWhen($this->checkUserHasBought(apiAuth()), function () {
                        return [
                            'files' => ProductFileResource::collection($this->files),
                        ];
                    }),
                    'reviews' => ReviewResource::collection($this->reviews) ,
                    'comments'=>CommentResource::collection($this->comments)

                ];
            })
        ];
    }


}





