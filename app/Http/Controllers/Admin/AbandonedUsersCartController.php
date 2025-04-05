<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AbandonedUsersCartExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\CartController;
use App\Models\AbandonedCartRuleHistory;
use App\Models\Cart;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AbandonedUsersCartController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize("admin_abandoned_cart_users");

        $query = Cart::query()->select('*', DB::raw("count(id) as total_items"))
            ->groupBy('creator_id');

        $totalUsers = Cart::distinct('creator_id')->count('creator_id');

        $totalAmount = $this->getTotalAmount();

        $totalItems = Cart::query()->count();

        $totalSentReminders = $this->getTotalReminders();


        $carts = $this->handleFilters($request, $query)
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'full_name', 'role_name', 'role_id', 'mobile', 'email');
                    $query->with(['carts']);
                }
            ])
            ->paginate(10);

        $cartController = new CartController();

        foreach ($carts as $cart) {
            $userCarts = $cart->user->carts;
            $calculate = $cartController->calculatePrice($userCarts, $cart->user);

            $cart->total_amount = $calculate["total"];


            $historyQuery = AbandonedCartRuleHistory::query()->where('user_id', $cart->creator_id);
            $cart->send_reminders = deepClone($historyQuery)
                ->where('rule_action', 'send_reminder')
                ->count();

            $cart->send_coupons = deepClone($historyQuery)
                ->where('rule_action', 'send_coupon')
                ->count();
        }

        if (!empty($request->get('excel'))) {
            return $this->exportExcel($carts);
        }

        $data = [
            'pageTitle' => trans('update.users_carts'),
            'carts' => $carts,
            'totalUsers' => $totalUsers,
            'totalAmount' => $totalAmount,
            'totalItems' => $totalItems,
            'totalSentReminders' => $totalSentReminders,
        ];

        return view('admin.abandoned_cart.users_carts.index', $data);
    }

    private function getTotalReminders()
    {
        /*$d= DB::table(DB::raw('(
                SELECT
                    COUNT(DISTINCT abandoned_cart_rule_histories.id) AS reminders
                FROM
                    `cart`
                LEFT JOIN
                    `abandoned_cart_rule_histories` ON `abandoned_cart_rule_histories`.`user_id` = `cart`.`creator_id`
                WHERE
                    `abandoned_cart_rule_histories`.`rule_action` = "send_reminder"
                GROUP BY
                    `cart`.`creator_id`
            ) as subquery'))
            ->selectRaw('SUM(reminders) as ddd')->value('ddd');*/

        return AbandonedCartRuleHistory::query()
            ->where('rule_action', 'send_reminder')
            ->count();
    }

    private function getTotalAmount()
    {
        $items = [
            'webinar_id' => 'webinars', // price
            'bundle_id' => 'bundles', // price
            'subscribe_id' => 'subscribes', // price
            'promotion_id' => 'promotions', // price
            'reserve_meeting_id' => 'reserve_meetings', // paid_amount
            'product_order_id' => 'product_orders',
        ];

        $totalPrice = 0;

        foreach ($items as $column => $table) {
            $query = Cart::query();
            $query->join("{$table}", "{$table}.id", "cart.{$column}");

            if ($column == "reserve_meeting_id") {
                $query->addSelect(DB::raw("sum({$table}.paid_amount) as {$table}_price"));

            } elseif ($column == "product_order_id") {
                $query->join('products', "products.id", "product_orders.product_id");
                $query->addSelect(DB::raw("sum(products.price) as product_orders_price"));

            } else {
                $query->addSelect(DB::raw("sum({$table}.price) as {$table}_price"));
            }

            $row = $query->first();

            if (!empty($row)) {
                $totalPrice += $row->{"{$table}_price"};
            }
        }

        return $totalPrice;
    }

    private function handleFilters(Request $request, $query)
    {
        $search = $request->get("search");
        $role = $request->get("role");
        $minimum_amount = $request->get("minimum_amount"); // It is not possible to specify the minimum and maximum amount of shopping cart items for each user in the query
        $maximum_amount = $request->get("maximum_amount");
        $sort = $request->get("sort");

        if (!empty($search)) {
            $query->whereHas('user', function ($query) use ($search) {
                $query->where('full_name', 'like', "%{$search}%");
            });
        }

        if (!empty($role)) {
            $roleName = Role::$user;

            if ($role == "instructor") {
                $roleName = Role::$teacher;
            } else if ($role == "organization") {
                $roleName = Role::$organization;
            }

            $query->whereHas('user', function ($query) use ($roleName) {
                $query->where('role_name', $roleName);
            });
        }

        if (!empty($sort)) {
            switch ($sort) {
                case "items_asc":
                    $query->orderBy('total_items', 'asc');
                    break;

                case "items_desc":
                    $query->orderBy('total_items', 'desc');
                    break;
            }
        }

        return $query;
    }

    public function sendReminder($userId)
    {
        $this->authorize("admin_abandoned_cart_users");

        $user = User::findOrFail($userId);
        $carts = Cart::query()->where('creator_id', $userId)->get();
        $userTotalCartsPrice = Cart::getCartsTotalPrice($carts);

        $template = getAbandonedCartSettings("default_cart_reminder");

        $notifyOptions = [
            "[u.name]" => $user->full_name,
            "[amount]" => $userTotalCartsPrice
        ];

        sendNotification($template, $notifyOptions, $user->id, null, 'system', 'single', $template);

        AbandonedCartRuleHistory::query()->create([
            'cart_rule_id' => null,
            'user_id' => $user->id,
            'rule_action' => 'send_reminder',
            'type' => 'manual',
            'created_at' => time()
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.reminder_sent_successfully'),
            'status' => 'success'
        ];

        return redirect()->back()->with(['toast' => $toastData]);
    }

    public function viewItems($userId)
    {
        $this->authorize("admin_abandoned_cart_users");

        $user = User::findOrFail($userId);
        $carts = Cart::query()->where('creator_id', $userId)->get();

        $data = [
            'pageTitle' => "«{$user->full_name}»" . ' ' . trans("cart.cart_items"),
            'carts' => $carts,
        ];

        return view('admin.abandoned_cart.users_carts.viewItems.index', $data);
    }

    public function empty($userId)
    {
        $this->authorize("admin_abandoned_cart_users");

        Cart::query()->where('creator_id', $userId)->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.user_carts_empty_successfully'),
            'status' => 'success'
        ];

        return redirect()->back()->with(['toast' => $toastData]);
    }

    public function deleteById($cartId)
    {
        $this->authorize("admin_abandoned_cart_users");

        Cart::query()->where('id', $cartId)->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.items_deleted_successful'),
            'status' => 'success'
        ];

        return redirect()->back()->with(['toast' => $toastData]);
    }

    private function exportExcel($carts)
    {
        $export = new AbandonedUsersCartExport($carts);

        return Excel::download($export, 'abandoned-users-carts.xlsx');
    }

}
