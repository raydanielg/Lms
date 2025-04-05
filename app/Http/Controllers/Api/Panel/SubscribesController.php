<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\Api\Bundle;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Accounting;
use App\Models\Api\Webinar;
use App\User;
use App\Models\SubscribeUse;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\Models\Api\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\URL;


class SubscribesController extends Controller
{
    public function index(Request $request)
    {
        $user = apiAuth();
        $subscribes = Subscribe::all()->map(function ($subscribe) {
            return $subscribe->details;
        });

        $activeSubscribe = Subscribe::getActiveSubscribe($user->id);
        $dayOfUse = Subscribe::getDayOfUse($user->id);
        //   $has_active_subscribe=($activeSubscribe)?true:false ;

        $data = [
            'subscribes' => $subscribes,
            'subscribed' => ($activeSubscribe) ? true : false,
            'subscribe_id' => ($activeSubscribe) ? $activeSubscribe->id : null,
            'subscribed_title' => ($activeSubscribe) ? $activeSubscribe->title : null,
            'remained_downloads' => ($activeSubscribe) ? $activeSubscribe->usable_count - $activeSubscribe->used_count : null,
            'days_remained' => ($activeSubscribe) ? $activeSubscribe->days - $dayOfUse : null,
            'dayOfUse' => $dayOfUse,
        ];
        return apiResponse2(1, 'retrieved', trans('public.retrieved'), $data);
    }

    public function webPayGenerator(Request $request)
    {

        validateParam($request->all(), [
            'subscribe_id' => ['required', Rule::exists('subscribes', 'id')]
        ]);

        $user = apiAuth();
        $activeSubscribe = Subscribe::getActiveSubscribe($user->id);

        if ($activeSubscribe) {

            return apiResponse2(0, 'has_active_subscribe',
                trans('site.you_have_active_subscribe'), null,
                trans('public.request_failed')

            );
        }

        return apiResponse2(1, 'generated', trans('api.link.generated'),
            [
                'link' => URL::signedRoute('my_api.web.subscribe', [apiAuth()->id
                    , $request->input('subscribe_id')
                ])

            ]
        );
    }

    public function webPayRender(Request $request, User $user, $subscribe_id)
    {
        $id = $subscribe_id;
        $subscribe = Subscribe::find($id);
        $amount = $subscribe->price;

        Auth::login($user);

        return view('api.subscribe', compact('amount', 'id'));
    }

