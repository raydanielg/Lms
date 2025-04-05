<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Support\Facades\DB;

class RegionsController extends Controller
{
    //
    public function countries()
    {

        $countries = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
            ->where('type', Region::$country)
            ->get();
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            $countries
        );
    }

    public function provinces($id = null)
    {
        $region_id = $id;
        return $this->region(Region::$province, 'country_id', $region_id);

    }

    public function cities($id = null)
    {
        $region_id = $id;
        return $this->region(Region::$city, 'province_id', $region_id);
    }

    public function districts($id = null)
    {
        $region_id = $id;
        return $this->region(Region::$district, 'city_id', $region_id);

    }

    public function region($type, $super_region_type, $super_region_id)
    {
        $user = apiAuth();
        $region_id = $super_region_id;
        $provinces = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
            ->where('type', $type);

        if ($region_id) {
            //  $provinces = ($user->country_id) ? $provinces->where('country_id', $user->country_id)->get() : [];
            $provinces = $provinces->where($super_region_type, $region_id);
        }
        $provinces = $provinces->get();

        foreach ($provinces as $province) {
            $province->geo_center = \Geo::get_geo_array($province->geo_center);
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            $provinces
        );
    }


}
