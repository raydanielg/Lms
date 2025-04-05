<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartDiscount;
use App\Models\Discount;
use App\Models\Translation\CartDiscountTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CartDiscountController extends Controller
{
    public function index()
    {
        $this->authorize("admin_cart_discount_controls");

        $discounts = Discount::query()
            ->whereHas('creator', function ($query) {
                $query->whereHas('role', function ($query) {
                    $query->where('is_admin', true);
                });
            })
            ->where('status', 'active')
            ->where(function (Builder $query) {
                $query->whereNull('expired_at');
                $query->orWhere('expired_at', '>', time());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $cartDiscount = CartDiscount::query()->first();

        $data = [
            'pageTitle' => trans('update.cart_discount'),
            'discounts' => $discounts,
            'cartDiscount' => $cartDiscount,
        ];

        return view('admin.cart_discount.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("admin_cart_discount_controls");

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string',
            'discount_id' => 'required|exists:discounts,id',
        ]);

        $data = $request->all();

        CartDiscount::query()->delete();

        $cartDiscount = CartDiscount::query()->create([
            'discount_id' => $data['discount_id'],
            'show_only_on_empty_cart' => (!empty($data['show_only_on_empty_cart']) and $data['show_only_on_empty_cart'] == '1'),
            'enable' => (!empty($data['enable']) and $data['enable'] == '1'),
            'created_at' => time()
        ]);

        CartDiscountTranslation::query()->updateOrCreate([
            'cart_discount_id' => $cartDiscount->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'],
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.cart_discount_successfully_created'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/cart_discount"))->with(['toast' => $toastData]);
    }


}
