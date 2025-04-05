<?php

namespace App\Http\Controllers\Panel\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use App\Models\Region;
use App\Models\Sale;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize("panel_products_sales");

        $user = auth()->user();

        if (!$user->isOrganization() and !$user->isTeacher()) {
            abort(403);
        }

        $query = ProductOrder::where('product_orders.seller_id', $user->id)
            ->where('product_orders.status', '!=', 'pending')
            ->whereHas('sale', function ($query) {
                $query->whereNull('refund_at');
            });

        $totalOrders = deepClone($query)->count();
        $pendingOrders = deepClone($query)->where('product_orders.status', ProductOrder::$waitingDelivery)->count();
        $canceledOrders = deepClone($query)->where('product_orders.status', ProductOrder::$canceled)->count();

        $totalSales = deepClone($query)
            ->join('sales', 'sales.product_order_id', 'product_orders.id')
            ->select(DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as totalAmount')) // DB::raw("sum(sales.total_amount) as totalAmount")
            ->first();

        $customerIds = deepClone($query)->pluck('buyer_id')->toArray();
        $customers = User::select('id', 'full_name')
            ->whereIn('id', array_unique($customerIds))
            ->get();

        $query = $this->filters($query, $request);

        $orders = $query->orderBy('created_at', 'desc')
            ->with([
                'product',
                'sale',
                'buyer' => function ($query) {
                    $query->select('id', 'full_name', 'email', 'mobile', 'avatar');
                }
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.product_sales_lists_page_title'),
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'canceledOrders' => $canceledOrders,
            'totalSales' => $totalSales ? $totalSales->totalAmount : 0,
            'customers' => $customers,
            'orders' => $orders,
        ];

        return view('web.default.panel.store.sales', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $customer_id = $request->input('customer_id');
        $type = $request->input('type');
        $status = $request->input('status');

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($customer_id) and $customer_id != 'all') {
            $query->where('buyer_id', $customer_id);
        }

        if (isset($type) and $type !== 'all') {
            $query->whereHas('product', function ($query) use ($type) {
                $query->where('type', $type);
            });
        }

        if (isset($status) and $status !== 'all') {
            $query->where('status', $status);
        }

        return $query;
    }

    public function invoice($saleId, $orderId)
    {
        $user = auth()->user();

        $productOrder = ProductOrder::where('seller_id', $user->id)
            ->where('id', $orderId)
            ->where('sale_id', $saleId)
            ->whereHas('sale', function ($query) {
                $query->whereNull('refund_at');
            })
            ->first();

        if (!empty($productOrder) and !empty($productOrder->product)) {
            $data = [
                'pageTitle' => trans('webinars.invoice_page_title'),
                'order' => $productOrder,
                'product' => $productOrder->product,
                'sale' => $productOrder->sale,
                'seller' => $productOrder->seller,
                'buyer' => $productOrder->buyer,
            ];

            return view('web.default.panel.store.invoice', $data);
        }

        abort(404);
    }

    public function getProductOrder($saleId, $orderId)
    {
        $user = auth()->user();

        $order = ProductOrder::where('seller_id', $user->id)
            ->where('id', $orderId)
            ->where('sale_id', $saleId)
            ->first();

        if (!empty($order)) {
            $buyer = $order->buyer;

            $order->address = $buyer->getAddress(true);
        }

        return response()->json([
            'order' => $order
        ]);
    }

    public function setTrackingCode(Request $request, $saleId, $orderId)
    {
        $user = auth()->user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'tracking_code' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = ProductOrder::where('seller_id', $user->id)
            ->where('id', $orderId)
            ->where('sale_id', $saleId)
            ->first();

        if (!empty($order)) {
            $order->update([
                'tracking_code' => $data['tracking_code'],
                'status' => ProductOrder::$shipped
            ]);

            $product = $order->product;
            $seller = $order->seller;

            $notifyOptions = [
                '[p.title]' => $product->title,
                '[u.name]' => $seller->full_name
            ];
            sendNotification('product_tracking_code', $notifyOptions, $order->buyer_id);
        }

        return response()->json([
            'code' => 200
        ]);
    }
}
