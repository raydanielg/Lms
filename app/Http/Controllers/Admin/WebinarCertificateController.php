<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mixins\Certificate\MakeCertificate;
use App\Models\Certificate;
use App\Models\Webinar;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class WebinarCertificateController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_course_certificate_list');

        $query = Certificate::query();
        $query->where(function (Builder $query) {
            $query->whereNotNull('webinar_id');
            $query->orWhereNotNull('bundle_id');
        });

        $query = $this->filters($query, $request);

        $certificates = $query->with([
            'webinar',
            'bundle',
            'student',
        ])->orderBy('created_at', 'desc')
            ->paginate(10);


        $data = [
            'pageTitle' => trans('update.competition_certificates'),
            'certificates' => $certificates,
        ];

        $teacher_ids = $request->get('teacher_ids');
        $student_ids = $request->get('student_ids');
        $webinarsIds = $request->get('webinars_ids');

        if (!empty($teacher_ids)) {
            $data['teachers'] = User::select('id', 'full_name')
                ->whereIn('id', $teacher_ids)->get();
        }

        if (!empty($student_ids)) {
            $data['students'] = User::select('id', 'full_name')
                ->whereIn('id', $student_ids)->get();
        }

        if (!empty($webinarsIds)) {
            $data['webinars'] = Webinar::select('id')
                ->whereIn('id', $webinarsIds)->get();
        }

        return view('admin.certificates.course_certificates', $data);
    }

    private function filters($query, $request)
    {
        $filters = $request->all();

        if (!empty($filters['student_ids'])) {
            $query->whereIn('student_id', $filters['student_ids']);
        }

        if (!empty($filters['teacher_ids'])) {
            $webinarsIds = Webinar::where(function ($query) use ($filters) {
                $query->whereIn('creator_id', $filters['teacher_ids']);
                $query->orWhereIn('teacher_id', $filters['teacher_ids']);
            })
                ->pluck('id')->toArray();

            if ($webinarsIds and is_array($webinarsIds)) {
                $query->whereIn('webinar_id', $webinarsIds);
            }
        }

        if (!empty($filters['webinars_ids'])) {
            $query->whereIn('webinar_id', $filters['webinars_ids']);
        }

        return $query;
    }

    public function show($certificateId)
    {
        $this->authorize('admin_course_certificate_list');

        $certificate = Certificate::findOrFail($certificateId);

        if ($certificate->type == 'course') {
            $makeCertificate = new MakeCertificate();

            return $makeCertificate->makeCourseCertificate($certificate);
        }

        abort(404);
    }
}
