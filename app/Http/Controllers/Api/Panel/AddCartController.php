<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Models\Api\Bundle;
use App\Models\Cart;
use App\Models\Api\Product;
use App\Models\ProductOrder;
use App\Models\ReserveMeeting;
use App\Models\Ticket;
use App\Models\Api\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Calculation\Web;

class AddCartController extends Controller
{
    public $cookieKey = 'carts';

    public function storeUserWebinarCart($user, $data)
    {
        $webinar_id = $data['item_id'];
        $ticket_id = $data['ticket_id'] ?? null;


        validateParam($data, [
            'item_id' => Rule::exists('webinars', 'id')
                ->where('status', 'active')->where('private', false)

        ]);

        $webinar = Webinar::find($webinar_id);
        if (!empty($webinar) and !empty($user)) {
            $checkCourseForSale = $webinar->checkWebinarForSale($user);

            if ($checkCourseForSale != 'ok') {
                return $checkCourseForSale;
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

            return 'ok';
        }
    }

    public function storeUserBundleCart($user, $data)
    {
        $bundle_id = $data['item_id'];
        $ticket_id = $data['ticket_id'] ?? null;

        validateParam($data, [
            'item_id' => Rule::exists('bundles', 'id')
                ->where('status', 'active')

        ]);

        $bundle = Bundle::where('id', $bundle_id)
            ->where('status', 'active')
            ->first();

        if (!empty($bundle) and !empty($user)) {
            $checkCourseForSale = $bundle->checkWebinarForSale($user);

            if ($checkCourseForSale != 'ok') {
                return $checkCourseForSale;
            }

            $activeSpecialOffer = $bundle->activeSpecialOffer();

            Cart::updateOrCreate([
                'creator_id' => $user->id,
                'bundle_id' => $bundle_id,
            ], [
                'ticket_id' => $ticket_id,
                'special_offer_id' => !empty($activeSpecialOffer) ? $activeSpecialOffer->id : null,
                'created_at' => time()
            ]);

            return 'ok';
        }

    }

    public function storeUserProductCart($user, $data)
    {
        $product_id = $data['item_id'];
        $specifications = $data['specifications'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        validateParam($data, [
            'item_id' => Rule::exists('products', 'id')
                ->where('status', 'active')

        ]);
        $product = Product::where('id', $product_id)
            ->where('status', 'active')
            ->first();


        if (!empty($product) and !empty($user)) {

            $checkProductForSale = $product->checkProductForSale($user);

            if ($checkProductForSale != 'ok') {
                return $checkProductForSale;
            }

            $activeDiscount = $product->getActiveDiscount();

            $productOrder = ProductOrder::updateOrCreate([
                'product_id' => $product->id,
                'seller_id' => $product->creator_id,
                'buyer_id' => $user->id,
                'sale_id' => null,
                'status' => 'pending',
            ], [
                'specifications' => $specifications ? json_encode($specifications) : null,
                'quantity' => $quantity,
                'discount_id' => !empty($activeDiscount) ? $activeDiscount->id : null,
                'created_at' => time()
            ]);

            Cart::updateOrCreate([
                'creator_id' => $user->id,
                'product_order_id' => $productOrder->id,
            ], [
                'product_discount_id' => !empty($activeDiscount) ? $activeDiscount->id : null,
                'created_at' => time()
            ]);

            return 'ok';
        }
    }

    public function store(Request $request)
    {
        $user = apiAuth();
        validateParam($request->all(), [
            'item_id' => 'required',
            'item_name' => 'required|in:webinar,bundle,product',
            'ticket_id' => 'nullable',
            'specifications' => 'nullable',
            'quantity' => 'nullable'
        ]);
        $rr = $request->input('item_name') . '_id';
        if (Cart::where($rr, $request->input('item_id'))->where('creator_id', $user->id)->count()) {
            return apiResponse2(0, 'already_in_cart', 'this item is in the cart');
        }


        $data = $request->except('_token');
        $item_name = $data['item_name'];

        $result = null;

        if ($item_name == 'webinar') {
            $result = $this->storeUserWebinarCart($user, $data);
        } elseif ($item_name == 'product') {
            $result = $this->storeUserProductCart($user, $data);
        } elseif ($item_name == 'bundle') {
            $result = $this->storeUserBundleCart($user, $data);
        }

        if ($result != 'ok') {
            return $result;
        }
        return apiResponse2(1, 'stored', trans('cart.cart_add_success_msg'));

    }

    public function destroy($id)
    {
        if (auth()->check()) {
            $user_id = auth()->id();

            $cart = Cart::where('id', $id)
                ->where('creator_id', $user_id)
                ->first();

            if (!empty($cart)) {
                if (!empty($cart->reserve_meeting_id)) {
                    $reserve = ReserveMeeting::where('id', $cart->reserve_meeting_id)
                        ->where('user_id', $user_id)
                        ->first();

                    if (!empty($reserve)) {
                        $reserve->delete();
                    }
                }

                $cart->delete();
            }
        } else {
            $carts = Cookie::get($this->cookieKey);

            if (!empty($carts)) {
                $carts = json_decode($carts, true);

                if (!empty($carts[$id])) {
                    unset($carts[$id]);
                }

                Cookie::queue($this->cookieKey, json_encode($carts), 30 * 24 * 60);
            }
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
