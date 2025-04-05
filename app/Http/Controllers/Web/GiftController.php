<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\Gift;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\Sale;
use App\Models\Webinar;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function index(Request $request, $itemType, $itemSlug)
    {
        $item = $this->getItem($itemType, $itemSlug);

        $giftSettings = getGiftsGeneralSettings();

        $settingKey = 'allow_sending_gift_for_courses';
        $pageTitle = trans('update.send_course_as_a_gift');
        $titleHint = trans('update.send_this_course_to_your_friends_and_let_them_enjoy_effective_learning');

        if ($itemType == 'bundle') {
            $settingKey = 'allow_sending_gift_for_bundles';

            $pageTitle = trans('update.send_bundle_as_a_gift');
            $titleHint = trans('update.send_this_bundle_to_your_friends_and_let_them_enjoy_effective_learning');
        } else if ($itemType == 'product') {
            $settingKey = 'allow_sending_gift_for_products';

            $pageTitle = trans('update.send_product_as_a_gift');
            $titleHint = trans('update.send_this_product_to_your_friends_and_let_them_enjoy_effective_learning');
        }

        if (!empty($item) and !empty($giftSettings['status']) and !empty($giftSettings[$settingKey])) {

            if ($itemType == 'product') {
                $productAvailability = $item->getAvailability();

                if ($productAvailability < 1) {
                    $toastData = [
                        'title' => trans('public.request_failed'),
                        'msg' => trans('update.product_not_availability'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }
            }

            $data = [
                'pageTitle' => $pageTitle,
                'titleHint' => $titleHint,
                'itemType' => $itemType,
                'item' => $item
            ];

            return view('web.default.gift.index', $data);
        }

        abort(404);
    }

    public function store(Request $request, $itemType, $itemSlug)
    {
        $this->validate($request, [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'date' => 'nullable',
        ]);
        $data = $request->all();

        $item = $this->getItem($itemType, $itemSlug);

        $giftSettings = getGiftsGeneralSettings();

        $settingKey = 'allow_sending_gift_for_courses';

        if ($itemType == 'bundle') {
            $settingKey = 'allow_sending_gift_for_bundles';
        } else if ($itemType == 'product') {
            $settingKey = 'allow_sending_gift_for_products';
        }

        if (!empty($item) and !empty($giftSettings['status']) and !empty($giftSettings[$settingKey])) {
            $userId = auth()->id();

            // Free Item
            if (empty($item->price) or $item->price < 1) {
                return $this->handleFreeItem($item, $itemType, $data);
            }

            $gift = $this->createGift($item, $itemType, $data);
            $columnName = $this->getColumnName($itemType);
            $columnValue = $item->id;

            if ($itemType == "product") {
                $productOrder = ProductOrder::updateOrCreate([
                    'product_id' => $item->id,
                    'seller_id' => $item->creator_id,
                    'buyer_id' => null,
                    'sale_id' => null,
                    'gift_id' => $gift->id,
                    'status' => 'pending',
                ], [
                    'specifications' => null,
                    'quantity' => 1,
                    'discount_id' => null,
                    'created_at' => time()
                ]);

                $columnName = "product_order_id";
                $columnValue = $productOrder->id;
            }

            Cart::updateOrCreate([
                'creator_id' => $userId,
                $columnName => $columnValue,
                'gift_id' => $gift->id,
            ], [
                'created_at' => time()
            ]);

            return redirect('/cart');
        }

        abort(404);
    }

    private function handleFreeItem($item, $itemType, $data)
    {
        $gift = $this->createGift($item, $itemType, $data);

        $sale = Sale::create([
            'buyer_id' => auth()->id(),
            'seller_id' => $item->creator_id,
            'gift_id' => $gift->id,
            'type' => Sale::$gift,
            'payment_method' => Sale::$credit,
            'amount' => 0,
            'total_amount' => 0,
            'created_at' => time(),
        ]);

        $gift->update([
            'status' => 'active'
        ]);

        if ($itemType == "product") {
            ProductOrder::query()->create([
                'product_id' => $item->id,
                'seller_id' => $item->creator_id,
                'buyer_id' => null,
                'sale_id' => $sale->id,
                'gift_id' => $gift->id,
                'status' => ProductOrder::$success,
                'specifications' => null,
                'quantity' => 1,
                'discount_id' => null,
                'created_at' => time()
            ]);
        }

        $gift->sendNotificationsWhenActivated(0);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans("update.this_{$itemType}_was_sent_to_email_as_a_gift", ['email' => $data['email']]),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    private function createGift($item, $itemType, $data)
    {
        $columnName = $this->getColumnName($itemType);
        $dateTimestamp = !empty($data['date']) ? convertTimeToUTCzone($data['date'], getTimezone())->getTimestamp() : null;

        $gift = Gift::query()->updateOrCreate([
            'user_id' => auth()->id(),
            $columnName => $item->id,
            'status' => 'pending'
        ], [
            'name' => $data['name'],
            'email' => $data['email'],
            'date' => $dateTimestamp,
            'description' => $data['description'] ?? null,
            'viewed' => false,
            'created_at' => time(),
        ]);

        return $gift;
    }

    private function getColumnName($itemType)
    {
        $columnName = "webinar_id";

        if ($itemType == 'bundle') {
            $columnName = "bundle_id";
        } else if ($itemType == 'product') {
            $columnName = "product_id";
        }

        return $columnName;
    }

    private function getItem($itemType, $itemSlug)
    {
        if ($itemType == 'course') {
            return Webinar::query()->where('slug', $itemSlug)->first();
        } else if ($itemType == 'bundle') {
            return Bundle::query()->where('slug', $itemSlug)->first();
        } else if ($itemType == 'product') {
            $product = Product::query()->where('slug', $itemSlug)->first();

            if ($product->isVirtual()) {
                return $product;
            }
        }

        return null;
    }
}
