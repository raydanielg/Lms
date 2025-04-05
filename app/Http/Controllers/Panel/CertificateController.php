<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mixins\Certificate\MakeCertificate;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Quiz;
use App\Models\QuizzesResult;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Sale;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class CertificateController extends Controller
{

    public function lists(Request $request)
    {
        $this->authorize("panel_certificates_lists");

        $user = auth()->user();

        if (!$user->isUser()) {
            $query = Quiz::where('creator_id', $user->id)
                ->where('status', Quiz::ACTIVE);

            $activeQuizzes = $query->count();

            $quizzesIds = $query->pluck('id')->toArray();

            $achievementsCount = Certificate::whereIn('quiz_id', $quizzesIds)->count();

            $quizResultQuery = QuizzesResult::whereIn('quiz_id', $quizzesIds);
            $failedResults = deepClone($quizResultQuery)->where('status', QuizzesResult::$failed)->count();
            $avgGrade = deepClone($quizResultQuery)->where('status', QuizzesResult::$passed)->avg('user_grade');

            $userAllQuizzes = deepClone($query)->get();

            $query = $this->quizFilters(deepClone($query), $request);

            $quizzes = $query->with([
                'webinar',
                'certificates',
                'quizResults' => function ($query) {
                    $query->orderBy('id', 'desc');
                },
            ])->paginate(10);

            foreach ($quizzes as $quiz) {
                $quizResults = $quiz->quizResults;

                $quiz->avg_grade = $quizResults->where('status', QuizzesResult::$passed)->avg('user_grade');
            }

            $userWebinars = Webinar::select('id')
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })
                ->where('status', 'active')
                ->get();

            $data = [
                'pageTitle' => trans('quiz.certificates_lists'),
                'quizzes' => $quizzes,
                'activeQuizzes' => $activeQuizzes,
                'achievementsCount' => $achievementsCount,
                'avgGrade' => round($avgGrade, 2),
                'failedResults' => $failedResults,
                'userWebinars' => $userWebinars,
                'userAllQuizzes' => $userAllQuizzes,
            ];

            return view('web.default.panel.certificates.list', $data);
        }

        abort(404);
    }

    public function achievements(Request $request)
    {
        $this->authorize("panel_certificates_achievements");

        $user = auth()->user();

        $results = QuizzesResult::where('user_id', $user->id);

        $failedQuizzes = deepClone($results)->where('status', QuizzesResult::$failed)->count();
        $avgGrades = deepClone($results)->where('status', QuizzesResult::$passed)->avg('user_grade');

        if (!empty($request->get('grade'))) {
            $results->where('user_grade', $request->get('grade'));
        }

        $quizzesIds = $results->where('status', QuizzesResult::$passed)
            ->pluck('quiz_id')
            ->toArray();
        $quizzesIds = array_unique($quizzesIds);

        $query = Quiz::query()->whereIn('id', $quizzesIds)
            ->where('status', Quiz::ACTIVE);

        $certificatesCount = deepClone($query)->count();

        $userAllQuizzes = deepClone($query)->get();

        $query = $this->quizFilters(deepClone($query), $request);

        $quizzes = $query
            ->whereHas('quizResults', function ($query) {
                $query->where('status', QuizzesResult::$passed);
            })
            ->with([
                'webinar',
                'quizResults' => function ($query) use ($user) {
                    $query->where('status', QuizzesResult::$passed);
                    $query->where('user_id', $user->id);
                    $query->orderBy('created_at', 'desc');
                },
            ])->paginate(10);


        foreach ($quizzes as $quiz) {
            $canDownloadCertificate = false;

            $userQuizDone = $quiz->quizResults;

            if (count($userQuizDone)) {
                $quiz->result = $userQuizDone->first();

                if ($quiz->result->status == 'passed') {
                    $canDownloadCertificate = true;
                }
            }

            $quiz->can_download_certificate = $canDownloadCertificate;
        }

        $webinarsIds = $user->getPurchasedCoursesIds();
        $userWebinars = Webinar::select('id')
            ->whereIn('id', $webinarsIds)
            ->where('status', 'active')
            ->get();

        $data = [
            'pageTitle' => trans('quiz.my_achievements_lists'),
            'quizzes' => $quizzes,
            'failedQuizzes' => $failedQuizzes,
            'avgGrades' => round($avgGrades, 2),
            'certificatesCount' => $certificatesCount,
            'userWebinars' => $userWebinars,
            'userAllQuizzes' => $userAllQuizzes,
        ];

        return view(getTemplate() . '.panel.certificates.achievements', $data);
    }

    private function quizFilters($query, $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $webinar_id = $request->get('webinar_id');
        $quiz_id = $request->get('quiz_id');
        $grade = $request->get('grade');


        fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($webinar_id)) {
            $query->where('webinar_id', $webinar_id);
        }

        if (!empty($quiz_id)) {
            $query->where('id', $quiz_id);
        }

        return $query;
    }

    public function makeCertificate($quizResultId)
    {
        $user = auth()->user();

        $makeCertificate = new MakeCertificate();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->where('user_id', $user->id)
            ->where('status', QuizzesResult::$passed)
            ->with(['quiz' => function ($query) {
                $query->with(['webinar']);
            }])
            ->first();

        if (!empty($quizResult)) {
            return $makeCertificate->makeQuizCertificate($quizResult);
        }

        abort(404);
    }
}
