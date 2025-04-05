<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Mixins\RegistrationPackage\UserPackage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentChannel;
use App\Models\Api\RegistrationPackage;
use App\Models\Product;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class RegistrationPackagesController extends Controller
{
    //
    public function index()
    {
        $user = apiAuth();

        $this->checkAccess($user);

        $role = 'instructors';


        if ($user->isOrganization()) {

            $role = 'organizations';
        }

        $userPackage = new UserPackage($user);

        $activePackage = $userPackage->getPackage();
        $activePackage = [
            'package_id' => $activePackage->package_id,
            'instructors_count' => !is_null($activePackage->instructors_count) ? (int)$activePackage->instructors_count : null,
            'students_count' => !is_null($activePackage->students_count) ? (int)$activePackage->students_count : null,
            'meeting_count' => !is_null($activePackage->meeting_count) ? (int)$activePackage->meeting_count : null,
            'courses_capacity' => !is_null($activePackage->courses_capacity) ? (int)$activePackage->courses_capacity : null,
            'courses_count' => !is_null($activePackage->courses_count) ? (int)$activePackage->courses_count : null,
            'title' => $activePackage->title,
            'activation_date' => $activePackage->activation_date,
            'days_remained' => $activePackage->days_remained ?? 'unlimited',

        ];


        $packages = RegistrationPackage::where('role', $role)
            ->where('status', 'active')
            ->get()->map(function ($package) use ($activePackage) {
                return array_merge($package->details,
                    [
                        'is_active' => ($activePackage and $activePackage['package_id'] == $package->id) ? true : false
                    ]
                );
            });

        /*    {{ $accountStatistics['myStudentsCount'] }}/{{ $activePackage->students_count }}*/

        $accountStatistics = $this->handleAccountStatistics($user);
        $data = [
            'packages' => $packages,
            'active_package' => $activePackage,
            'auth_role' => $user->role_name,
            'account_courses_count' => ($activePackage and $activePackage['courses_count']) ? $accountStatistics['myCoursesCount'] . '/' . $activePackage['courses_count'] : null,
            'account_meeting_count' => ($activePackage and $activePackage['meeting_count']) ? $accountStatistics['myMeetingCount'] . '/' . $activePackage['meeting_count'] : null,
            'account_courses_capacity' => ($activePackage and $activePackage['courses_capacity']) ? $activePackage['courses_capacity'] : null,
            'account_instructors_count' => ($activePackage and $activePackage['instructors_count']) ? $accountStatistics['myInstructorsCount'] . '/' . $activePackage['instructors_count'] : null,
            'account_students_count' => ($activePackage and $activePackage['students_count']) ? $accountStatistics['myStudentsCount'] . '/' . $activePackage['students_count'] : null,

            'account_statistics' => $accountStatistics,
        ];

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            $data
        );

    }

    private function checkAccess($user = null)
    {
        if (empty($user)) {
            $user = apiAuth();
        }

        // or !getRegistrationPackagesGeneralSettings('status')
        if (!($user->isOrganization() or $user->isTeacher())) {
            abort(404);
        }
    }

    private function handleAccountStatistics($user)
    {
        $myInstructorsCount = 0;
        $myStudentsCount = 0;
        if ($user->isOrganization()) {
            $myInstructorsCount = $user->getOrganizationTeachers()->count();
            $myStudentsCount = $user->getOrganizationStudents()->count();
        }

        $myCoursesCount = Webinar::where('creator_id', $user->id)->count();
        $myMeetingCount = !empty($user->meeting) ? $user->meeting->meetingTimes()->count() : 0;
        $myProductCount = Product::where('creator_id', $user->id)->count();

        return [
            'myInstructorsCount' => $myInstructorsCount,
            'myStudentsCount' => $myStudentsCount,
            'myCoursesCount' => $myCoursesCount,
            'myMeetingCount' => $myMeetingCount,
            'myProductCount' => $myProductCount,

        ];
    }

    public function webPayGenerator(Request $request)
    {
        $user = apiAuth();

        validateParam($request->all(), [
            'package_id' => 'required|exists:registration_packages,id'
        ]);


        return apiResponse2(1, 'generated', trans('api.link.generated'),
            [
                'link' => URL::signedRoute('my_api.web.registration_packages', [apiAuth()->id
                    , $request->input('package_id')
                ]),

            ]
        );

    }

    public function webPayRender(Request $request, User $user,$package_id)
    {

        Auth::login($user);

        return view('api.registration_package', compact('package_id'));
    }
}
