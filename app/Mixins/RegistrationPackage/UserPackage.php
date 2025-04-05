<?php

namespace App\Mixins\RegistrationPackage;

use App\Models\GroupRegistrationPackage;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Webinar;

class UserPackage
{
    public $package_id;
    public $instructors_count;
    public $students_count;
    public $courses_capacity;
    public $courses_count;
    public $meeting_count;
    public $product_count;
    public $title;
    public $activation_date;
    public $days_remained;

    private $user;

    public function __construct($user = null)
    {
        if (empty($user)) {
            $user = auth()->user();
        }

        if (empty($user)) {
            $user = apiAuth();
        }

        $this->user = $user;

        $this->title = trans('update.default');
        $this->activation_date = $user->created_at;
    }

    private function make($data = null, $type = null): UserPackage
    {
        $package = new UserPackage();
        $checkAccountRestrictions = $this->checkAccountRestrictions();

        if ($checkAccountRestrictions) {
            $package->instructors_count = (!empty($data) and isset($data->instructors_count)) ? $data->instructors_count : null;
            $package->students_count = (!empty($data) and isset($data->students_count)) ? $data->students_count : null;
            $package->courses_capacity = (!empty($data) and isset($data->courses_capacity)) ? $data->courses_capacity : null;
            $package->courses_count = (!empty($data) and isset($data->courses_count)) ? $data->courses_count : null;
            $package->meeting_count = (!empty($data) and isset($data->meeting_count)) ? $data->meeting_count : null;
            $package->product_count = (!empty($data) and isset($data->product_count)) ? $data->product_count : null;
            $package->ai_content_access = !!(!empty($data) and !empty($data->ai_content_access) and $data->ai_content_access);

            if ($type == 'package') {
                $package->package_id = $data->id;
                $package->title = $data->title;
                $package->activation_date = $data->activation_date;
                $package->days_remained = $data->days_remained;
            }
        }

        return $package;
    }

    private function checkAccountRestrictions(): bool
    {
        if ($this->user->isOrganization()) {
            $settings = getRegistrationPackagesOrganizationsSettings();
        } else {
            $settings = getRegistrationPackagesInstructorsSettings();
        }

        return (!empty($settings) and !empty($settings['status']) and $settings['status']);
    }

    public function getDefaultPackage($role = null): UserPackage
    {
        if ($this->user->isOrganization() or ($role == 'organizations')) {
            $settings = getRegistrationPackagesOrganizationsSettings();
        } else {
            $settings = getRegistrationPackagesInstructorsSettings();
        }

        if (!empty($settings)) {
            $settings = (!empty($settings['status']) and $settings['status']) ? (object)$settings : null;
        }

        return $this->make($settings);
    }

    private function getLastPurchasedPackage()
    {
        $user = $this->user;

        $lastSalePackage = Sale::where('buyer_id', $user->id)
            ->where('type', Sale::$registrationPackage)
            ->whereNotNull('registration_package_id')
            ->whereNull('refund_at')
            ->latest('created_at')
            ->first();

        $package = null;

        if (!empty($lastSalePackage)) {
            $registrationPackage = $lastSalePackage->registrationPackage;

            $countDayOfSale = (int)diffTimestampDay(time(), $lastSalePackage->created_at);

            if ($registrationPackage->days >= $countDayOfSale) {
                $registrationPackage->activation_date = $lastSalePackage->created_at;
                $registrationPackage->days_remained = $registrationPackage->days - $countDayOfSale;

                $package = $registrationPackage;
            } else {
                $registrationPackageExpire = $lastSalePackage->created_at + ($registrationPackage->days * 24 * 60 * 60);

                $notifyOptions = [
                    '[item_title]' => $registrationPackage->title,
                    '[time.date]' => dateTimeFormat($registrationPackageExpire, 'j M Y')
                ];
                sendNotification("registration_package_expired", $notifyOptions, $user->id);
            }
        }

        return $package;
    }

    public function getPackage(): UserPackage
    {
        $user = $this->user;
        $registrationPackage = null;
        $registrationPackageType = null;

        $checkAccountRestrictions = $this->checkAccountRestrictions();

        if ($checkAccountRestrictions) {
            $userRegistrationPackage = $user->userRegistrationPackage()->where('status', 'active')->first();

            if (!empty($userRegistrationPackage)) {
                $registrationPackage = $userRegistrationPackage;
                $registrationPackageType = 'user';
            } else {
                $userGroup = $user->userGroup;
                $groupRegistrationPackage = null;

                if (!empty($userGroup)) {
                    $groupRegistrationPackage = GroupRegistrationPackage::where('group_id', $userGroup->group_id)
                        ->where('status', 'active')
                        ->first();
                }

                if (!empty($groupRegistrationPackage)) {
                    $registrationPackage = $groupRegistrationPackage;
                    $registrationPackageType = 'group';
                } else {
                    $registrationPackage = $this->getLastPurchasedPackage();
                    $registrationPackageType = 'package';
                }
            }
        }

        if ($registrationPackage) {
            return $this->make($registrationPackage, $registrationPackageType);
        }

        return $this->getDefaultPackage();
    }

    /**
     * @param $type => instructors_count, students_count, courses_capacity, courses_count, meeting_count, product_count
     * */
    public function checkPackageLimit($type, $count = null)
    {
        $user = $this->user;
        $package = $this->getPackage();
        $result = false; // no limit
        $usedCount = 0;

        if (!empty($package) and !is_null($package->{$type})) {
            switch ($type) {
                case 'instructors_count':
                    $usedCount = $user->getOrganizationTeachers()->count();
                    break;

                case 'students_count':
                    $usedCount = $user->getOrganizationStudents()->count();
                    break;

                case 'courses_capacity':
                    $usedCount = $count;
                    break;

                case 'courses_count':
                    $usedCount = Webinar::where('creator_id', $user->id)->count();
                    break;

                case 'meeting_count':
                    $userMeeting = $user->meeting;

                    if (!empty($userMeeting)) {
                        $usedCount = $userMeeting->meetingTimes()->count();
                    }
                    break;

                case 'product_count':
                    $usedCount = Product::where('creator_id', $user->id)->count();
                    break;
            }

            if ((is_null($usedCount) and !empty($package->{$type})) or ($usedCount > $package->{$type})) {
                $resultData = [
                    'type' => $type,
                    'currentCount' => $package->{$type}
                ];

                $result = (string)view()->make('web.default.panel.financial.package_limitation_modal', $resultData);
                $result = str_replace(array("\r\n", "\n", "  "), '', $result);
            }
        }

        return $result;
    }
}
