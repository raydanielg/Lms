<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mixins\Cashback\CashbackRules;
use App\Mixins\Installment\InstallmentPlans;
use App\Models\Category;
use App\Models\Discount;
use App\Models\ForumTopic;
use App\Models\Newsletter;
use App\Models\Product;
use App\Models\ReserveMeeting;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Sale;
use App\Models\UserOccupation;
use App\Models\Webinar;
use App\User;
use App\Models\Role;
use App\Models\Follow;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function profile($id)
    {
        $user = User::where('id', $id)
            //->whereIn('role_name', [Role::$organization, Role::$teacher, Role::$user])
            ->with([
                'blog' => function ($query) {
                    $query->where('status', 'publish');
                    $query->withCount([
                        'comments' => function ($query) {
                            $query->where('status', 'active');
                        }
                    ]);
                },
                'products' => function ($query) {
                    $query->where('status', Product::$active);
                },
                'userMetas'
            ])
            ->first();

        if (!$user) {
            abort(404);
        }

        $userMetas = $user->userMetas;

        if (!empty($userMetas)) {
            foreach ($userMetas as $meta) {
                $user->{$meta->name} = $meta->value;
            }
        }

        $userBadges = $user->getBadges();

        $meeting = Meeting::where('creator_id', $user->id)
            ->with([
                'meetingTimes'
            ])
            ->first();

        $times = [];
        $installments = null;
        $cashbackRules = null;

        if (!empty($meeting) and !empty($meeting->meetingTimes)) {
            $times = convertDayToNumber($meeting->meetingTimes->groupby('day_label')->toArray());

            $authUser = auth()->user();
            // Installments
            /*
            if (getInstallmentsSettings('status') and (empty($authUser) or $authUser->enable_installments)) {
                $installmentPlans = new InstallmentPlans($authUser);
                $installments = $installmentPlans->getPlans('meetings', null, null, null, $user->id);
            }*/

            /* Cashback Rules */
            if (getFeaturesSettings('cashback_active') and (empty($authUser) or !$authUser->disable_cashback)) {
                $cashbackRulesMixin = new CashbackRules($authUser);
                $cashbackRules = $cashbackRulesMixin->getRules('meetings', null, null, null, $user->id);
            }
        }

        $followings = $user->following();
        $followers = $user->followers();

        $authUserIsFollower = false;
        if (auth()->check()) {
            $authUserIsFollower = $followers->where('follower', auth()->id())
                ->where('status', Follow::$accepted)
                ->first();
        }

        $userMetas = $user->userMetas;
        $occupations = $user->occupations()
            ->with([
                'category'
            ])->get();


        $webinars = Webinar::where('status', Webinar::$active)
            ->where('private', false)
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })
            ->orderBy('updated_at', 'desc')
            ->with(['teacher' => function ($qu) {
                $qu->select('id', 'full_name', 'avatar');
            }, 'reviews', 'tickets', 'feature'])
            ->get();

        $meetingIds = Meeting::where('creator_id', $user->id)->pluck('id');
        $appointments = ReserveMeeting::whereIn('meeting_id', $meetingIds)
            ->whereNotNull('reserved_at')
            ->where('status', '!=', ReserveMeeting::$canceled)
            ->count();

        $studentsIds = Sale::whereNull('refund_at')
            ->where('seller_id', $user->id)
            ->whereNotNull('webinar_id')
            ->pluck('buyer_id')
            ->toArray();
        $user->students_count = count(array_unique($studentsIds));

        $instructors = null;
        if ($user->isOrganization()) {
            $instructors = User::where('organ_id', $user->id)
                ->where('role_name', Role::$teacher)
                ->where('status', 'active')
                ->get();
        }

        $instructorDiscounts = null;

        if (!empty(getFeaturesSettings('frontend_coupons_status'))) {
            $instructorDiscounts = Discount::query()
                ->where('creator_id', $user->id)
                ->where(function (Builder $query) {
                    $query->where('source', 'all');
                    $query->orWhere('source', Discount::$discountSourceMeeting);
                })
                ->where('status', 'active')
                ->where('expired_at', '>', time())
                ->get();
        }


        $data = [
            'pageTitle' => $user->full_name . ' ' . trans('public.profile'),
            'user' => $user,
            'userBadges' => $userBadges,
            'meeting' => $meeting,
            'times' => $times,
            'userRates' => $user->rates(),
            'userFollowers' => $followers,
            'userFollowing' => $followings,
            'authUserIsFollower' => $authUserIsFollower,
            'educations' => $userMetas->where('name', 'education'),
            'experiences' => $userMetas->where('name', 'experience'),
            'occupations' => $occupations,
            'webinars' => $webinars,
            'appointments' => $appointments,
            'meetingTimezone' => $meeting ? $meeting->getTimezone() : null,
            'instructors' => $instructors,
            'forumTopics' => $this->getUserForumTopics($user->id),
            'cashbackRules' => $cashbackRules,
            'instructorDiscounts' => $instructorDiscounts
        ];

        return view('web.default.user.profile', $data);
    }

    private function getUserForumTopics($userId)
    {
        $forumTopics = null;

        if (!empty(getFeaturesSettings('forums_status')) and getFeaturesSettings('forums_status')) {
            $forumTopics = ForumTopic::where('creator_id', $userId)
                ->orderBy('pin', 'desc')
                ->orderBy('created_at', 'desc')
                ->withCount([
                    'posts'
                ])
                ->get();

            foreach ($forumTopics as $topic) {
                $topic->lastPost = $topic->posts()->orderBy('created_at', 'desc')->first();
            }
        }

        return $forumTopics;
    }

    public function followToggle($id)
    {
        $authUser = auth()->user();
        $user = User::where('id', $id)->first();

        $followStatus = false;
        $follow = Follow::where('follower', $authUser->id)
            ->where('user_id', $user->id)
            ->first();

        if (empty($follow)) {
            Follow::create([
                'follower' => $authUser->id,
                'user_id' => $user->id,
                'status' => Follow::$accepted,
            ]);

            $followStatus = true;
        } else {
            $follow->delete();
        }

        return response()->json([
            'code' => 200,
            'follow' => $followStatus
        ], 200);
    }

    public function availableTimes(Request $request, $id)
    {
        $timestamp = $request->get('timestamp');
        $dayLabel = $request->get('day_label');
        $date = $request->get('date');

        $user = User::where('id', $id)
            ->whereIn('role_name', [Role::$teacher, Role::$organization])
            ->where('status', 'active')
            ->first();

        if (!$user) {
            abort(404);
        }

        $meeting = Meeting::where('creator_id', $user->id)
            ->with(['meetingTimes'])
            ->first();

        $resultMeetingTimes = [];

        if (!empty($meeting->meetingTimes)) {

            if (empty($dayLabel)) {
                $dayLabel = dateTimeFormat($timestamp, 'l', false, false);
            }

            $dayLabel = mb_strtolower($dayLabel);

            $meetingTimes = $meeting->meetingTimes()->where('day_label', $dayLabel)->get();

            if (!empty($meetingTimes) and count($meetingTimes)) {

                foreach ($meetingTimes as $meetingTime) {
                    $can_reserve = true;

                    $reserveMeeting = ReserveMeeting::where('meeting_time_id', $meetingTime->id)
                        ->where('day', $date)
                        ->whereIn('status', ['pending', 'open'])
                        ->first();

                    if ($reserveMeeting && ($reserveMeeting->locked_at || $reserveMeeting->reserved_at)) {
                        $can_reserve = false;
                    }

                    /*if ($timestamp + $secondTime < time()) {
                        $can_reserve = false;
                    }*/

                    $resultMeetingTimes[] = [
                        "id" => $meetingTime->id,
                        "time" => $meetingTime->time,
                        "description" => $meetingTime->description,
                        "can_reserve" => $can_reserve,
                        'meeting_type' => $meetingTime->meeting_type
                    ];
                }
            }
        }

        return response()->json([
            'times' => $resultMeetingTimes
        ], 200);
    }

    public function instructors(Request $request)
    {
        $seoSettings = getSeoMetas('instructors');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('home.instructors');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('home.instructors');
        $pageRobot = getPageRobot('instructors');

        $data = $this->handleInstructorsOrOrganizationsPage($request, Role::$teacher);

        $data['title'] = trans('home.instructors');
        $data['page'] = 'instructors';
        $data['pageTitle'] = $pageTitle;
        $data['pageDescription'] = $pageDescription;
        $data['pageRobot'] = $pageRobot;

        return view('web.default.pages.instructors', $data);
    }

    public function organizations(Request $request)
    {
        $seoSettings = getSeoMetas('organizations');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('home.organizations');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('home.organizations');
        $pageRobot = getPageRobot('organizations');

        $data = $this->handleInstructorsOrOrganizationsPage($request, Role::$organization);

        $data['title'] = trans('home.organizations');
        $data['page'] = 'organizations';
        $data['pageTitle'] = $pageTitle;
        $data['pageDescription'] = $pageDescription;
        $data['pageRobot'] = $pageRobot;

        return view('web.default.pages.instructors', $data);
    }

    public function handleInstructorsOrOrganizationsPage(Request $request, $role)
    {
        $query = User::where('role_name', $role)
            //->where('verified', true)
            ->where('users.status', 'active')
            ->where(function ($query) {
                $query->where('users.ban', false)
                    ->orWhere(function ($query) {
                        $query->whereNotNull('users.ban_end_at')
                            ->orWhere('users.ban_end_at', '<', time());
                    });
            })
            ->with(['meeting' => function ($query) {
                $query->with('meetingTimes');
                $query->withCount('meetingTimes');
            }]);

        $instructors = $this->filterInstructors($request, deepClone($query), $role)
            ->paginate(6);

        if ($request->ajax()) {
            $html = null;

            foreach ($instructors as $instructor) {
                $html .= '<div class="col-12 col-lg-4">';
                $html .= (string)view()->make('web.default.pages.instructor_card', ['instructor' => $instructor]);
                $html .= '</div>';
            }

            return response()->json([
                'html' => $html,
                'last_page' => $instructors->lastPage(),
            ], 200);
        }

        if (empty($request->get('sort')) or !in_array($request->get('sort'), ['top_rate', 'top_sale'])) {
            $bestRateInstructorsQuery = $this->getBestRateUsers(deepClone($query), $role);

            $bestSalesInstructorsQuery = $this->getTopSalesUsers(deepClone($query), $role);

            $bestRateInstructors = $bestRateInstructorsQuery
                ->limit(8)
                ->get();

            $bestSalesInstructors = $bestSalesInstructorsQuery
                ->limit(8)
                ->get();
        }

        $categories = Category::where('parent_id', null)
            ->with('subCategories')
            ->get();

        $data = [
            'pageTitle' => trans('home.instructors'),
            'instructors' => $instructors,
            'instructorsCount' => deepClone($query)->count(),
            'bestRateInstructors' => $bestRateInstructors ?? null,
            'bestSalesInstructors' => $bestSalesInstructors ?? null,
            'categories' => $categories,
        ];

        return $data;
    }

    private function filterInstructors($request, $query, $role)
    {
        $categories = $request->get('categories', null);
        $sort = $request->get('sort', null);
        $availableForMeetings = $request->get('available_for_meetings', null);
        $hasFreeMeetings = $request->get('free_meetings', null);
        $withDiscount = $request->get('discount', null);
        $search = $request->get('search', null);


        if (!empty($categories) and is_array($categories)) {
            $userIds = UserOccupation::whereIn('category_id', $categories)->pluck('user_id')->toArray();

            $query->whereIn('users.id', $userIds);
        }

        if (!empty($sort) and $sort == 'top_rate') {
            $query = $this->getBestRateUsers($query, $role);
        }

        if (!empty($sort) and $sort == 'top_sale') {
            $query = $this->getTopSalesUsers($query, $role);
        }

        if (!empty($availableForMeetings) and $availableForMeetings == 'on') {
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

        if (!empty($hasFreeMeetings) and $hasFreeMeetings == 'on') {
            $freeMeetingsIds = Meeting::where('disabled', 0)
                ->where(function ($query) {
                    $query->whereNull('amount')->orWhere('amount', '0');
                })->groupBy('creator_id')
                ->pluck('creator_id')
                ->toArray();

            $query->whereIn('users.id', $freeMeetingsIds);
        }

        if (!empty($withDiscount) and $withDiscount == 'on') {
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

    public function makeNewsletter(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'newsletter_email' => 'required|email|max:255|unique:newsletters,email'
        ]);

        if ($validator->fails()) {
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('update.entering_the_email_for_the_newsletter_is_required'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }

        $user_id = null;
        $email = $data['newsletter_email'];

        if (auth()->check()) {
            $user = auth()->user();

            if (empty($user->email)) {
                $user->update([
                    'email' => $email,
                    'newsletter' => true,
                ]);
            } else if ($user->email == $email) {
                $user_id = $user->id;

                $user->update([
                    'newsletter' => true,
                ]);
            }
        }

        $check = Newsletter::where('email', $data['newsletter_email'])->first();

        if (!empty($check)) {
            if (!empty($check->user_id) and !empty($user_id) and $check->user_id != $user_id) {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('update.this_email_used_by_another_user'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
            } elseif (empty($check->user_id) and !empty($user_id)) {
                $check->update([
                    'user_id' => $user_id
                ]);
            }
        } else {
            Newsletter::create([
                'user_id' => $user_id,
                'email' => $data['newsletter_email'],
                'created_at' => time()
            ]);
        }

        if (!empty($user_id)) {
            $newsletterReward = RewardAccounting::calculateScore(Reward::NEWSLETTERS);
            RewardAccounting::makeRewardAccounting($user_id, $newsletterReward, Reward::NEWSLETTERS, $user_id, true);
        }

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('site.create_newsletter_success'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function sendMessage(Request $request, $id)
    {
        if (!empty($id)) {
            $user = User::select('id', 'email')
                ->where('id', $id)
                ->first();

            if (!empty($user) and !empty($user->email)) {
                $data = $request->all();

                $validator = Validator::make($data, [
                    'title' => 'required|string',
                    'email' => 'required|email',
                    'description' => 'required|string',
                    'captcha' => 'required|captcha',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'code' => 422,
                        'errors' => $validator->errors()
                    ], 422);
                }

                $mail = [
                    'title' => $data['title'],
                    'message' => trans('site.you_have_message_from', ['email' => $data['email']]) . "\n" . $data['description'],
                ];

                try {
                    \Mail::to($user->email)->send(new \App\Mail\SendNotifications($mail));

                    return response()->json([
                        'code' => 200
                    ]);
                } catch (Exception $e) {
                    return response()->json([
                        'code' => 500,
                        'message' => trans('site.server_error_try_again')
                    ]);
                }
            }

            return response()->json([
                'code' => 403,
                'message' => trans('site.user_disabled_public_message')
            ]);
        }
    }
}
