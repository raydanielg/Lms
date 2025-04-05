<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Api\Product;
use App\Models\Api\Comment;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = apiAuth();

        if ((!$user->isTeacher() and !$user->isOrganization()) or !$user->checkCanAccessToStore()) {
            abort(403);
        }

        $query = Product::where('creator_id', $user->id);
        $physicalProducts = deepClone($query)->where('type', Product::$physical)->count();;
        $virtualProducts = deepClone($query)->where('type', Product::$virtual)->count();

        $totalPhysicalSales = deepClone($query)->where('products.type', Product::$physical)
            ->join('product_orders', 'products.id', 'product_orders.product_id')
            ->leftJoin('sales', function ($join) {
                $join->on('product_orders.id', '=', 'sales.product_order_id')
                    ->whereNull('sales.refund_at');
            })
            ->select(DB::raw('sum(sales.total_amount) as total_sales'))
            ->whereNotNull('product_orders.sale_id')
            ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending])
            ->first();

        $totalVirtualSales = deepClone($query)->where('products.type', Product::$virtual)
            ->join('product_orders', 'products.id', 'product_orders.product_id')
            ->leftJoin('sales', function ($join) {
                $join->on('product_orders.id', '=', 'sales.product_order_id')
                    ->whereNull('sales.refund_at');
            })
            ->select(DB::raw('sum(sales.total_amount) as total_sales'))
            ->whereNotNull('product_orders.sale_id')
            ->whereNotIn('product_orders.status', [ProductOrder::$canceled, ProductOrder::$pending])
            ->first();


        $products = deepClone($query)
            ->orderBy('created_at', 'desc')
            ->get();

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            ['products' => ProductResource::collection($products),
                'physical_products_count' => $physicalProducts,
                'virtual_products_count' => $virtualProducts,
                'physical_products_sale' => (float)$totalPhysicalSales->total_sales ?? 0,
                'virtual_products_sale' => (float)$totalVirtualSales->total_sales ?? 0,
            ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::where('creator_id', apiAuth()->id)
            ->where('id', $id)->get();
        if (!$product) {
            // abort(404);
        }

    }

    public function purchasedComment()
    {
        $comments = Comment::where('user_id', apiAuth()->id)
            ->whereNotNull('product_id')
            ->handleFilters()->orderBy('created_at', 'desc')->get()->map(function ($comment) {
                return $comment->details;
            });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [
                'comments' => $comments,

            ]);

    }

    public function myComments(Request $request)
    {
        $user = apiAuth();

        $query = Comment::where('status', 'active')
            ->whereNotNull('product_id')
            ->whereHas('product', function ($query) use ($user) {
                $query->where('creator_id', $user->id);
            });

        $repliedCommentsCount = deepClone($query)->whereNotNull('reply_id')->count();

        $comments = $query->handleFilters()->orderBy('created_at', 'desc')
            ->get();

        foreach ($comments->whereNull('viewed_at') as $comment) {
            $comment->update([
                'viewed_at' => time()
            ]);
        }
        $comments = $comments->map(function ($comment) {
            return $comment->details;
        });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [
                'comments_count' => $comments->count(),
                'replied_comment_count' => $repliedCommentsCount,
                'comments' => $comments,

            ]);

    }


}
