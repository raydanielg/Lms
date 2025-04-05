<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{

    public function index()
    {
        $categories = ProductCategory::whereNull('parent_id')
            ->with([
                'subCategories' => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->get();

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'categories' => ProductCategoryResource::collection($categories)
        ]);
    }
}
