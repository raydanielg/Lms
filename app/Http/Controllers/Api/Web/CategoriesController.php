<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Api\TrendCategory;
use App\Models\Api\Webinar;
use Illuminate\Http\Request;
use App\Models\Api\Category;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {

        $categories = Category::whereNull('parent_id')->get()
        ->map(function($category){
            return $category->details ;
        }) ;
        ;
         return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),[

            'count'=>$categories->count() ,
            'categories'=>$categories
        ]);

    }

    public function trendCategory()
    {

        $categories = TrendCategory::orderBy('created_at', 'desc')
            ->get()->map(function ($trendCategories) {
                return $trendCategories->details ;
             });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),[
            'count'=>$categories->count() ,
            'categories'=>$categories
        ] );
    }

    public function categoryWebinar(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
             abort(404);
        }
        $webinars = Webinar::where('category_id', $category->id)->handleFilters()->get()
        ->map(function($webinar){

            return $webinar->brief ;
        }) ;


        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'filters' => $category->filters->map(function ($filter) {
                return [
                    'id' => $filter->id,
                    'title' => $filter->title,
                    'options' => $filter->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'title' => $option->title,
                            'order' => $option->order,
                        ];
                    }),
                ];
            }),
            'webinars' => $webinars
        ]);


    }



}
