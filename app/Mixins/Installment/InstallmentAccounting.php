<?php

namespace App\Mixins\Installment;

use App\Http\Controllers\Web\CartController;
use App\Models\Accounting;
use App\Models\Cart;
use App\Models\InstallmentOrderPayment;
use App\Models\OrderItem;

class InstallmentAccounting
{
    public function refundOrder($order)
    {
        $orderPayments = InstallmentOrderPayment::query()
            ->where('installment_order_id', $order->id)
            ->where('status', 'paid')
            ->with([
                'sale'
            ])
            ->get();

        if ($orderPayments->isNotEmpty()) {
            foreach ($orderPayments as $payment) {
                $sale = $payment->sale;

                if (!empty($sale)) {
                    // Buyer
                    Accounting::create([
                        'user_id' => $order->user_id,
                        'amount' => $sale->total_amount,
                        'installment_payment_id' => $payment->id,
                        'type' => Accounting::$addiction,
                        'type_account' => Accounting::$asset,
                        'description' => trans('update.installment_refund'),
                        'created_at' => time()
                    ]);

                    // System
                    Accounting::create([
                        'system' => true,
                        'user_id' => $order->user_id,
                        'amount' => $sale->total_amount,
                        'installment_payment_id' => $payment->id,
                        'type' => Accounting::$deduction,
                        'type_account' => Accounting::$income,
                        'description' => trans('update.installment_refund'),
                        'created_at' => time()
                    ]);

                    $sale->update([
                        'refund_at' => time()
                    ]);
                }

                $payment->update([
                    'status' => 'refunded'
                ]);
            }
        }

        $order->update([
            'status' => 'canceled'
        ]);

        // refund Seller Accounting
        $this->refundSellerOrder($order);

        return true;
    }

    public function refundSellerOrder($order)
    {
        $item = $order->getItem();

        if (!empty($item->creator_id)) {

            $accounting = Accounting::query()
                ->where('installment_order_id', $order->id)
                ->where('user_id', $item->creator_id)
                ->first();

            if (!empty($accounting)) {
                Accounting::create([
                    'user_id' => $item->creator_id,
                    'amount' => $accounting->amount,
                    'installment_order_id' => $order->id,
                    'type' => Accounting::$deduction,
                    'type_account' => Accounting::$income,
                    'description' => trans('update.installment_refund'),
                    'created_at' => time()
                ]);
            }
        }
    }

    public function createAccountingForSeller($order)
    {
        $orderPrices = $this->handleOrderPrices($order);

        $price = $orderPrices['sub_total'];
        $totalDiscount = $orderPrices['total_discount'];
        $tax = $orderPrices['tax'];
        $taxPrice = $orderPrices['tax_price'];
        $commission = $orderPrices['commission'];
        $commissionPrice = $orderPrices['commission_price'];
        $discountCouponPrice = 0;

        $allDiscountPrice = $totalDiscount + $discountCouponPrice;

        $subTotalWithoutDiscount = $price - $allDiscountPrice;
        $totalAmount = $subTotalWithoutDiscount + $taxPrice;


        $orderItem = new OrderItem();
        $orderItem->user_id = $order->user_id;
        $orderItem->order_id = 1; //
        $orderItem->installment_order_id = $order->id; //
        $orderItem->webinar_id = $order->webinar_id ?? null;
        $orderItem->bundle_id = $order->bundle_id ?? null;
        $orderItem->product_id = (!empty($order->product_order_id) and !empty($order->productOrder->product)) ? $order->productOrder->product->id : null;
        $orderItem->product_order_id = (!empty($order->product_order_id)) ? $order->product_order_id : null;
        $orderItem->amount = $price;
        $orderItem->total_amount = $totalAmount;
        $orderItem->tax = $tax;
        $orderItem->tax_price = $taxPrice;
        $orderItem->commission = $commission;
        $orderItem->commission_price = $commissionPrice;
        $orderItem->discount = $allDiscountPrice;
        $orderItem->created_at = time();

        Accounting::createAccountingSeller($orderItem);

        if ($orderItem->commission_price) {
            Accounting::createAccountingCommission($orderItem);
        }
    }

    private function handleOrderPrices($order)
    {
        $cart = new Cart();

        $cart->creator_id = $order->user_id;
        $cart->webinar_id = $order->webinar_id;
        $cart->bundle_id = $order->bundle_id;
        $cart->product_order_id = $order->product_order_id;
        $cart->subscribe_id = $order->subscribe_id;

        $cartController = new CartController();

        return $cartController->handleOrderPrices($cart, $order->user);
    }

}
