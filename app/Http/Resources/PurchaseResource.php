<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     * $this => App\Sale
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "seller_id" => $this->seller_id,
            "buyer_id" => $this->buyer_id,
            "order_id" => $this->order_id,
            "webinar_id" => $this->webinar_id,
            "bundle_id" => $this->bundle_id,
            "meeting_id" => $this->meeting_id,
            "meeting_time_id" => $this->meeting_time_id,
            "subscribe_id" => $this->subscribe_id,
            "ticket_id" => $this->ticket_id,
            "promotion_id" => $this->promotion_id,
            "product_order_id" => $this->product_order_id,
            "registration_package_id" => $this->registration_package_id,
            "installment_payment_id" => $this->installment_payment_id,
            "gift_id" => $this->gift_id,
            "payment_method" => $this->payment_method,
            "type" => $this->type,
            "amount" => $this->amount,
            "tax" => $this->tax,
            "commission" => $this->commission,
            "discount" => $this->discount,
            "total_amount" => $this->total_amount,
            "product_delivery_fee" => $this->product_delivery_fee,
            "manual_added" => $this->manual_added,
            "access_to_purchased_item" => $this->access_to_purchased_item,
            "created_at" => $this->created_at,
            "refund_at" => $this->refund_at,
            "expired" => $this->expired ?? false,
            "expired_at" => $this->expired_at ?? null,
            "webinar" => (!empty($this->webinar)) ? $this->webinar->brief : null,
            "bundle" => (!empty($this->bundle)) ? (new BundleResource($this->bundle)) : null
        ];
    }

}
