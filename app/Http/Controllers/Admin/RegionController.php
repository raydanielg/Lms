<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{

    public function index($pageType)
    {
        $this->authorize('admin_regions_' . $pageType);

        $pageTypes = [
            'countries' => Region::$country,
            'provinces' => Region::$province,
            'cities' => Region::$city,
            'districts' => Region::$district
        ];

        $type = $pageTypes[$pageType];

        $regions = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->with([
                'countryProvinces',
                'provinceCities',
                'province',
                'city',
            ])
            ->paginate(20);

        $data = [
            'pageTitle' => trans('update.' . $pageType),
            'regions' => $regions,
            'type' => $type
        ];

        return view('admin.regions.index', $data);
    }

    public function create(Request $request)
    {
        $this->authorize('admin_regions_create');

        $type = $request->get('type');
        $countries = null;

        if ($type !== Region::$country) {
            $countries = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
                ->where('type', Region::$country)
                ->get();

            foreach ($countries as $country) {
                $country->geo_center = \Geo::get_geo_array($country->geo_center);
            }
        }


        $data = [
            'pageTitle' => trans('update.new_' . $type),
            'countries' => $countries,
            'latitude' => 42.67,
            'longitude' => 12.65,
        ];

        return view('admin.regions.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_regions_create');

        $this->validate($request, [
            'type' => 'required|in:' . implode(',', Region::$types),
            'title' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
            'country_id' => 'required_if:type,province,city,district',
            'province_id' => 'required_if:type,city,district',
            'city_id' => 'required_if:type,district',
        ]);

        $data = $request->all();

        Region::create([
            'country_id' => $data['country_id'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'type' => $data['type'],
            'title' => $data['title'],
            'geo_center' => DB::raw("point(" . $data['latitude'] . "," . $data['longitude'] . ")"),
            'created_at' => time()
        ]);

        $url = getAdminPanelUrl('/regions/');
        if ($data['type'] == Region::$country) {
            $url .= 'countries';
        } else if ($data['type'] == Region::$province) {
            $url .= 'provinces';
        } else if ($data['type'] == Region::$city) {
            $url .= 'cities';
        } else {
            $url .= 'districts';
        }

        return redirect($url);
    }

    public function edit($id)
    {
        $this->authorize('admin_regions_edit');

        $region = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
            ->where('id', $id)
            ->first();

        if ($region) {
            $region->geo_center = \Geo::get_geo_array($region->geo_center);

            $latitude = $region->geo_center[0];
            $longitude = $region->geo_center[1];
            $countries = null;
            $provinces = null;
            $cities = null;

            if ($region->type !== Region::$country) {
                $countries = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
                    ->where('type', Region::$country)
                    ->get();

                foreach ($countries as $country) {
                    $country->geo_center = \Geo::get_geo_array($country->geo_center);
                }
            }

            if ($region->type !== Region::$country and $region->type !== Region::$province) {
                $provinces = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
                    ->where('type', Region::$province)
                    ->where('country_id', $region->country_id)
                    ->get();

                foreach ($provinces as $province) {
                    $province->geo_center = \Geo::get_geo_array($province->geo_center);
                }
            }

            if ($region->type == Region::$district) {
                $cities = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
                    ->where('type', Region::$city)
                    ->where('country_id', $region->country_id)
                    ->get();

                foreach ($cities as $city) {
                    $city->geo_center = \Geo::get_geo_array($city->geo_center);
                }
            }


            $data = [
                'pageTitle' => trans('update.new_country'),
                'region' => $region,
                'countries' => $countries,
                'provinces' => $provinces,
                'cities' => $cities,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ];

            return view('admin.regions.create', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_regions_edit');

        $this->validate($request, [
            'type' => 'required|in:' . implode(',', Region::$types),
            'title' => 'required|string',
            'latitude' => 'required',
            'longitude' => 'required',
            'country_id' => 'required_if:type,province,city,district',
            'province_id' => 'required_if:type,city,district',
            'city_id' => 'required_if:type,district',
        ]);

        $data = $request->all();
        $region = Region::findOrFail($id);

        $region->update([
            'country_id' => $data['country_id'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'type' => $data['type'],
            'title' => $data['title'],
            'geo_center' => DB::raw("point(" . $data['latitude'] . "," . $data['longitude'] . ")"),
            'created_at' => time()
        ]);

        $url = getAdminPanelUrl('/regions/');
        if ($data['type'] == Region::$country) {
            $url .= 'countries';
        } else if ($data['type'] == Region::$province) {
            $url .= 'provinces';
        } else if ($data['type'] == Region::$city) {
            $url .= 'cities';
        } else {
            $url .= 'districts';
        }

        return redirect($url);
    }

    public function delete($id)
    {
        $this->authorize('admin_regions_delete');

        $region = Region::findOrFail($id);

        $region->delete();

        return back();
    }

    public function provincesByCountry($countryId)
    {
        $this->authorize('admin_regions_create');

        $provinces = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
            ->where('type', Region::$province)
            ->where('country_id', $countryId)
            ->get();

        if (!empty($provinces)) {
            foreach ($provinces as $province) {
                $province->geo_center = \Geo::get_geo_array($province->geo_center);
            }
        }

        return response()->json([
            'code' => 200,
            'provinces' => $provinces
        ]);
    }

    public function citiesByProvince($provinceId)
    {
        $this->authorize('admin_regions_create');

        $cities = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
            ->where('type', Region::$city)
            ->where('province_id', $provinceId)
            ->get();

        if (!empty($cities)) {
            foreach ($cities as $city) {
                $city->geo_center = \Geo::get_geo_array($city->geo_center);
            }
        }

        return response()->json([
            'code' => 200,
            'cities' => $cities
        ]);
    }
}
