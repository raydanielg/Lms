<?php

namespace App\Models\Api;

use App\Models\Api\Meeting;
use App\Models\Region;
use App\Models\UserOccupation;
use App\User as Model;
use App\Models\ReserveMeeting;
use App\Models\Api\Follow;
use App\Models\Role;
use App\Models\Api\Sale;
use App\Models\Api\Subscribe;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements JWTSubject
{
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getBriefAttribute()
    {
        $rate = $this->rates();

        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'role_name' => $this->role_name,
            'bio' => $this->bio,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'offline' => $this->offline,
            'offline_message' => $this->offline_message,
            'verified' => $this->verified,
            'rate' => ($rate > 0) ? (float)$rate : 0,
            'avatar' => url($this->getAvatar()),
            'meeting_status' => $this->meeting_status,
            'user_group' => $this->userGroup->brief ?? null,
            'address' => $this->address,
            'status' => $this->status,
        ];
    }

    public function getDetailsAttribute()
    {
        $details = [
            'organ_id' => $this->organ_id,
            'timezone' => $this->timezone,
            'status' => $this->status,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'language' => $this->language,
            'newsletter' => ($this->newsletter) ? true : false,
            'public_message' => $this->public_message,


            'active_subscription' => Subscribe::getActiveSubscribe($this->id)->details ?? null,
            'headline' => $this->headline,
            'access_content' => $this->access_content,

            'courses_count' => $this->webinars->count(),
            'reviews_count' => $this->reviewsCount(),
            'appointments_count' => $this->appointments()->count(),
            'students_count' => $this->students->count(),
            'followers_count' => $this->followers()->count(),
            'following_count' => $this->following()->count(),
            'badges' => $this->badges,
            'students' => $this->students,
            'followers' => $this->followers()->map(function ($follower) {
                return $follower->userFollower->brief;
            }),
            'following' => $this->following()->map(function ($following) {
                return $following->user->brief;
            }),
            'auth_user_is_follower' => $this->authUserIsFollower,

            'referral' => null,

            'education' => $this->userMetas()->where('name', 'education')->get()->map(function ($meta) {
                return $meta->value;
            }),

            'experience' => $this->userMetas()->where('name', 'experience')->get()->map(function ($meta) {
                return $meta->value;
            }),
            'occupations' => $this->occupations->map(function ($occupation) {
                return $occupation->category->title;
            }),
            'about' => $this->about,

            'webinars' => $this->webinars->map(function ($webinar) {
                return $webinar->brief;
            }),

            'meeting' => ($this->meeting && $this->meeting->meetingTimes->count()) ? $this->meeting->details : null,

            'organization_teachers' => $this->getOrganizationTeachers->map(function ($teacher) {
                return $teacher->brief;
            }),
            'country_id' => $this->country_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,

            /*  'country' => [
                  'id' => $this->country_id,
                  'title' => Region::find($this->country_id)->title??null,
              ],
              'province' =>  [
                  'id' => $this->province_id,
                  'title' => Region::find($this->province_id)->title??null,
              ],

              'city' =>  [
                  'id' => $this->city_id,
                  'title' => Region::find($this->city_id)->title??null,
              ],
              'district_id' =>  [
                  'id' => $this->district_id,
                  'title' => Region::find($this->district_id)->title??null,
              ],*/


        ];

        return array_merge($this->brief, $details, $this->financial);
    }


    public function meetingsSaleAmount()
    {
        return Sale::where('seller_id', $this->id)
            ->whereNotNull('meeting_id')
            ->sum('amount');
    }

    public function classesSaleAmount()
    {
        return Sale::where('seller_id', $this->id)
            ->whereNotNull('webinar_id')
            ->sum('amount');
    }

    public function achievement_certificates($webinar)
    {

        $quiz_id = $webinar->quizzes->pluck('id');

        return QuizzesResult::where('user_id', $this->id)
            ->whereIn('quiz_id', $quiz_id)
            ->where('status', QuizzesResult::$passed)
            ->get()->map(function ($result) {

                return array_merge($result->details,
                    ['certificate' => $result->certificate->brief ?? null]
                );

            });


    }


    public function getFinancialAttribute()
    {
        return [
            'account_type' => $this->type,
            'iban' => $this->iban,
            'account_id' => $this->account_id,
            'identity_scan' => ($this->identity_scan) ? url($this->identity_scan) : null,
            'certificate' => ($this->certificate) ? url($this->certificate) : null,
            'address' => $this->address,

        ];
    }

    public function getAuthUserIsFollowerAttribute()
    {
        $user = apiAuth();
        $authUserIsFollower = false;

        if ($user) {
            $authUserIsFollower = $user->following()->where('follower', $user->id)
                ->where('status', Follow::$accepted)
                ->count();
            if ($authUserIsFollower) {
                return true;
            }
            return false;
        }
        return $authUserIsFollower;
    }

    public function getTotalPointsAttribute()
    {
        return (int)RewardAccounting::where('user_id', $this->id)->where('status', RewardAccounting::ADDICTION)
            ->sum('score');
    }

    public function getSpentPointsAttribute()
    {
        return (int)RewardAccounting::where('user_id', $this->id)->where('status', RewardAccounting::DEDUCTION)
            ->sum('score');

    }

    public function getAvailablePointsAttribute()
    {
        return $this->total_points - $this->spent_points;
    }

    public function getStudentsAttribute()
    {
        return $this->getOrganizationStudents()->get()->map(function ($user) {
                return $user->brief;
            });

        //   ->pluck('buyer_id')
        // ->toArray();

        //   $user->students_count = count(array_unique($studentsIds));
    }

    public function getActiveSubscription()
    {

        return Subscribe::getActiveSubscribe($this->id)->details ?? false;
    }

    public function getHasActiveSubscriptionAttribute()
    {

        return (Subscribe::getActiveSubscribe($this->id)) ? true : false;

    }


    public function getBadgesAttribute()
    {

        return collect($this->getBadges())->map(function ($badges) {
            return [
                'id' => $badges->id,
                'title' => !empty($badges->badge_id) ? $badges->badge->title : $badges->title,
                'type' => $badges->type,
                'condition' => $badges->condition,
                'image' => !empty($badges->badge_id) ? url($badges->badge->image) : url($badges->image),
                'locale' => $badges->locale,
                'description' => !empty($badges->badge_id) ? $badges->badge->description : $badges->description,
                'created_at' => $badges->created_at,

            ];
        });

    }

    public function getMeetingStatusAttribute()
    {
        $meeting = 'no';
        if ($this->meeting) {
            $meeting = 'available';
            if ($this->meeting->disabled) {
                $meeting = 'unavailable';
            }
        }

        return $meeting;
    }

    public function appointments()
    {
        $meetingIds = Meeting::where('creator_id', $this->id)->pluck('id');
        $appointments = ReserveMeeting::whereIn('meeting_id', $meetingIds)
            ->whereNotNull('reserved_at')
            ->where('status', '!=', ReserveMeeting::$canceled)->get();
        return $appointments;

    }

    public function getRoleLabelAttribute()
    {

        /*   @if($cardUser->isUser())
         * {{ trans('quiz.student') }}
         * @elseif($cardUser->isTeacher())
         * {{ trans('public.instructor') }}
         * @elseif($cardUser->isOrganization())
         * {{ trans('home.organization') }}
         * @elseif($cardUser->isAdmin())
         * {{ trans('panel.staff') }}
         * @endif
         */

        if ($this->isUser()) {
            return trans('quiz.student');
        } elseif ($this->isTeacher()) {
            return trans('public.instructor');
        } else {
        }

    }


    public function quizResults()
    {
        return $this->hasMany('App\Models\Api\QuizzesResult', 'user_id');

    }

    public function meeting()
    {
        return $this->hasOne('App\Models\Api\Meeting', 'creator_id', 'id');
    }

    public function webinars()
    {
        return $this->hasMany('App\Models\Api\Webinar', 'creator_id', 'id')
            ->orWhere('teacher_id', $this->id);
    }

    public function userCreatedQuizzes()
    {
        return $this->hasMany('App\Models\Api\Quiz', 'creator_id');


    }

    public function userGroup()
    {
        return $this->belongsTo('App\Models\Api\GroupUser', 'id', 'user_id');
    }

    public function followers()
    {
        return Follow::where('user_id', $this->id)->where('status', Follow::$accepted)->get();
    }

    public function following()
    {
        return Follow::where('follower', $this->id)->where('status', Follow::$accepted)->get();
    }

    public function getOrganizationTeachers()
    {
        return $this->hasMany($this, 'organ_id', 'id')->where('role_name', Role::$teacher);
    }

    public function purchases()
    {
        return $this->hasMany(Sale::class, 'buyer_id');
    }

}
