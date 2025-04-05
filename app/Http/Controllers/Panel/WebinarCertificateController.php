<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mixins\Certificate\MakeCertificate;
use App\Models\Bundle;
use App\Models\Certificate;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Sale;
use App\Models\Webinar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class WebinarCertificateController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize("panel_certificates_course_certificates");

        $user = auth()->user();

        $webinars = $this->calculateCoursesCertificates($user);

        $bundles = $this->calculateBundlesCertificates($user);


        $query = Certificate::query()->where('student_id', $user->id);
        $query->where(function (Builder $query) {
            $query->where(function (Builder $query) {
                $query->whereNotNull('webinar_id')
                    ->where('type', 'course');
            });

            $query->orWhere(function (Builder $query) {
                $query->whereNotNull('bundle_id')
                    ->where('type', 'bundle');
            });
        });

        $certificates = $this->handleFilters($query, $request)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.course_certificates'),
            'certificates' => $certificates,
            'userWebinars' => $webinars,
        ];

        return view('web.default.panel.certificates.webinar_certificates', $data);
    }

    private function handleFilters($query, $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $webinar_id = $request->get('webinar_id');


        fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($webinar_id)) {
            $query->where('webinar_id', $webinar_id);
        }

        return $query;
    }

    private function calculateCoursesCertificates($user)
    {
        $webinars = Webinar::where('status', 'active')
            ->where('certificate', true)
            ->whereHas('sales', function ($query) use ($user) {
                $query->where('buyer_id', $user->id);
                $query->whereNull('refund_at');
                $query->where('access_to_purchased_item', true);
            })
            ->get();

        foreach ($webinars as $webinar) {
            $webinar->makeCertificateForUser($user);
        }

        return $webinars;
    }

    private function calculateBundlesCertificates($user)
    {
        $bundles = Bundle::query()->where('status', 'active')
            ->where('certificate', true)
            ->whereHas('sales', function ($query) use ($user) {
                $query->where('buyer_id', $user->id);
                $query->whereNull('refund_at');
                $query->where('access_to_purchased_item', true);
            })
            ->get();

        foreach ($bundles as $bundle) {
            if (count($bundle->bundleWebinars)) {
                $allCoursesPass = true;

                foreach ($bundle->bundleWebinars as $bundleWebinar) {
                    $webinar = $bundleWebinar->webinar;

                    if ($webinar->getProgress(true) < 100) {
                        //$allCoursesPass = false;
                    }
                }

                if ($allCoursesPass) {
                    $bundle->makeCertificateForUser($user);
                }
            }
        }

        return $bundles;
    }

    public function showCourseCertificate($certificateId)
    {
        $user = auth()->user();

        $certificate = Certificate::where('id', $certificateId)
            ->where('student_id', $user->id)
            ->whereNotNull('webinar_id')
            ->first();

        if (!empty($certificate)) {
            $makeCertificate = new MakeCertificate();

            return $makeCertificate->makeCourseCertificate($certificate);
        }

        abort(404);
    }

    public function showBundleCertificate($certificateId)
    {
        $user = auth()->user();

        $certificate = Certificate::where('id', $certificateId)
            ->where('student_id', $user->id)
            ->whereNotNull('bundle_id')
            ->first();

        if (!empty($certificate)) {
            $makeCertificate = new MakeCertificate();

            return $makeCertificate->makeBundleCertificate($certificate);
        }

        abort(404);
    }
}
