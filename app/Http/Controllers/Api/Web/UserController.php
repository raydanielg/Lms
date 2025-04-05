<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Mixins\Cashback\CashbackRules;
use App\Models\Api\Meeting;
use App\Models\Newsletter;
use App\Models\Api\ReserveMeeting;
use App\Models\Role;
use App\Models\Sale;
use App\Models\UserOccupation;
use App\Models\Api\Webinar;
use App\Models\Api\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Api\Setting;
use Exception;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function profile(Request $request, $id)
    {
        $user = User::where('id', $id)
            ->whereIn('role_name', [Role::$organization, Role::$teacher, Role::$user])
            ->first();
        if (!$user) {
            abort(404);
        }
        $meeting = \App\Models\Meeting::where('creator_id', $user->id)
            ->with([
                'meetingTimes'
            ])
            ->first();
        $cashbackRules = null;

        if (!empty($meeting) and !empty($meeting->meetingTimes)) {

            $authUser = apiAuth();
            /* Cashback Rules */
            if (getFeaturesSettings('cashback_active') and (empty($authUser) or !$authUser->disable_cashback)) {
                $cashbackRulesMixin = new CashbackRules($authUser);
                $cashbackRules = $cashbackRulesMixin->getRules('meetings', null, null, null, $user->id);
            }
        }
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'user' => $user->details,
            'cashbackRules' => $cashbackRules,
        ]);

    }

    public function instructors(Request $request)
    {
        $providers = $this->handleProviders($request, [Role::$teacher]);

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $providers);

    }

    public function consultations(Request $request)
    {
        $providers = $this->handleProviders($request, [Role::$teacher, Role::$organization], true);
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $providers);


    }

    public function organizations(Request $request)
    {
        $providers = $this->handleProviders($request, [Role::$organization]);

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $providers);


    }

    public function providers(Request $request)
    {
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'instructors' => $this->instructors($request),
            'organizations' => $this->organizations($request),
            'consultations' => $this->consultations($request),
        ]);

    }

    public function handleProviders(Request $request, $role, $has_meeting = false)
    {
        $query = User::whereIn('role_name', $role)
            //->where('verified', true)
            ->where('users.status', 'active')
            ->where(function ($query) {
                $query->where('users.ban', false)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('users.ban_end_at')
                            ->orWhere('users.ban_end_at', '<', time());
                    });
            });

        if ($has_meeting) {
            $query->whereHas('meeting');
        }

        $users = $this->filterProviders($request, deepClone($query), $role)
            ->get()
            ->map(function ($user) {
                return $user->brief;
            });

        return [
            'count' => $users->count(),
            'users' => $users,
        ];

    }

    private function filterProviders($request, $query, $role)
    {
        $categories = $request->get('categories', null);
        $sort = $request->get('sort', null);
        $availableForMeetings = $request->get('available_for_meetings', null);
        $hasFreeMeetings = $request->get('free_meetings', null);
        $withDiscount = $request->get('discount', null);
        $search = $request->get('search', null);
        $organization_id = $request->get('organization', null);
        $downloadable = $request->get('downloadable', null);

        if ($downloadable) {
            $query->whereHas('webinars', function ($qu) {
                return $qu->where('downloadable', 1);
            });
        }
        if (!empty($categories) and is_array($categories)) {
            $userIds = UserOccupation::whereIn('category_id', $categories)->pluck('user_id')->toArray();

            $query->whereIn('users.id', $userIds);
        }
        if ($organization_id) {
            $query->where('organ_id', $organization_id);
        }

        if (!empty($sort) and $sort == 'top_rate') {
            $query = $this->getBestRateUsers($query, $role);
        }

        if (!empty($sort) and $sort == 'top_sale') {
            $query = $this->getTopSalesUsers($query, $role);
        }

        if (!empty($availableForMeetings) and $availableForMeetings == 1) {
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
        }

        if (!empty($hasFreeMeetings) and $hasFreeMeetings == 1) {
            $freeMeetingsIds = Meeting::where('disabled', 0)
                ->where(function ($query) {
                    $query->whereNull('amount')->orWhere('amount', '0');
                })->groupBy('creator_id')
                ->pluck('creator_id')
                ->toArray();

            $query->whereIn('users.id', $freeMeetingsIds);
        }

        if (!empty($withDiscount) and $withDiscount == 1) {
            $withDiscountMeetingsIds = Meeting::where('disabled', 0)
                ->whereNotNull('discount')
                ->groupBy('creator_id')
                ->pluck('creator_id')
                ->toArray();

            $query->whereIn('users.id', $withDiscountMeetingsIds);
        }

        if (!empty($search)) {
            $query->where(function ($qu) use ($search) {
                $qu->where('users.full_name', 'like', "%$search%")
                    ->orWhere('users.email', 'like', "%$search%")
                    ->orWhere('users.mobile', 'like', "%$search%");
            });
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

    private function getTopSalesUsers($query, $role)
    {
        $query->leftJoin('sales', function ($join) {
            $join->on('users.id', '=', 'sales.seller_id')
                ->whereNull('refund_at');
        })
            ->whereNotNull('sales.seller_id')
            ->select('users.*', 'sales.seller_id', DB::raw('count(sales.seller_id) as counts'))
            ->groupBy('sales.seller_id')
            ->orderBy('counts', 'desc');

        return $query;
    }


    public function sendMessage(Request $request, $id)
    {

        $user = User::find($id);
        abort_unless($user, 404);
        if (!$user->public_message) {
            return apiResponse2(0, 'disabled_public_message', trans('api.user.disabled_public_message'));
        }

        validateParam($request->all(), [
            'title' => 'required|string',
            'email' => 'required|email',
            'description' => 'required|string',
            //    'captcha' => 'required|captcha',
        ]);
        $data = $request->all();

        $mail = [
            'title' => $data['title'],
            'message' => trans('site.you_have_message_from', ['email' => $data['email']]) . "\n" . $data['description'],
        ];

        try {
            Mail::to($user->email)->send(new \App\Mail\SendNotifications($mail));


            return apiResponse2(1, 'email_sent', trans('api.user.email_sent'));

        } catch (Exception $e) {

            return apiResponse2(0, 'email_error', $e->getMessage());


        }


    }


    public function makeNewsletter(Request $request)
    {
        validateParam($request->all(), [
            'email' => 'required|string|email|max:255|unique:newsletters,email'
        ]);

        $data = $request->all();
        $user_id = null;
        $email = $data['email'];
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->email == $email) {
                $user_id = $user->id;

                $user->update([
                    'newsletter' => true,
                ]);
            }
        }

        Newsletter::create([
            'user_id' => $user_id,
            'email' => $email,
            'created_at' => time()
        ]);

        return apiResponse2('1', 'subscribed_newsletter', 'email subscribed in newsletter successfully.');


    }


    public function availableTimes(Request $request, $id)
    {
        $date = $request->input('date');

        $day_label = $request->input('day_label');

        $timestamp = strtotime($date);

        //  dd($timestamp);
        $user = User::where('id', $id)
            ->whereIn('role_name', [Role::$teacher, Role::$organization])
            ->where('status', 'active')
            ->first();

        if (!$user) {
            abort(404);
        }

        $meeting = Meeting::where('creator_id', $user->id)->first();

        $meetingTimes = [];

        if (!empty($meeting->meetingTimes)) {
            foreach ($meeting->meetingTimes->groupBy('day_label') as $day => $meetingTime) {

                foreach ($meetingTime as $time) {
                    $can_reserve = true;

                 $explodetime = explode('-', $time->time);

                     $secondTime = dateTimeFormat(strtotime($explodetime['0']), 'H') * 3600 + dateTimeFormat(strtotime($explodetime['0']), 'i') * 60;

                    $reserveMeeting = ReserveMeeting::where('meeting_time_id', $time->id)
                        ->where('day', dateTimeFormat($timestamp, 'Y-m-d'))
                        ->where('meeting_time_id', $time->id)
                        ->first();

                    if ($reserveMeeting && ($reserveMeeting->locked_at || $reserveMeeting->reserved_at)) {
                        $can_reserve = false;
                    }

                        if ($timestamp + $secondTime < time()) {
                           $can_reserve = false;
                       }
                    // $time_explode = explode('-', $time->time);
                    // Carbon::parse($time_explode[0]);

                    $user = apiAuth();
                    $userReservedMeeting = null;
                    if ($user) {
                        $userReservedMeeting = ReserveMeeting::where('user_id', $user->id)
                            ->where('meeting_id', $meeting->id)->where('meeting_time_id',
                                $time->id
                            )
                            ->first();
                    }


                    $meetingTimes[$day]["times"][] =
                        [
                            "id" => $time->id,
                            "time" => $time->time,
                            "can_reserve" => $can_reserve,
                            "description" => $time->description,
                            'meeting_type'=>$time->meeting_type ,
                            'meeting' => $time->meeting->details,
                            'auth_reservation' => $userReservedMeeting

                        ];
                }
            }
        }

        //  return $meetingTimes ;
        $array = [];;
        foreach ($meetingTimes as $day => $time) {
            if ($day == strtolower(date('l', $timestamp))) // if ($day == $day_label) {
            {
                $array = $time['times'];

            }
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'count' => count($array),
            'times' => $array
        ]);

    }


}
