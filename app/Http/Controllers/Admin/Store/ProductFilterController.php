<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductFilter;
use Illuminate\Http\Request;

class ProductFilterController extends Controller
{
    public function getByCategoryId($categoryId)
    {
        $defaultLocale = getDefaultLocale();

        $filters = ProductFilter::select('*')
            ->where('category_id', $categoryId)
            ->with([
                'options'  => function ($query) {
                    $query->orderBy('order', 'asc');
                },
            ])
            ->get();

        return response()->json([
            'filters' => $filters,
            'defaultLocale' => mb_strtolower($defaultLocale)
        ], 200);
    }
}
