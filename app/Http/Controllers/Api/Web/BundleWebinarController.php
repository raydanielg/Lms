<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebinarResource;
use App\Models\Api\Bundle;
use Illuminate\Http\Request;

class BundleWebinarController extends Controller
{
    public function index($id)
    {
        $bundle = Bundle::where('id', $id)->where('status', 'active')->first();
        if (!$bundle) {
            abort(404);
        }
        $webinars = $bundle->bundleWebinars->where('webinar.status', 'active')->map(function ($bundleWebinar) {
            return $bundleWebinar->webinar;
        });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [
                'webinars' => WebinarResource::collection($webinars),

            ]);
    }


}
