<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\BlogCategory ;

class BlogCategoryController extends Controller
{
    //

    public function index(){

        $categories=BlogCategory::all()->map(function($category){
            return $category->details ;
        }) ;
   return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),$categories);
    }
}
