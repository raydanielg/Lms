<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\Objects\WebinarObj;
use App\Models\Api\FeatureWebinar;
use Illuminate\Http\Request;

class FeatureWebinarController
{
    public function index(Request $request){

        $webinars=FeatureWebinar::whereIn('page', ['home', 'home_categories'])
        ->where('status', 'publish') 
        ->handleFilters()
        ->get()->map(function ($item) {
            return $item->webinar->brief;
        });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $webinars);

    }
   
    
}
