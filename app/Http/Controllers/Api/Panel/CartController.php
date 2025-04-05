<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Mixins\Cashback\CashbackRules;
use App\Models\Product;
use App\Models\ProductOrder;
use App\User;
use Illuminate\Http\Request;
use App\Models\Api\Cart;
use App\Models\ReserveMeeting;
use App\Models\Api\Webinar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Discount;
use App\Models\PaymentChannel;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Support\Facades\URL;


class CartController extends Controller
{
    public function index()
    {
        $user = apiAuth();
        $carts = Cart::where('creator_id', $user->id)
            ->with([
                'productOrder' => function ($query) {
                    $query->whereHas('product');
                }
            ])
            ->get();
        $cartt = null;

        if (!empty($carts) and !$carts->isEmpty()) {
            $calculate = $this->calculatePrice($carts, $user);

            $totalCashbackAmount = $this->getTotalCashbackAmount($carts, $user, $calculate["sub_total"]);

            $hasPhysicalProduct = $carts->where('productOrder.product.type', Product::$physical);

            $deliveryEstimateTime = 0;

            if (!empty($hasPhysicalProduct) and count($hasPhysicalProduct)) {
                foreach ($hasPhysicalProduct as $physicalProductCart) {
                    if (!empty($physicalProductCart->productOrder) and
                        !empty($physicalProductCart->productOrder->product) and
                        !empty($physicalProductCart->productOrder->product->delivery_estimated_time) and
                        $physicalProductCart->productOrder->product->delivery_estimated_time > $deliveryEstimateTime
                    ) {
                        $deliveryEstimateTime = $physicalProductCart->productOrder->product->delivery_estimated_time;
                    }
                }
            }

            if (!empty($calculate)) {

                $cartt = [
                    /*    'items' => $carts->map(function ($cart) {
                            return $cart->details;
                        }),*/
                    'items' => CartResource::collection($carts),
                    'amounts' => $calculate,
                    'totalCashbackAmount' => $totalCashbackAmount,
                    'user_group' => $user->userGroup ? $user->userGroup->group : null,
                ];

            }
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'cart' => $cartt
        ]);

    }

    public function destroy($id)
    {
        $user_id = apiAuth()->id;
        $cart = Cart::where('id', $id)
            ->where('creator_id', $user_id)
            ->first();
        abort_unless($cart, 404);

        if (!empty($cart->reserve_meeting_id)) {
            $reserve = ReserveMeeting::where('id', $cart->reserve_meeting_id)
                ->where('user_id', $user_id)
                ->first();

            if (!empty($reserve)) {
                $reserve->delete();
            }
        }

        $cart->delete();
        return apiResponse2(1, 'deleted', trans('api.public.deleted'));


    }

    public function store(Request $request)
    {
        $user = apiAuth();

        validateParam($request->all(),
            [
                'webinar_id' => ['required',
                    Rule::exists('webinars', 'id')->where('private', false)
                        ->where('status', 'active')
                ],
                'ticket_id' => 'nullable',
            ]
        );

        $webinar_id = $request->get('webinar_id');
        $ticket_id = $request->input('ticket_id');

        $webinar = Webinar::find($webinar_id);


        $checkCourseForSale = $webinar->canAddToCart();

        if ($checkCourseForSale != 'ok') {
            return apiResponse2(0, $checkCourseForSale, trans('api.course.purchase.' . $checkCourseForSale));
        }

        $activeSpecialOffer = $webinar->activeSpecialOffer();


        Cart::updateOrCreate([
            'creator_id' => $user->id,
            'webinar_id' => $webinar_id,
        ], [
            'ticket_id' => $ticket_id,
            'special_offer_id' => !empty($activeSpecialOffer) ? $activeSpecialOffer->id : null,
            'created_at' => time()
        ]);

        return apiResponse2(1, 'stored', trans('api.public.store'));

    }

    public function validateCoupon(Request $request)
    {
        $user = apiAuth();
        $coupon = $request->get('coupon');

        $discountCoupon = Discount::where('code', $coupon)
            ->where('expired_at', '>', time())
            ->first();

        if (!$discountCoupon || !$discountCoupon->checkValidDiscount($user)) {
            return apiResponse2(0, 'invalid', trans('api.cart.invalid_coupon'));

        }

        $carts = Cart::where('creator_id', $user->id)
            ->get();

        if (!empty($carts) and !$carts->isEmpty()) {
            $calculate = $this->calculatePrice($carts, $user, $discountCoupon);

            if (!empty($calculate)) {


                return apiResponse2(1, 'valid', trans('api.cart.valid_coupon'), [
                    'amounts' => $calculate,
                    'discount' => $discountCoupon,
                ]);

            }
        }


    }

    public function createOrderAndOrderItems($carts, $calculate, $user, $discountCoupon = null)
    {
        $order = Order::create([
            'user_id' => $user->id,
            'status' => Order::$pending,
            'amount' => $calculate["sub_total"],
            'tax' => $calculate["tax_price"],
            'total_discount' => $calculate["total_discount"],
            'total_amount' => $calculate["total"],
            'created_at' => time(),
        ]);

        foreach ($carts as $cart) {
            $price = 0;
            $discount = 0;
            $discountCouponPrice = 0;
            $sellerUser = null;

            if (!empty($cart->webinar_id)) {
                $price = $cart->webinar->price;
                $discount = $cart->webinar->getDiscount($cart->ticket, $user);
                $sellerUser = $cart->webinar->creator;
            } elseif (!empty($cart->reserve_meeting_id)) {
                $price = $cart->reserveMeeting->paid_amount;
                $discount = $price * $cart->reserveMeeting->discount / 100;
                $sellerUser = $cart->reserveMeeting->meeting->creator;
            }

            if (!empty($discountCoupon)) {
                if ($discountCoupon->discount_type == Discount::$discountTypeFixedAmount) {
                    $discountCouponPrice = $discountCoupon->amount;
                } else {
                    $couponAmount = $price * $discountCoupon->percent / 100;

                    if (!empty($discountCoupon->amount) and $couponAmount > $discountCoupon->amount) {
                        $discountCouponPrice += $discountCoupon->amount;
                    } else {
                        $discountCouponPrice += $couponAmount;
                    }
                }
            }


            $financialSettings = getFinancialSettings();
            $commission = $financialSettings['commission'] ?? 0;
            $tax = $financialSettings['tax'] ?? 0;

            if (!empty($sellerUser)) {
                $commission = $sellerUser->getCommission();
            }

            $allDiscountPrice = $discount + $discountCouponPrice;
            $subAmount = $price - $allDiscountPrice;

            if ($allDiscountPrice > $price) {
                $subAmount = 0;
            }

            $taxPrice = ($tax and $subAmount > 0) ? ($subAmount * $tax) / 100 : 0;
            $commissionPrice = $subAmount > 0 ? ($subAmount * $commission) / 100 : 0;
            $totalAmount = $subAmount + $taxPrice;

            $ticket = $cart->ticket;
            if (!empty($ticket) and !$ticket->isValid()) {
                $ticket = null;
            }

            OrderItem::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'webinar_id' => $cart->webinar_id ?? null,
                'reserve_meeting_id' => $cart->reserve_meeting_id ?? null,
                'subscribe_id' => $cart->subscribe_id ?? null,
                'promotion_id' => $cart->promotion_id ?? null,
                'ticket_id' => !empty($ticket) ? $ticket->id : null,
                'discount_id' => $discountCoupon ? $discountCoupon->id : null,
                'amount' => $price,
                'total_amount' => $totalAmount,
                'tax' => $tax,
                'tax_price' => $taxPrice,
                'commission' => $commission,
                'commission_price' => $commissionPrice,
                'discount' => $discount + $discountCouponPrice,
                'created_at' => time(),
            ]);
        }

        return $order;
    }

    public function webCheckoutGenerator(Request $request)
    {
        return apiResponse2(1, 'generated', trans('api.link.generated'),
            [
                'link' => URL::signedRoute('my_api.web.checkout', [apiAuth()->id, 'discount_id' => $request->input('discount_id')])
                ,
            ]
        );
    }

    public function webCheckoutRender(Request $request, User $user)
    {
        $discount_id = $request->input('discount_id');
        Auth::login($user);

        return view('api.checkout', compact('discount_id'));
    }


    public function checkout(Request $request)
    {

        $discountId = $request->input('discount_id');

        $paymentChannels = PaymentChannel::where('status', 'active')->get();

        $discountCoupon = Discount::where('id', $discountId)->first();

        if (empty($discountCoupon) or !$discountCoupon->checkValidDiscount()) {
            $discountCoupon = null;
        }

        $user = apiAuth();
        $carts = Cart::where('creator_id', $user->id)
            ->get();

        if (!empty($carts) and !$carts->isEmpty()) {
            $calculate = $this->calculatePrice($carts, $user, $discountCoupon);

            $order = $this->createOrderAndOrderItems($carts, $calculate, $user, $discountCoupon);

            if (!empty($order) and $order->total_amount > 0) {
                $razorpay = false;
                foreach ($paymentChannels as $paymentChannel) {
                    if ($paymentChannel->class_name == 'Razorpay') {
                        $razorpay = true;
                    }
                }

                $data = [
                    //      'pageTitle' => trans('public.checkout_page_title'),
                    'paymentChannels' => $paymentChannels,
                    'carts' => $carts->map(function ($cart) {
                        return $cart->details;
                    }),
                    // 'subTotal' => $calculate["sub_total"],
                    // 'totalDiscount' => $calculate["total_discount"],
                    //  'tax' => $calculate["tax"],
                    //   'taxPrice' => $calculate["tax_price"],
                    //     'total' => $calculate["total"],
                    'user_group' => $user->userGroup ? $user->userGroup->group : null,
                    'order' => $order,
                    'count' => $carts->count(),
                    'userCharge' => $user->getAccountingCharge(),
                    'razorpay' => $razorpay,
                    'amounts' => $calculate,
                ];

                return apiResponse2(1, 'checkout', trans('api.cart.checkout'), $data);


            } else {
                return $this->handlePaymentOrderWithZeroTotalAmount($order);
            }
        }

        return apiResponse2(0, 'empty_cart', trans('api.payment.empty_cart'));

    }

    private function handlePaymentOrderWithZeroTotalAmount($order)
    {
        $order->update([
            'payment_method' => Order::$paymentChannel
        ]);

        $paymentController = new PaymentsController();

        $paymentController->setPaymentAccounting($order);

        $order->update([
            'status' => Order::$paid
        ]);
        return apiResponse2(1, 'paid', trans('api.payment.paid'));

    }

    private function calculatePrice($carts, $user, $discountCoupon = null)
    {
        $financialSettings = getFinancialSettings();

        $subTotal = 0;
        $totalDiscount = 0;
        $tax = (!empty($financialSettings['tax']) and $financialSettings['tax'] > 0) ? $financialSettings['tax'] : 0;
        $taxPrice = 0;
        $commissionPrice = 0;
        $commission = 0;

        $cartHasWebinar = array_filter($carts->pluck('webinar_id')->toArray());
        $cartHasBundle = array_filter($carts->pluck('bundle_id')->toArray());
        $cartHasMeeting = array_filter($carts->pluck('reserve_meeting_id')->toArray());

        $taxIsDifferent = (count($cartHasWebinar) or count($cartHasBundle) or count($cartHasMeeting));

        foreach ($carts as $cart) {
            $orderPrices = $this->handleOrderPrices($cart, $user, $taxIsDifferent);
            $subTotal += $orderPrices['sub_total'];
            $totalDiscount += $orderPrices['total_discount'];
            $tax = $orderPrices['tax'];
            $taxPrice += $orderPrices['tax_price'];
            $commission += $orderPrices['commission'];
            $commissionPrice += $orderPrices['commission_price'];
            $taxIsDifferent = $orderPrices['tax_is_different'];
        }

        if (!empty($discountCoupon)) {
            $totalDiscount += $this->handleDiscountPrice($discountCoupon, $carts, $subTotal);
        }

        if ($totalDiscount > $subTotal) {
            $totalDiscount = $subTotal;
        }

        $subTotalWithoutDiscount = $subTotal - $totalDiscount;
        $productDeliveryFee = $this->calculateProductDeliveryFee($carts);

        $total = $subTotalWithoutDiscount + $taxPrice + $productDeliveryFee;

        if ($total < 0) {
            $total = 0;
        }

        $currencyItem = getUserCurrencyItem($user);
        return [
            'sub_total' => round($subTotal, 2),
            'total_discount' => round(convertPriceToUserCurrency($totalDiscount,$currencyItem), 2),
            'tax' => $tax,
            'tax_price' => round(convertPriceToUserCurrency($taxPrice,$currencyItem), 2),
            'commission' => $commission,
            'commission_price' => round(convertPriceToUserCurrency($commissionPrice,$currencyItem), 2),
            'total' => round($total, 2),
            'product_delivery_fee' => round(convertPriceToUserCurrency($productDeliveryFee,$currencyItem), 2),
            'tax_is_different' => $taxIsDifferent
        ];
    }

    private function updateProductOrders(Request $request, $carts, $user)
    {
        $data = $request->all();

        foreach ($carts as $cart) {
            if (!empty($cart->product_order_id)) {
                ProductOrder::where('id', $cart->product_order_id)
                    ->where('buyer_id', $user->id)
                    ->update([
                        'message_to_seller' => $data['message_to_seller'],
                    ]);
            }
        }

        $user->update([
            'country_id' => $data['country_id'] ?? $user->country_id,
            'province_id' => $data['province_id'] ?? $user->province_id,
            'city_id' => $data['city_id'] ?? $user->city_id,
            'district_id' => $data['district_id'] ?? $user->district_id,
            'address' => $data['address'] ?? $user->address,
        ]);
    }
    private function getSeller($cart)
    {
        $user = null;

        if (!empty($cart->webinar_id) or !empty($cart->bundle_id)) {
            $user = $cart->webinar_id ? $cart->webinar->creator : $cart->bundle->creator;
        } elseif (!empty($cart->reserve_meeting_id)) {
            $user = $cart->reserveMeeting->meeting->creator;
        } elseif (!empty($cart->product_order_id)) {
            $user = $cart->productOrder->seller;
        }

        return $user;
    }

    private function handleOrderPrices($cart, $user, $taxIsDifferent = false)
    {
        $financialSettings = getFinancialSettings();
        $seller = $this->getSeller($cart);

        $subTotal = 0;
        $totalDiscount = 0;
        $tax = (!empty($financialSettings['tax']) and $financialSettings['tax'] > 0) ? $financialSettings['tax'] : 0;
        $taxPrice = 0;
        $commissionPrice = 0;
        $commission = $seller->getCommission();

        if (!empty($cart->webinar_id) or !empty($cart->bundle_id)) {
            $item = !empty($cart->webinar_id) ? $cart->webinar : $cart->bundle;
            $price = $item->price;
            $discount = $item->getDiscount($cart->ticket, $user);

            $priceWithoutDiscount = $price - $discount;

            if ($tax > 0 and $priceWithoutDiscount > 0) {
                $taxPrice += $priceWithoutDiscount * $tax / 100;
            }

            if (!empty($commission) and $commission > 0) {
                $commissionPrice += $priceWithoutDiscount > 0 ? $priceWithoutDiscount * $commission / 100 : 0;
            }

            $totalDiscount += $discount;
            $subTotal += $price;
        } elseif (!empty($cart->reserve_meeting_id)) {
            $price = $cart->reserveMeeting->paid_amount;
            $discount = $cart->reserveMeeting->getDiscountPrice($user);

            $priceWithoutDiscount = $price - $discount;

            if ($tax > 0 and $priceWithoutDiscount > 0) {
                $taxPrice += $priceWithoutDiscount * $tax / 100;
            }

            if (!empty($commission) and $commission > 0) {
                $commissionPrice += $priceWithoutDiscount > 0 ? $priceWithoutDiscount * $commission / 100 : 0;
            }

            $totalDiscount += $discount;
            $subTotal += $price;
        } elseif (!empty($cart->product_order_id)) {
            $product = $cart->productOrder->product;

            if (!empty($product)) {
                $price = ($product->price * $cart->productOrder->quantity);
                $discount = $product->getDiscountPrice();

                $commission = $product->getCommission();
                $productTax = $product->getTax();

                $priceWithoutDiscount = $price - $discount;

                $taxIsDifferent = ($taxIsDifferent and $tax != $productTax);

                $tax = $productTax;
                if ($productTax > 0 and $priceWithoutDiscount > 0) {
                    $taxPrice += $priceWithoutDiscount * $productTax / 100;
                }

                if ($commission > 0) {
                    $commissionPrice += $priceWithoutDiscount > 0 ? $priceWithoutDiscount * $commission / 100 : 0;
                }

                $totalDiscount += $discount;
                $subTotal += $price;
            }
        }

        if ($totalDiscount > $subTotal) {
            $totalDiscount = $subTotal;
        }


        return [
            'sub_total' => round($subTotal, 2),
            'total_discount' => round($totalDiscount, 2),
            'tax' => $tax,
            'tax_price' => round($taxPrice, 2),
            'commission' => $commission,
            'commission_price' => round($commissionPrice, 2),
            //'product_delivery_fee' => round($productDeliveryFee, 2),
            'tax_is_different' => $taxIsDifferent
        ];
    }

    private function productDeliveryFeeBySeller($carts)
    {
        $productFee = [];

        foreach ($carts as $cart) {
            if (!empty($cart->productOrder) and !empty($cart->productOrder->product)) {
                $product = $cart->productOrder->product;

                if (!empty($product->delivery_fee)) {
                    if (!empty($productFee[$product->creator_id]) and $productFee[$product->creator_id] < $product->delivery_fee) {
                        $productFee[$product->creator_id] = $product->delivery_fee;
                    } else if (empty($productFee[$product->creator_id])) {
                        $productFee[$product->creator_id] = $product->delivery_fee;
                    }
                }
            }
        }

        return $productFee;
    }

    private function productCountBySeller($carts)
    {
        $productCount = [];

        foreach ($carts as $cart) {
            if (!empty($cart->productOrder) and !empty($cart->productOrder->product)) {
                $product = $cart->productOrder->product;

                if (!empty($productCount[$product->creator_id])) {
                    $productCount[$product->creator_id] += 1;
                } else {
                    $productCount[$product->creator_id] = 1;
                }
            }
        }

        return $productCount;
    }

    private function calculateProductDeliveryFee($carts)
    {
        $fee = 0;

        if (!empty($carts)) {
            $productsFee = $this->productDeliveryFeeBySeller($carts);

            if (!empty($productsFee) and count($productsFee)) {
                $fee = array_sum($productsFee);
            }
        }

        return $fee;
    }
    private function getTotalCashbackAmount($carts, $user, $subTotal)
    {
        $amount = 0;

        if (getFeaturesSettings('cashback_active') and (empty($user) or !$user->disable_cashback)) {
            $cashbackRulesMixin = new CashbackRules($user);
            $applyPerItemRules = [];

            foreach ($carts as $cart) {
                $rules = $cashbackRulesMixin->getRulesByItem($cart);

                if (!empty($rules) and count($rules)) {
                    foreach ($rules as $rule) {
                        if (empty($rule->min_amount) or $rule->min_amount <= $subTotal) {
                            if ($rule->amount_type == "fixed_amount") {
                                if ($rule->apply_cashback_per_item and $rule->apply_cashback_per_item > 0) {
                                    $amount += $rule->amount;
                                } else {
                                    $applyPerItemRules[$rule->id] = $rule;
                                }
                            } else if ($rule->amount_type == "percent") {
                                $itemOrder = $this->handleOrderPrices($cart, $user);
                                $itemPrice = $itemOrder['sub_total'];

                                if (!empty($itemOrder['total_discount'])) {
                                    $itemPrice = $itemPrice - $itemOrder['total_discount'];
                                }

                                $ruleAmount = $rule->getAmount($itemPrice);

                                if (!empty($rule->max_amount) and $rule->max_amount < $ruleAmount) {
                                    $amount += $rule->max_amount;
                                } else {
                                    $amount += $ruleAmount;
                                }
                            }
                        }
                    }
                }
            }


            if (!empty($applyPerItemRules)) {
                foreach ($applyPerItemRules as $applyPerItemRule) {
                    $amount += $applyPerItemRule->amount;
                }
            }
        }

        return $amount;
    }
    private function handleDiscountPrice($discount, $carts, $subTotal)
    {
        $user = apiAuth();
        $percent = $discount->percent ?? 1;
        $totalDiscount = 0;

        if ($discount->source == Discount::$discountSourceCourse) {
            $totalWebinarsAmount = 0;
            $webinarOtherDiscounts = 0;
            $discountWebinarsIds = $discount->discountCourses()->pluck('course_id')->toArray();

            foreach ($carts as $cart) {
                $webinar = $cart->webinar;
                if (!empty($webinar) and in_array($webinar->id, $discountWebinarsIds)) {
                    $totalWebinarsAmount += $webinar->price;
                    //$webinarOtherDiscounts += $webinar->getDiscount($cart->ticket, $user);
                }
            }

            if ($discount->discount_type == Discount::$discountTypeFixedAmount) {
                $totalDiscount = ($totalWebinarsAmount > $discount->amount) ? $discount->amount : $totalWebinarsAmount;

                /*if (!empty($webinarOtherDiscounts)) {
                    $totalDiscount = $totalDiscount - (int)$webinarOtherDiscounts;
                }*/
            } else {
                $totalDiscount = ($totalWebinarsAmount > 0) ? $totalWebinarsAmount * $percent / 100 : 0;
            }
        } elseif ($discount->source == Discount::$discountSourceBundle) {
            $totalBundlesAmount = 0;
            $bundleOtherDiscounts = 0;
            $discountBundlesIds = $discount->discountBundles()->pluck('bundle_id')->toArray();

            foreach ($carts as $cart) {
                $bundle = $cart->bundle;
                if (!empty($bundle) and in_array($bundle->id, $discountBundlesIds)) {
                    $totalBundlesAmount += $bundle->price;
                    //$bundleOtherDiscounts += $bundle->getDiscount($cart->ticket, $user);
                }
            }

            if ($discount->discount_type == Discount::$discountTypeFixedAmount) {
                $totalDiscount = ($totalBundlesAmount > $discount->amount) ? $discount->amount : $totalBundlesAmount;

                /*if (!empty($bundleOtherDiscounts)) {
                    $totalDiscount = $totalDiscount - (int)$bundleOtherDiscounts;
                }*/
            } else {
                $totalDiscount = ($totalBundlesAmount > 0) ? $totalBundlesAmount * $percent / 100 : 0;
            }
        } elseif ($discount->source == Discount::$discountSourceProduct) {
            $totalProductsAmount = 0;
            $productOtherDiscounts = 0;

            foreach ($carts as $cart) {
                if (!empty($cart->productOrder)) {
                    $product = $cart->productOrder->product;

                    if (!empty($product) and ($discount->product_type == 'all' or $discount->product_type == $product->type)) {
                        $totalProductsAmount += ($product->price * $cart->productOrder->quantity);
                        //$productOtherDiscounts += $product->getDiscountPrice();
                    }
                }
            }

            if ($discount->discount_type == Discount::$discountTypeFixedAmount) {
                $totalDiscount = ($totalProductsAmount > $discount->amount) ? $discount->amount : $totalProductsAmount;

                /*if (!empty($productOtherDiscounts)) {
                    $totalDiscount = $totalDiscount - (int)$productOtherDiscounts;
                }*/
            } else {
                $totalDiscount = ($totalProductsAmount > 0) ? $totalProductsAmount * $percent / 100 : 0;
            }
        } elseif ($discount->source == Discount::$discountSourceMeeting) {
            $totalMeetingAmount = 0;
            $meetingOtherDiscounts = 0;

            foreach ($carts as $cart) {
                $reserveMeeting = $cart->reserveMeeting;

                if (!empty($reserveMeeting)) {
                    $totalMeetingAmount += $reserveMeeting->paid_amount;
                    //$meetingOtherDiscounts += $reserveMeeting->getDiscountPrice($user);
                }
            }

            if ($discount->discount_type == Discount::$discountTypeFixedAmount) {
                $totalDiscount = ($totalMeetingAmount > $discount->amount) ? $discount->amount : $totalMeetingAmount;

                /*if (!empty($meetingOtherDiscounts)) {
                    $totalDiscount = $totalDiscount - $meetingOtherDiscounts;
                }*/
            } else {
                $totalDiscount = ($totalMeetingAmount > 0) ? $totalMeetingAmount * $percent / 100 : 0;
            }
        } elseif ($discount->source == Discount::$discountSourceCategory) {
            $totalCategoriesAmount = 0;
            $categoriesOtherDiscounts = 0;

            $categoriesIds = ($discount->discountCategories) ? $discount->discountCategories()->pluck('category_id')->toArray() : [];

            foreach ($carts as $cart) {
                $webinar = $cart->webinar;

                if (!empty($webinar) and in_array($webinar->category_id, $categoriesIds)) {
                    $totalCategoriesAmount += $webinar->price;
                    //$categoriesOtherDiscounts += $webinar->getDiscount($cart->ticket, $user);
                }
            }

            if ($discount->discount_type == Discount::$discountTypeFixedAmount) {
                $totalDiscount = ($totalCategoriesAmount > $discount->amount) ? $discount->amount : $totalCategoriesAmount;

                /*if (!empty($categoriesOtherDiscounts)) {
                    $totalDiscount = $totalDiscount - $categoriesOtherDiscounts;
                }*/
            } else {
                $totalDiscount = ($totalCategoriesAmount > 0) ? $totalCategoriesAmount * $percent / 100 : 0;
            }
        } else {
            $totalCartAmount = 0;
            $totalCartOtherDiscounts = 0;

            foreach ($carts as $cart) {
                $webinar = $cart->webinar;
                $reserveMeeting = $cart->reserveMeeting;

                if (!empty($webinar)) {
                    $totalCartAmount += $webinar->price;
                    //$totalCartOtherDiscounts += $webinar->getDiscount($cart->ticket, $user);
                }

                if (!empty($reserveMeeting)) {
                    $totalCartAmount += $reserveMeeting->paid_amount;
                    //$totalCartOtherDiscounts += $reserveMeeting->getDiscountPrice($user);
                }
            }

            if ($discount->discount_type == Discount::$discountTypeFixedAmount) {
                $totalDiscount = ($totalCartAmount > $discount->amount) ? $discount->amount : $totalCartAmount;

                /*if (!empty($totalCartOtherDiscounts)) {
                    $totalDiscount = $totalDiscount - $totalCartOtherDiscounts;
                }*/
            } else {
                $totalDiscount = ($totalCartAmount > 0) ? $totalCartAmount * $percent / 100 : 0;
            }
        }

        if ($discount->discount_type != Discount::$discountTypeFixedAmount and !empty($discount->max_amount) and $totalDiscount > $discount->max_amount) {
            $totalDiscount = $discount->max_amount;
        }

        return $totalDiscount;
    }

}
