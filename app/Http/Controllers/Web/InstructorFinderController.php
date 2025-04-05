<?php

namespace App\Http\Controllers\Web;

use App\Bitwise\UserLevelOfTraining;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Meeting;
use App\Models\MeetingTime;
use App\Models\Region;
use App\Models\Role;
use App\Models\UserMeta;
use App\Models\UserOccupation;
use App\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InstructorFinderController extends Controller
{

    public function index(Request $request)
    {
        $query = User::where('users.status', 'active')
            ->where(function ($query) {
                $query->where('users.ban', false)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('users.ban_end_at')
                            ->orWhere('users.ban_end_at', '<', time());
                    });
            })
            ->with([
                'meeting' => function ($query) {
                    $query->with('meetingTimes');
                    $query->withCount('meetingTimes');
                },
                'occupations'
            ]);

        $query = $this->handleFilters($query, $request);

        $query = $query->addSelect(DB::raw('ST_AsText(location) as userLocation'));

        $instructors = deepClone($query)->paginate(6);

        foreach ($instructors as $instructor) {
            $instructor->location = $instructor->userLocation;
        }

        if ($request->ajax()) {
            return $this->handleLoadMoreHtml($instructors);
        }

        $mapUsers = $query->whereNotNull('location')->get();

        foreach ($mapUsers as $mapUser) {
            $mapUser->price = $mapUser->meeting ? convertPriceToUserCurrency($mapUser->meeting->amount) : 0;
            $mapUser->avatar = $mapUser->getAvatar();
            $mapUser->rate = $mapUser->rates();
            $mapUser->profileUrl = url($mapUser->getProfileUrl());

            $mapUser->location = \Geo::get_geo_array($mapUser->userLocation);
        }

        $seoSettings = getSeoMetas('instructor_finder');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('home.instructors');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('home.instructors');
        $pageRobot = getPageRobot('instructor_finder');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
            'mapUsers' => $mapUsers,
            'instructors' => $instructors,
        ];

        $locationData = $this->getLocationData($request);
        $data = array_merge($data, $locationData);

        return view('web.default.instructorFinder.index', $data);
    }

    private function handleLoadMoreHtml($instructors)
    {

        $html = null;

        foreach ($instructors as $instructor) {
            $html .= (string)view()->make('web.default.instructorFinder.components.instructor_card', ['instructor' => $instructor]);
        }

        return response()->json([
            'html' => $html,
            'last_page' => $instructors->lastPage(),
        ], 200);
    }

    private function handleFilters($query, Request $request)
    {
        $categoryId = $request->get('category_id', null);
        $levelOfTraining = $request->get('level_of_training', null);
        $gender = $request->get('gender', null);
        $meetingSupport = $request->get('meeting_type', null);
        $population = $request->get('population', null);
        $countryId = $request->get('country_id', null);
        $provinceId = $request->get('province_id', null);
        $cityId = $request->get('city_id', null);
        $districtId = $request->get('district_id', null);
        $sort = $request->get('sort', null);
        $availableForMeetings = $request->get('available_for_meetings', null);
        $hasFreeMeetings = $request->get('free_meetings', null);
        $withDiscount = $request->get('discount', null);

        if (empty($request->get('role', null))) {
            $role = [Role::$organization, Role::$teacher];
        } else {
            $role = [$request->get('role')];
        }

        $query->whereIn('users.role_name', $role);

        if (!empty($categoryId)) {
            $userIds = UserOccupation::where('category_id', $categoryId)->pluck('user_id')->toArray();

            $query->whereIn('users.id', $userIds);
        }

        if (!empty($levelOfTraining) and in_array($levelOfTraining, UserLevelOfTraining::$levelOfTraining)) {
            $levelBit = (new UserLevelOfTraining())->getValue($levelOfTraining);

            $query->whereRaw('users.level_of_training & ? > 0', [$levelBit]);
        }

        if (!empty($gender)) {
            $userIds = UserMeta::where('name', 'gender')
                ->where('value', $gender)
                ->pluck('user_id')
                ->toArray();

            $query->whereIn('users.id', $userIds);
        }

        if (!empty($meetingSupport) and $meetingSupport != 'all') {
            $query->where('users.meeting_type', $meetingSupport);
        }

        if (!empty($population) and in_array($population, ['single', 'group'])) {
            $query->whereHas('meeting', function ($query) use ($population) {
                if ($population == 'single') {
                    $query->where('group_meeting', false);
                } elseif ($population == 'group') {
                    $query->where('group_meeting', true);
                }
            });
        }

        $query = $this->handlePriceFilter($query, $request);


        $query = $this->handleAgeFilter($query, $request);


        if (!empty($countryId)) {
            $query->where('country_id', $countryId);
        }
        if (!empty($provinceId)) {
            $query->where('province_id', $provinceId);
        }
        if (!empty($cityId)) {
            $query->where('city_id', $cityId);
        }
        if (!empty($districtId)) {
            $query->where('district_id', $districtId);
        }

        $query = $this->handleDaysAndTimeFilter($query, $request);

        if (!empty($availableForMeetings) and $availableForMeetings == 'on') {
            $query = $this->handleAvailableForMeetings($query);
        }

        if (!empty($hasFreeMeetings) and $hasFreeMeetings == 'on') {
            $query = $this->handleHasFreeMeetings($query);
        }

        if (!empty($withDiscount) and $withDiscount == 'on') {
            $query = $this->handleWithDiscount($query);
        }

        if (!empty($sort)) {
            if ($sort == 'top_rate') {
                $roleForSort = ($request->get('role') == Role::$organization) ? Role::$organization : Role::$teacher;

                $query = $this->getBestRateUsers($query, $roleForSort);
            }

            if ($sort == 'top_sale') {
                $query = $this->getTopSalesUsers($query);
            }
        } else {
            // order by meetings
            $query->leftJoin('meetings', 'meetings.creator_id', '=', 'users.id')
                ->select('users.*', DB::raw('count(meetings.id) as meetingCounts'))
                ->groupBy('users.id')
                ->orderBy('meetingCounts', 'desc')
                ->orderBy('users.id', 'desc');
        }

        return $query;
    }

    private function getBestRateUsers($query, $role)
    {
        $query->leftJoin('webinars', function ($join) use ($role) {
            if ($role == Role::$organization) {
                $join->on('users.id', '=', 'webinars.creator_id');
            } else {
                $join->on('users.id', '=', 'webinars.teacher_id');
            }

            $join->where('webinars.status', 'active');
        })->leftJoin('webinar_reviews', function ($join) {
            $join->on('webinars.id', '=', 'webinar_reviews.webinar_id');
            $join->where('webinar_reviews.status', 'active');
        })
            ->whereNotNull('rates')
            ->select('users.*', DB::raw('avg(rates) as rates'))
            ->orderBy('rates', 'desc');

        if ($role == Role::$organization) {
            $query->groupBy('webinars.creator_id');
        } else {
            $query->groupBy('webinars.teacher_id');
        }

        return $query;
    }

    private function getTopSalesUsers($query)
    {
        $query->leftJoin('sales', function ($join) {
            $join->on('users.id', '=', 'sales.seller_id')
                ->whereNull('refund_at');
        })
            ->whereNotNull('sales.seller_id')
            ->whereNotNull('sales.meeting_id')
            ->select('users.*', 'sales.seller_id', DB::raw('count(sales.seller_id) as counts'))
            ->groupBy('sales.seller_id')
            ->orderBy('counts', 'desc');

        return $query;
    }

    private function handlePriceFilter($query, Request $request)
    {
        $minPrice = $request->get('min_price', null);
        $maxPrice = $request->get('max_price', null);

        if (!empty($minPrice) or !empty($maxPrice)) {
            $userIds = Meeting::where('disabled', false)
                ->where(function ($query) use ($minPrice, $maxPrice) {
                    if (!empty($minPrice)) {
                        $query->where('amount', '>=', $minPrice);

                        if (!empty($maxPrice)) {
                            $query->where('amount', '<=', $maxPrice);
                        }
                    } else {
                        $query->whereNull('amount');
                        $query->orWhere('amount', '=', '0');
                    }
                })
                ->pluck('creator_id')
                ->toArray();

            $query->whereIn('users.id', $userIds);
        }

        return $query;
    }

    private function handleAgeFilter($query, Request $request)
    {
        $minAge = $request->get('min_age', null);
        $maxAge = $request->get('max_age', null);

        if (!empty($minAge) or !empty($maxAge)) {
            $userAgeQuery = UserMeta::where('name', 'age');

            if (!empty($minAge)) {
                $userAgeQuery->whereRaw('value >= ' . $minAge);
            }

            if (!empty($maxAge)) {
                $userAgeQuery->whereRaw('value <= ' . $maxAge);
            }

            $userIds = $userAgeQuery->pluck('user_id')->toArray();

            $query->whereIn('users.id', $userIds);
        }

        return $query;
    }

    private function handleDaysAndTimeFilter($query, Request $request)
    {
        $days = $request->get('day');
        $minTime = $request->get('min_time');
        $maxTime = $request->get('max_time');

        if (empty($minTime) or $minTime < 0) {
            $minTime = 0;
        }

        if (empty($maxTime) or $maxTime > 23) {
            $maxTime = 23;
        }

        if ($maxTime == 23) {
            $maxTime = '23:59';
        }

        if (isset($minTime) and isset($maxTime)) {

            $minTimeFilter = Carbon::createFromTimeString($minTime);
            $maxTimeFilter = Carbon::createFromTimeString($maxTime);

            $meetingsTimes = null;

            if (!empty($days) and is_array($days)) {
                $meetingsTimes = MeetingTime::whereIn('meeting_times.day_label', $days)
                    ->get();
            } else {
                $meetingsTimes = MeetingTime::query()->get();
            }

            if (!empty($meetingsTimes)) {
                $meetingsIds = [];

                foreach ($meetingsTimes as $meetingsTime) {
                    $time = explode('-', $meetingsTime->time);

                    $startTime = Carbon::createFromTimeString($time[0]);
                    $endTime = Carbon::createFromTimeString($time[1]);

                    if ($minTimeFilter <= $startTime and $maxTimeFilter >= $endTime) {
                        $meetingsIds[] = $meetingsTime->meeting_id;
                    }
                }

                $userIds = Meeting::whereIn('id', $meetingsIds)
                    ->where('disabled', false)
                    ->pluck('creator_id')
                    ->toArray();

                $query->whereIn('users.id', $userIds);
            }
        }

        return $query;
    }

    private function handleAvailableForMeetings($query)
    {
        $hasMeetings = DB::table('meetings')
            ->where('meetings.disabled', 0)
            ->join('meeting_times', 'meetings.id', '=', 'meeting_times.meeting_id')
            ->select('meetings.creator_id', DB::raw('count(meeting_id) as counts'))
            ->groupBy('creator_id')
            ->orderBy('counts', 'desc')
            ->get();

        $hasMeetingsInstructorsIds = [];
        if (!empty($hasMeetings)) {
            $hasMeetingsInstructorsIds = $hasMeetings->pluck('creator_id')->toArray();
        }

        $query->whereIn('users.id', $hasMeetingsInstructorsIds);

        return $query;
    }

    private function handleHasFreeMeetings($query)
    {
        $freeMeetingsIds = Meeting::where('disabled', 0)
            ->where(function ($query) {
                $query->whereNull('amount')->orWhere('amount', '0');
            })->groupBy('creator_id')
            ->pluck('creator_id')
            ->toArray();

        $query->whereIn('users.id', $freeMeetingsIds);

        return $query;
    }

    private function handleWithDiscount($query)
    {
        $withDiscountMeetingsIds = Meeting::where('disabled', 0)
            ->whereNotNull('discount')
            ->groupBy('creator_id')
            ->pluck('creator_id')
            ->toArray();

        $query->whereIn('users.id', $withDiscountMeetingsIds);

        return $query;
    }

    private function getLocationData(Request $request)
    {
        $countries = Region::select(DB::raw('*, ST_AsText(geo_center) as geo_center'))
            ->where('type', Region::$country)
            ->get();

        $provinces = null;
        $cities = null;
        $districts = null;
        $mapCenter = [37.718590, 37.617188]; // default Location
        $mapZoom = 3;

        if ($request->get('country_id')) {
            $provinces = Region::select(DB::raw(' *, ST_AsText(geo_center) as geo_center'))
                ->where('type', Region::$province)
                ->where('country_id', $request->get('country_id'))
                ->get();

            $country = $countries->where('id', $request->get('country_id'))->first();

            if ($country) {
                $mapCenter = \Geo::get_geo_array($country->geo_center);
                $mapZoom = 5;
            }
        }

        if ($request->get('province_id')) {

            if (!empty($provinces)) {
                $province = $provinces->where('id', $request->get('province_id'))->first();

                if ($province) {
                    $mapCenter = \Geo::get_geo_array($province->geo_center);
                    $mapZoom = 7;
                }
            }

            $cities = Region::select(DB::raw(' *, ST_AsText(geo_center) as geo_center'))
                ->where('type', Region::$city)
                ->where('province_id', $request->get('province_id'))
                ->get();
        }

        if ($request->get('city_id')) {

            if (!empty($cities)) {
                $city = $cities->where('id', $request->get('city_id'))->first();

                if ($city) {
                    $mapCenter = \Geo::get_geo_array($city->geo_center);
                    $mapZoom = 12;
                }
            }

            $districts = Region::select(DB::raw(' *, ST_AsText(geo_center) as geo_center'))
                ->where('type', Region::$district)
                ->where('city_id', $request->get('city_id'))
                ->get();
        }


        if (!empty($districts) and $request->get('district_id')) {
            $district = $districts->where('id', $request->get('district_id'))->first();

            if ($district) {
                $mapCenter = \Geo::get_geo_array($district->geo_center);
                $mapZoom = 14;
            }
        }

        return [
            'countries' => $countries,
            'provinces' => $provinces,
            'cities' => $cities,
            'districts' => $districts,
            'mapCenter' => $mapCenter,
            'mapZoom' => $mapZoom,
        ];
    }

    public function wizard(Request $request)
    {
        $step = $request->get('step', 1);

        if ($step > 4) {
            $params = array_filter($request->all());

            $url = '/instructor-finder?' . http_build_query($params);

            return redirect($url);
        }

        $step = $step > 4 ? 4 : ($step < 1 ? 1 : $step);

        $rules = [];

        if ($step == 2) {
            $rules = [
                'category_id' => 'required | integer'
            ];
        }

        if (!empty($rules)) {
            $this->validate($request, $rules);
        }


        $instructorsCount = User::where('role_name', Role::$teacher)
            ->where('status', 'active')
            ->count();

        $organizationsCount = User::where('role_name', Role::$organization)
            ->where('status', 'active')
            ->count();

        $citiesCount = Region::where('type', Region::$city)
            ->count();


        $countries = null;

        if ($step == 2) {
            $countries = Region::select(DB::raw(' *, ST_AsText(geo_center) as geo_center'))
                ->where('type', Region::$country)
                ->get();
        }

        $seoSettings = getSeoMetas('instructor_finder_wizard');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('home . instructors');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('home . instructors');
        $pageRobot = getPageRobot('instructor_finder_wizard');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
            'step' => $step,
            'countries' => $countries,
            'instructorsCount' => $instructorsCount,
            'organizationsCount' => $organizationsCount,
            'citiesCount' => $citiesCount,
        ];

        return view('web.default.instructorFinder.wizard', $data);
    }
}
