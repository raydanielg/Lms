<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvertisingBanner;

class AdvertisingBannerController extends Controller
{

    public function list(Request $request)
    {
        $advertisingBanners = AdvertisingBanner::where('published', true)->get()->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'image' => url($banner->image),
                'link' => $banner->link,
                'possion' => $banner->position,
            ];


        });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'count' => $advertisingBanners->count(),
            'advertising_banners' => $advertisingBanners,
        ]);
    }

}