    public function pay(Request $request)
    {
        validateParam($request->all(), [
            'subscribe_id' => ['required', Rule::exists('subscribes', 'id')]
        ]);
        $paymentChannels = PaymentChannel::where('status', 'active')->get();

        $subscribe_id = $request->input('subscribe_id');
        $subscribe = Subscribe::find($subscribe_id);

        $user = apiAuth();
        $activeSubscribe = Subscribe::getActiveSubscribe($user->id);

        if ($activeSubscribe) {

            return apiResponse2(0, 'has_active_subscribe', trans('api.subscribe.has_active_subscribe'));
        }

        $financialSettings = getFinancialSettings();
        $tax = $financialSettings['tax'] ?? 0;

        $amount = $subscribe->price;

        $taxPrice = $tax ? $amount * $tax / 100 : 0;

        $order = Order::create([
            "user_id" => $user->id,
            "status" => Order::$pending,
            'tax' => $taxPrice,
            'commission' => 0,
            "amount" => $amount,
            "total_amount" => $amount + $taxPrice,
            "created_at" => time(),
        ]);

        OrderItem::updateOrCreate([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'subscribe_id' => $subscribe->id,
        ], [
            'amount' => $order->amount,
            'total_amount' => $amount + $taxPrice,
            'tax' => $tax,
            'tax_price' => $taxPrice,
            'commission' => 0,
            'commission_price' => 0,
            'created_at' => time(),
        ]);

        $razorpay = false;
        foreach ($paymentChannels as $paymentChannel) {
            if ($paymentChannel->class_name == 'Razorpay') {
                $razorpay = true;
            }
        }


        $data = [
            //  'pageTitle' => trans('public.checkout_page_title'),
            'paymentChannels' => $paymentChannels,
            'total' => $order->total_amount,
            'order' => $order,
            // 'count' => 1,
            'userCharge' => $user->getAccountingCharge(),
            'razorpay' => $razorpay
        ];

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $data);

    }

    public function apply(Request $request)
    {
        validateParam($request->all(), [

            'webinar_id' => ['required',
                Rule::exists('webinars', 'id')->where('private', false)
                    ->where('status', 'active')]
        ]);

        $user = apiAuth();

        $subscribe = Subscribe::getActiveSubscribe($user->id);
        $webinar = Webinar::find($request->input('webinar_id'));

        if (!$webinar->subscribe) {
            return apiResponse2(0, 'not_subscribable', trans('api.course.not_subscribable'));

        }

        if (!$subscribe) {

            return apiResponse2(0, 'no_active_subscribe',
                trans('site.you_dont_have_active_subscribe'), null,
                trans('public.request_failed')

            );

        }
        $checkCourseForSale = $webinar->canAddToCart($user);

        if ($checkCourseForSale == 'free') {
            return apiResponse2(0, 'free',
                trans('api.cart.free')

            );
        }

        if ($checkCourseForSale != 'ok') {
            return apiResponse2(0, $checkCourseForSale,
                $webinar->checkCourseForSaleMsg(), null,
                trans('public.request_failed')

            );
        }

        $sale = Sale::create([
            'buyer_id' => $user->id,
            'seller_id' => $webinar->creator_id,
            'webinar_id' => $webinar->id,
            'subscribe_id' => $subscribe->id,
            'type' => Sale::$webinar,
            'payment_method' => Sale::$subscribe,
            'amount' => 0,
            'total_amount' => 0,
            'created_at' => time(),
        ]);

        Accounting::createAccountingForSaleWithSubscribe($webinar, $subscribe,Sale::$webinar."_id");

        SubscribeUse::create([
            'user_id' => $user->id,
            'subscribe_id' => $subscribe->id,
            'webinar_id' => $webinar->id,
            'sale_id' => $sale->id,
        ]);


        return apiResponse2(1, 'subscribed',
            trans('cart.success_pay_msg_subscribe'),
            null,
            trans('cart.success_pay_title')

        );
    }

    public function generalApply(Request $request)
    {
        validateParam($request->all(), [
            'item_id' => 'required',
            'item_name' => 'required|in:webinar,bundle',
        ]);
        $item_name = $request->input('item_name');
        $item_id = $request->input('item_id');
        $user = apiAuth();

        $subscribe = \App\Models\Subscribe::getActiveSubscribe($user->id);

        if (!$subscribe) {
            return apiResponse2(0, 'no_active_subscribe',
                trans('site.you_dont_have_active_subscribe')
            );
        }
        if ($item_name == 'webinar') {
            $item = Webinar::where('id', $item_id)
                ->where('status', 'active')
                // ->where('subscribe', true)
                ->first();
        } elseif ($item_name == 'bundle') {
            $item = Bundle::where('id', $item_id)
                //  ->where('subscribe', true)
                ->first();
        }
        if (!$item->subscribe) {
            return apiResponse2(0, 'not_subscribable', trans('api.course.not_subscribable'));

        }

        $checkCourseForSale = $item->checkWebinarForSale($user);

        if ($checkCourseForSale != 'ok') {
            return $checkCourseForSale;
        }

        $sale = Sale::create([
            'buyer_id' => $user->id,
            'seller_id' => $item->creator_id,
            $item_name . '_id' => $item->id,
            'subscribe_id' => $subscribe->id,
            'type' => $item_name == 'webinar' ? Sale::$webinar : Sale::$bundle,
            'payment_method' => Sale::$subscribe,
            'amount' => 0,
            'total_amount' => 0,
            'created_at' => time(),
        ]);

        Accounting::createAccountingForSaleWithSubscribe($item, $subscribe, $item_name . '_id');

        SubscribeUse::create([
            'user_id' => $user->id,
            'subscribe_id' => $subscribe->id,
            $item_name . '_id' => $item->id,
            'sale_id' => $sale->id,
        ]);

        return apiResponse2(1, 'subscribed',
            trans('cart.success_pay_msg_subscribe')

        );

    }

}


