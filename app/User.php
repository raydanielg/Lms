<?php

namespace App;

use App\Bitwise\UserLevelOfTraining;
use App\Mixins\RegistrationPackage\UserPackage;
use App\Models\Accounting;
use App\Models\Badge;
use App\Models\BundleWebinar;
use App\Models\ForumTopic;
use App\Models\ForumTopicLike;
use App\Models\ForumTopicPost;
use App\Models\Meeting;
use App\Models\Noticeboard;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\ProductOrder;
use App\Models\QuizzesResult;
use App\Models\Region;
use App\Models\ReserveMeeting;
use App\Models\RewardAccounting;
use App\Models\Role;
use App\Models\Follow;
use App\Models\Sale;
use App\Models\Section;
use App\Models\UserCommission;
use App\Models\Webinar;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    static $active = 'active';
    static $pending = 'pending';
    static $inactive = 'inactive';

    protected $dateFormat = 'U';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $hidden = [
        'password', 'remember_token', 'google_id', 'facebook_id', 'role_id'
    ];

    static $statuses = [
        'active', 'pending', 'inactive'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'level_of_training' => 'integer',
    ];
    private $permissions;
    private $user_group;
    private $userInfo;


    static function getMainAdmin()
    {
        $role = Role::where('name', Role::$admin)->first();

        $admin = self::where('role_name', $role->name)
            ->where('role_id', $role->id)
            ->first();

        return $admin;
    }

    static function getMainAdminId()
    {
        $admin = self::getMainAdmin();

        if (!empty($admin)) {
            return $admin->id;
        }

        return null;
    }

    public function isAdmin()
    {
        return $this->role->is_admin;
    }

    public function isUser()
    {
        return $this->role_name === Role::$user;
    }

    public function isTeacher()
    {
        return $this->role_name === Role::$teacher;
    }

    public function isOrganization()
    {
        return $this->role_name === Role::$organization;
    }

    public function hasPermission($section_name)
    {
        if (!isset($this->permissions)) {
            $sections_id = Permission::where('role_id', '=', $this->role_id)->where('allow', true)->pluck('section_id')->toArray();
            $this->permissions = Section::whereIn('id', $sections_id)->pluck('name')->toArray();
        }

        return in_array($section_name, $this->permissions);
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }

    public function getAvatar($size = 40)
    {
        if (!empty($this->avatar)) {
            $avatarUrl = $this->avatar;
        } else {
            $settings = getOthersPersonalizationSettings();

            if (!empty($settings) and !empty($settings['user_avatar_style']) and $settings['user_avatar_style'] == "ui_avatar") {
                $avatarUrl = "/getDefaultAvatar?item={$this->id}&name={$this->full_name}&size=$size";
            } else {
                if (!empty($settings) and !empty($settings['default_user_avatar'])) {
                    $avatarUrl = $settings['default_user_avatar'];
                } else {
                    $avatarUrl = "/assets/default/img/default/avatar-1.png";
                }
            }
        }

        return $avatarUrl;
    }

    public function getCover()
    {
        if (!empty($this->cover_img)) {
            $path = str_replace('/storage', '', $this->cover_img);

            $imgUrl = url($path);
        } else {
            $imgUrl = getPageBackgroundSettings('user_cover');
        }

        return $imgUrl;
    }

    public function getSignature()
    {
        $path = null;

        if (!empty($this->userMetas)) {
            $meta = $this->userMetas->where('name', 'signature')->first();

            if ($meta) {
                $path = $meta->value;
            }
        }

        if (empty($path)) {
            $path = getPageBackgroundSettings('user_default_signature');
        }

        return $path;
    }

    public function getProfileUrl()
    {
        return '/users/' . $this->id . '/profile';
    }

    public function getLevelOfTrainingAttribute()
    {
        $levels = null;
        $bit = $this->attributes['level_of_training'];

        if (!empty($bit) and is_string($bit)) { // in host with mariaDB
            try {
                $tmp = (int)bin2hex($bit);

                if (is_numeric($tmp) and $tmp > 0 and $tmp <= 7) {
                    $bit = $tmp;
                }
            } catch (\Exception $exception) {

            }
        }

        if (!empty($bit) and is_numeric($bit)) {
            $levels = (new UserLevelOfTraining())->getName($bit);

            if (!empty($levels) and !is_array($levels)) {
                $levels = [$levels];
            }
        }

        return $levels;
    }

    public function getUserGroup()
    {
        if (empty($this->user_group)) {
            if (!empty($this->userGroup) and !empty($this->userGroup->group) and $this->userGroup->group->status == 'active') {
                $this->user_group = $this->userGroup->group;
            }
        }

        return $this->user_group;
    }


    public static function generatePassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function accounting()
    {
        return $this->hasMany(Accounting::class, 'user_id', 'id');
    }

    public function meeting()
    {
        return $this->hasOne('App\Models\Meeting', 'creator_id', 'id');
    }

    public function hasMeeting()
    {
        return Meeting::where('disabled', false)
            ->where('creator_id', $this->id)
            ->first();
    }

    public function ReserveMeetings()
    {
        return $this->hasMany('App\Models\ReserveMeeting', 'user_id', 'id');
    }

    public function affiliateCode()
    {
        return $this->hasOne('App\Models\AffiliateCode', 'user_id', 'id');
    }

    public function affiliates()
    {
        return $this->hasOne('App\Models\Affiliate', 'affiliate_user_id', 'id');
    }

    public function followers()
    {
        return Follow::where('user_id', $this->id)->where('status', Follow::$accepted)->get();
    }

    public function following()
    {
        return Follow::where('follower', $this->id)->where('status', Follow::$accepted)->get();
    }

    public function webinars()
    {
        return $this->hasMany('App\Models\Webinar', 'creator_id', 'id')
            ->orWhere('teacher_id', $this->id);
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'creator_id', 'id');
    }

    public function productOrdersAsBuyer()
    {
        return $this->hasMany('App\Models\ProductOrder', 'buyer_id', 'id');
    }

    public function productOrdersAsSeller()
    {
        return $this->hasMany('App\Models\ProductOrder', 'seller_id', 'id');
    }

    public function forumTopics()
    {
        return $this->hasMany('App\Models\ForumTopic', 'creator_id', 'id');
    }

    public function forumTopicPosts()
    {
        return $this->hasMany('App\Models\ForumTopicPost', 'user_id', 'id');
    }

    public function blog()
    {
        return $this->hasMany('App\Models\Blog', 'author_id', 'id');
    }

    public function selectedBank()
    {
        return $this->hasOne('App\Models\UserSelectedBank', 'user_id', 'id');
    }

    public function installmentOrders()
    {
        return $this->hasMany('App\Models\InstallmentOrder', 'user_id', 'id');
    }

    public function loginHistories()
    {
        return $this->hasMany('App\Models\UserLoginHistory', 'user_id', 'id');
    }

    public function getActiveWebinars($just_count = false)
    {
        $webinars = Webinar::where('status', 'active')
            ->where(function ($query) {
                $query->where('creator_id', $this->id)
                    ->orWhere('teacher_id', $this->id);
            })
            ->orderBy('created_at', 'desc');

        if ($just_count) {
            return $webinars->count();
        }

        return $webinars->get();
    }

    public function getLiveChatJsCode()
    {
        $code = null;

        if (!empty($this->userMetas)) {
            $meta = $this->userMetas->where('name', 'live_chat_js_code')->first();

            if ($meta) {
                $code = $meta->value;
            }
        }

        return $code;
    }

    public function userMetas()
    {
        return $this->hasMany('App\Models\UserMeta');
    }

    public function carts()
    {
        return $this->hasMany('App\Models\Cart', 'creator_id', 'id');
    }

    public function userGroup()
    {
        return $this->belongsTo('App\Models\GroupUser', 'id', 'user_id');
    }

    public function certificates()
    {
        return $this->hasMany('App\Models\Certificate', 'student_id', 'id');
    }

    public function customBadges()
    {
        return $this->hasMany('App\Models\UserBadge', 'user_id', 'id');
    }

    public function supports()
    {
        return $this->hasMany('App\Models\Support', 'user_id', 'id');
    }

    public function occupations()
    {
        return $this->hasMany('App\Models\UserOccupation', 'user_id', 'id');
    }

    public function userRegistrationPackage()
    {
        return $this->hasOne('App\Models\UserRegistrationPackage', 'user_id', 'id');
    }

    public function organization()
    {
        return $this->hasOne($this, 'id', 'organ_id');
    }


    public function getOrganizationTeachers()
    {
        return $this->hasMany($this, 'organ_id', 'id')->where('role_name', Role::$teacher);
    }

    public function getOrganizationStudents()
    {
        return $this->hasMany($this, 'organ_id', 'id')->where('role_name', Role::$user);
    }

    public function zoomApi()
    {
        return $this->hasOne('App\Models\UserZoomApi', 'user_id', 'id');
    }

    public function commissions()
    {
        return $this->hasMany(UserCommission::class, 'user_id', 'id');
    }


    public function rates()
    {
        $webinars = $this->webinars()
            ->where('status', 'active')
            ->get();

        $rate = 0;

        if (!empty($webinars)) {
            $rates = 0;
            $count = 0;

            foreach ($webinars as $webinar) {
                $webinarRate = $webinar->getRate();

                if (!empty($webinarRate) and $webinarRate > 0) {
                    $count += 1;
                    $rates += $webinarRate;
                }
            }

            if ($rates > 0) {
                if ($count < 1) {
                    $count = 1;
                }

                $rate = number_format($rates / $count, 2);
            }
        }

        return $rate;
    }

    public function reviewsCount()
    {
        $webinars = $this->webinars;
        $count = 0;

        if (!empty($webinars)) {
            foreach ($webinars as $webinar) {
                $count += $webinar->reviews->count();
            }
        }

        return $count;
    }

    public function getBadges($customs = true, $getNext = false)
    {
        return Badge::getUserBadges($this, $customs, $getNext);
    }

    public function getCommission()
    {
        $commission = 0;

        $getUserGroup = $this->getUserGroup();
        if (!empty($getUserGroup) and isset($getUserGroup->commission)) {
            $commission = $getUserGroup->commission;
        }

        if (isset($this->commission)) {
            $commission = $this->commission;
        }

        return $commission;
    }

    public function getIncome()
    {
        $totalIncome = Accounting::where('user_id', $this->id)
            ->where('type_account', Accounting::$income)
            ->where('type', Accounting::$addiction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        return $totalIncome;
    }

    public function getPayout()
    {
        $credit = Accounting::where('user_id', $this->id)
            ->where('type_account', Accounting::$income)
            ->where('type', Accounting::$addiction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        $debit = Accounting::where('user_id', $this->id)
            ->where('type_account', Accounting::$income)
            ->where('type', Accounting::$deduction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        return $credit - $debit;
    }

    public function getAccountingCharge()
    {
        $query = Accounting::where('user_id', $this->id)
            ->where('type_account', Accounting::$asset)
            ->where('system', false)
            ->where('tax', false);

        $additions = deepClone($query)->where('type', Accounting::$addiction)
            ->sum('amount');

        $deductions = deepClone($query)->where('type', Accounting::$deduction)
            ->sum('amount');

        $charge = $additions - $deductions;
        return $charge > 0 ? $charge : 0;
    }

    public function getAccountingBalance()
    {
        $additions = Accounting::where('user_id', $this->id)
            ->where('type', Accounting::$addiction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        $deductions = Accounting::where('user_id', $this->id)
            ->where('type', Accounting::$deduction)
            ->where('system', false)
            ->where('tax', false)
            ->sum('amount');

        $balance = $additions - $deductions;
        return $balance > 0 ? $balance : 0;
    }

    public function getRegisteredDays()
    {
        $createdAt = Carbon::parse($this->created_at);
        $currentDate = Carbon::now();

        return $createdAt->diffInDays($currentDate);
    }

    public function getPurchaseAmounts()
    {
        return Sale::where('buyer_id', $this->id)
            ->sum('amount');
    }

    public function getSaleAmounts()
    {
        return Sale::where('seller_id', $this->id)
            ->whereNull('refund_at')
            ->sum('amount');
    }

    public function sales()
    {
        $webinarIds = Webinar::where('creator_id', $this->id)->pluck('id')->toArray();

        return Sale::whereIn('webinar_id', $webinarIds)->sum('amount');
    }

    public function salesCount()
    {
        return Sale::where('seller_id', $this->id)
            ->whereNotNull('webinar_id')
            ->where('type', 'webinar')
            ->whereNull('refund_at')
            ->count();
    }

    public function productsSalesCount()
    {
        return Sale::where('seller_id', $this->id)
            ->whereNotNull('product_order_id')
            ->where('type', 'product')
            ->whereNull('refund_at')
            ->count();
    }

    public function getUnReadNotifications()
    {
        $user = $this;

        $notifications = Notification::where(function ($query) {
            $query->where(function ($query) {
                $query->where('user_id', $this->id)
                    ->where('type', 'single');
            })->orWhere(function ($query) {
                if (!$this->isAdmin()) {
                    $query->whereNull('user_id')
                        ->whereNull('group_id')
                        ->where('type', 'all_users');
                }
            });
        })->doesntHave('notificationStatus')
            ->orderBy('created_at', 'desc')
            ->get();

        $userGroup = $this->userGroup()->first();
        if (!empty($userGroup)) {
            $groupNotifications = Notification::where('group_id', $userGroup->group_id)
                ->where('type', 'group')
                ->doesntHave('notificationStatus')
                ->orderBy('created_at', 'desc')
                ->get();

            if (!empty($groupNotifications) and !$groupNotifications->isEmpty()) {
                $notifications = $notifications->merge($groupNotifications);
            }
        }

        if ($this->isUser()) {
            $studentsNotifications = Notification::whereNull('user_id')
                ->whereNull('group_id')
                ->where('type', 'students')
                ->doesntHave('notificationStatus')
                ->orderBy('created_at', 'desc')
                ->get();
            if (!empty($studentsNotifications) and !$studentsNotifications->isEmpty()) {
                $notifications = $notifications->merge($studentsNotifications);
            }
        }

        if ($this->isTeacher()) {
            $instructorNotifications = Notification::whereNull('user_id')
                ->whereNull('group_id')
                ->where('type', 'instructors')
                ->doesntHave('notificationStatus')
                ->orderBy('created_at', 'desc')
                ->get();
            if (!empty($instructorNotifications) and !$instructorNotifications->isEmpty()) {
                $notifications = $notifications->merge($instructorNotifications);
            }
        }

        if ($this->isOrganization()) {
            $organNotifications = Notification::whereNull('user_id')
                ->whereNull('group_id')
                ->where('type', 'organizations')
                ->doesntHave('notificationStatus')
                ->orderBy('created_at', 'desc')
                ->get();
            if (!empty($organNotifications) and !$organNotifications->isEmpty()) {
                $notifications = $notifications->merge($organNotifications);
            }
        }

        /* Get Course Students Notifications */
        $userBoughtWebinarsIds = $this->getAllPurchasedWebinarsIds();

        if (!empty($userBoughtWebinarsIds)) {
            $courseStudentsNotifications = Notification::whereIn('webinar_id', $userBoughtWebinarsIds)
                ->where('type', 'course_students')
                ->whereDoesntHave('notificationStatus', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            if (!empty($courseStudentsNotifications) and !$courseStudentsNotifications->isEmpty()) {
                $notifications = $notifications->merge($courseStudentsNotifications);
            }
        }

        return $notifications->sortByDesc('created_at');
    }

    public function getAllPurchasedWebinarsIds()
    {
        $userBoughtWebinarsIds = [];
        $userBoughtWebinars = Sale::query()->where('buyer_id', $this->id)
            ->whereNotNull('webinar_id')
            ->whereNull('refund_at')
            ->get();

        foreach ($userBoughtWebinars as $sale) {
            if (!empty($sale->webinar) and $sale->webinar->checkUserHasBought($this)) {
                $userBoughtWebinarsIds[] = $sale->webinar->id;
            }
        }

        return $userBoughtWebinarsIds;
    }

    public function getUnreadNoticeboards()
    {
        $purchasedCoursesIds = $this->getPurchasedCoursesIds();
        $purchasedCoursesInstructorsIds = Webinar::whereIn('id', $purchasedCoursesIds)
            ->pluck('teacher_id')
            ->toArray();

        $noticeboards = Noticeboard::where(function ($query) {
            $query->whereNotNull('organ_id')
                ->where('organ_id', $this->organ_id)
                ->where(function ($query) {
                    if ($this->isOrganization()) {
                        $query->where('type', 'organizations');
                    } else {
                        $type = 'students';

                        if ($this->isTeacher()) {
                            $type = 'instructors';
                        }

                        $query->whereIn('type', ['students_and_instructors', $type]);
                    }
                });
        })->orWhere(function ($query) {
            $type = ['all'];

            if ($this->isUser()) {
                $type = array_merge($type, ['students', 'students_and_instructors']);
            } elseif ($this->isTeacher()) {
                $type = array_merge($type, ['instructors', 'students_and_instructors']);
            } elseif ($this->isOrganization()) {
                $type = array_merge($type, ['organizations']);
            }

            $query->whereNull('organ_id')
                ->whereNull('instructor_id')
                ->whereIn('type', $type);
        })->orWhere(function ($query) use ($purchasedCoursesInstructorsIds) {
            $query->whereNull('webinar_id')
                ->whereIn('instructor_id', $purchasedCoursesInstructorsIds);
        })->orWhere(function ($query) use ($purchasedCoursesIds) {
            $query->whereIn('webinar_id', $purchasedCoursesIds);
        })
            ->orderBy('created_at', 'desc')
            ->get();


        /*
        ->whereDoesntHave('noticeboardStatus', function ($qu) {
            $qu->where('user_id', $this->id);
        })
        */

        return $noticeboards;
    }

    public function getPurchasedCoursesIds()
    {
        $webinarIds = [];
        $bundleIds = [];

        $sales = Sale::where('buyer_id', $this->id)
            ->where(function ($query) {
                $query->whereNotNull('webinar_id');
                $query->orWhereNotNull('bundle_id');
            })
            ->whereNull('refund_at')
            ->get();

        foreach ($sales as $sale) {
            if ($sale->payment_method == Sale::$subscribe) {
                $subscribe = $sale->getUsedSubscribe($sale->buyer_id, $sale->webinar_id);

                if (!empty($subscribe)) {
                    $subscribeSale = Sale::where('buyer_id', $this->id)
                        ->where('type', Sale::$subscribe)
                        ->where('subscribe_id', $subscribe->id)
                        ->whereNull('refund_at')
                        ->latest('created_at')
                        ->first();

                    if (!empty($subscribeSale)) {
                        $usedDays = (int)diffTimestampDay(time(), $subscribeSale->created_at);

                        if ($usedDays <= $subscribe->days) {
                            if (!empty($sale->webinar_id)) {
                                $webinarIds[] = $sale->webinar_id;
                            }

                            if (!empty($sale->bundle_id)) {
                                $bundleIds[] = $sale->bundle_id;
                            }
                        }
                    }
                }
            } else {
                if (!empty($sale->webinar_id)) {
                    $webinarIds[] = $sale->webinar_id;
                }

                if (!empty($sale->bundle_id)) {
                    $bundleIds[] = $sale->bundle_id;
                }
            }
        }

        if (!empty($bundleIds)) {
            $bundleWebinarIds = BundleWebinar::query()->whereIn('bundle_id', $bundleIds)
                ->pluck('webinar_id')
                ->toArray();

            $webinarIds = array_merge($webinarIds, $bundleWebinarIds);
        }

        return array_unique($webinarIds);
    }

    public function getActiveQuizzesResults($group_by_quiz = false, $status = null)
    {
        $query = QuizzesResult::where('user_id', $this->id);

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if ($group_by_quiz) {
            $query->groupBy('quiz_id');
        }

        return $query->get();
    }

    public function getTotalHoursTutoring()
    {
        $seconds = 0;

        if (!empty($this->meeting)) {
            $meetingId = $this->meeting->id;

            $reserves = ReserveMeeting::where('meeting_id', $meetingId)
                ->where('status', 'finished')
                ->get();

            if (!empty($reserves)) {

                foreach ($reserves as $reserve) {
                    $meetingTime = $reserve->meetingTime;

                    if ($meetingTime) {
                        $time = explode('-', $meetingTime->time);

                        $start = strtotime($time[0]);
                        $end = strtotime($time[1]);

                        $seconds = $end - $start;
                    }
                }
            }
        }

        $hours = $seconds ? $seconds / (60 * 60) : 0;

        return round($hours, 0, PHP_ROUND_HALF_UP);
    }

    public function getRewardPoints()
    {
        $credit = RewardAccounting::where('user_id', $this->id)
            ->where('status', RewardAccounting::ADDICTION)
            ->sum('score');

        $debit = RewardAccounting::where('user_id', $this->id)
            ->where('status', RewardAccounting::DEDUCTION)
            ->sum('score');

        return $credit - $debit;
    }

    public function getAddress($full = false)
    {
        $address = null;

        if ($full) {
            $regionIds = [$this->country_id, $this->province_id, $this->city_id, $this->district_id];

            $regions = Region::whereIn('id', $regionIds)->get();

            foreach ($regions as $region) {
                if ($region->id == $this->country_id) {
                    $address .= $region->title;
                } elseif ($region->id == $this->province_id) {
                    $address .= ', ' . $region->title;
                } elseif ($region->id == $this->city_id) {
                    $address .= ', ' . $region->title;
                } elseif ($region->id == $this->district_id) {
                    $address .= ', ' . $region->title;
                }
            }
        }

        if (!empty($address)) {
            $address .= ', ';
        }

        $address .= $this->address;

        return $address;
    }

    public function getWaitingDeliveryProductOrdersCount()
    {
        return ProductOrder::where('seller_id', $this->id)
            ->where('status', ProductOrder::$waitingDelivery)
            ->count();
    }

    public function checkCanAccessToStore()
    {
        $result = (!empty(getStoreSettings('status')) and getStoreSettings('status'));

        if (!$result) {
            $result = $this->can_create_store;
        }

        return $result;
    }

    public function getTopicsPostsCount()
    {
        $topics = ForumTopic::where('creator_id', $this->id)->count();
        $posts = ForumTopicPost::where('user_id', $this->id)->count();

        return $topics + $posts;
    }

    public function getTopicsPostsLikesCount()
    {
        $topicsIds = ForumTopic::where('creator_id', $this->id)->pluck('id')->toArray();
        $postsIds = ForumTopicPost::where('user_id', $this->id)->pluck('id')->toArray();

        $topicsLikes = ForumTopicLike::whereIn('topic_id', $topicsIds)->count();
        $postsLikes = ForumTopicLike::whereIn('topic_post_id', $postsIds)->count();

        return $topicsLikes + $postsLikes;
    }

    public function getCountryAndState()
    {
        $address = null;

        if (!empty($this->country_id)) {
            $country = Region::where('id', $this->country_id)->first();

            if (!empty($country)) {
                $address .= $country->title;
            }
        }

        if (!empty($this->province_id)) {
            $province = Region::where('id', $this->province_id)->first();

            if (!empty($province)) {

                if (!empty($address)) {
                    $address .= '/';
                }

                $address .= $province->title;
            }
        }

        return $address;
    }

    public function getRegionByTypeId($typeId, $justTitle = true)
    {
        $region = !empty($typeId) ? Region::where('id', $typeId)->first() : null;

        if (!empty($region)) {
            return $justTitle ? $region->title : $region;
        }

        return '';
    }

    public function checkAccessToAIContentFeature()
    {
        $access = false;

        if (!empty(getAiContentsSettingsName('status'))) {
            if ($this->isOrganization() and !empty(getAiContentsSettingsName("active_for_organization_panel"))) {
                $access = true;
            }

            if ($this->isTeacher() and !empty(getAiContentsSettingsName("active_for_instructor_panel"))) {
                $access = true;
            }
        }

        if ($this->enable_ai_content) {
            $access = true;
        }

        if (!$access) {
            $userPackage = new UserPackage($this);
            $activePackage = $userPackage->getPackage();

            $access = !!(!empty($activePackage->ai_content_access) and $activePackage->ai_content_access);
        }

        return $access;
    }
}
