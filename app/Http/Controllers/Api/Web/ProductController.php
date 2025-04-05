<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Mixins\Cashback\CashbackRules;
use App\Models\AdvertisingBanner;
use App\Models\Api\Product;
use App\Models\Follow;
use App\Models\ProductCategory;
use App\Models\ProductSelectedSpecification;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();

        $products = Product::where('products.status', Product::$active)
            ->where('ordering', true)->handleFilters()->get();


        if (!empty($data['category_id'])) {
            $selectedCategory = ProductCategory::where('id', $data['category_id'])->first();
        }
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [
                'products' => ProductResource::collection($products),
            ]);

    }

    public function show($id)
    {
        $user = apiAuth();

        $product = Product::where('status', Product::$active)
            ->where('id', $id)
            ->with([
                'selectedSpecifications' => function ($query) {
                    $query->where('status', ProductSelectedSpecification::$Active);
                    $query->with(['specification']);
                },
                'comments' => function ($query) {
                    $query->where('status', 'active');
                    $query->whereNull('reply_id');
                    $query->with([
                        'replies' => function ($query) {
                            $query->where('status', 'active');
                        }
                    ]);
                    $query->orderBy('created_at', 'desc');
                },
                'files' => function ($query) {
                    $query->where('status', 'active');
                    $query->orderBy('order', 'asc');
                },
                'reviews' => function ($query) {
                    $query->where('status', 'active');
                    $query->with([
                        'comments' => function ($query) {
                            $query->where('status', 'active');
                        },
                    ]);
                },
            ])
            ->first();

        if (empty($product)) {
            abort(404);
        }

        $selectableSpecifications = $product->selectedSpecifications->where('allow_selection', true)
              ->where('type', 'multi_value');
          $selectedSpecifications = $product->selectedSpecifications->where('allow_selection', false);
         $seller = $product->creator;

        $cashbackRules = null;
        if (!empty($product->price) and getFeaturesSettings('cashback_active') and (empty($user) or !$user->disable_cashback)) {
            $cashbackRulesMixin = new CashbackRules($user);
            $cashbackRules = $cashbackRulesMixin->getRules('store_products', $product->id, $product->type, $product->category_id, $product->creator_id);
        }
        $product->cashbackRules = $cashbackRules;
        $resource = new ProductResource($product);
        $resource->show = true;
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [
                'product' =>$resource ,

            ]);


        return view(getTemplate() . '.products.show', $data);
    }


}
