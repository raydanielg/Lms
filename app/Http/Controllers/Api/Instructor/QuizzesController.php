<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebinarResource;
use App\Models\Quiz;
use App\Models\Role;
use App\Models\WebinarChapter;
use App\User;
use App\Models\Webinar;
use App\Models\QuizzesResult;
use App\Models\QuizzesQuestion;
use App\Models\QuizzesQuestionsAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizzesController extends Controller
{

    public function store(Request $request)
    {

        $rules = [
            'title' => 'required|string|max:255',
            'webinar_id' => 'nullable|exists:webinars,id',
            'chapter_id' => 'nullable|exists:webinar_chapters,id',
            'pass_mark' => 'required|digits_between:1,10',
            'attempt' => 'digits_between:1,10 ',
            'time' => 'digits_between:1,10',
            'active' => 'boolean',
            'certificate' => 'boolean'
        ];
        $data = $request->all();
        validateParam($request->all(), $rules);

        $user = auth()->user();
        $webinar = null;
        $chapter = null;
        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])
                ->where(function ($query) use ($user) {
                    $query->where('teacher_id', $user->id)
                        ->orWhere('creator_id', $user->id);
                })->first();

            if (!empty($webinar) and !empty($data['chapter_id'])) {
                $chapter = WebinarChapter::where('id', $data['chapter_id'])
                    ->where('webinar_id', $webinar->id)
                    ->first();
            }
        }

        $quiz = Quiz::create([
            'title' => $data['title'],
            'webinar_id' => !empty($webinar) ? $webinar->id : null,
            'chapter_id' => !empty($chapter) ? $chapter->id : null,
            'creator_id' => $user->id,
            'webinar_title' => !empty($webinar) ? $webinar->title : null,
            'attempt' => $data['attempt'] ?? null,
            'pass_mark' => $data['pass_mark'],
            'time' => $data['time'] ?? null,
            'status' => (!empty($data['active']) and $data['active'] == 1) ? Quiz::ACTIVE : Quiz::INACTIVE,
            'certificate' => (!empty($data['certificate']) and $data['certificate'] == 1) ? true : false,
            'created_at' => time(),
        ]);

        return apiResponse2(1, 'created', trans('public.stored'));

    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::find($id);
        if (!$quiz) {
            abort(404);
        }
        $rules = [
            'title' => 'required|string|max:255',
            'webinar_id' => 'nullable|exists:webinars,id',
            'chapter_id' => 'nullable|exists:webinar_chapters,id',
            'pass_mark' => 'required|digits_between:1,10',
            'attempt' => 'digits_between:1,10 ',
            'time' => 'digits_between:1,10',
            'active' => 'boolean',
            'certificate' => 'boolean'
        ];
        validateParam($request->all(), $rules);
        $data = $request->all();
        $user = auth()->user();

        $webinar = null;
        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])
                ->where(function ($query) use ($user) {
                    $query->where('teacher_id', $user->id)
                        ->orWhere('creator_id', $user->id);
                })->first();

            if (!empty($webinar) and !empty($data['chapter_id'])) {
                $chapter = WebinarChapter::where('id', $data['chapter_id'])
                    ->where('webinar_id', $webinar->id)
                    ->first();
            }
        }


        $quiz->update([
            'title' => $data['title'],
            'webinar_id' => !empty($webinar) ? $webinar->id : null,
            'chapter_id' => !empty($chapter) ? $chapter->id : null,
            'webinar_title' => !empty($webinar) ? $webinar->title : null,
            'attempt' => $data['attempt'] ?? null,
            'pass_mark' => $data['pass_mark'],
            'time' => $data['time'] ?? null,
            'status' => (!empty($data['active']) and $data['active'] == 1) ? Quiz::ACTIVE : Quiz::INACTIVE,
            'certificate' => (!empty($data['certificate']) and $data['certificate'] == 1) ? true : false,
            'updated_at' => time(),
        ]);


        return apiResponse2(1, 'updated', trans('public.updated'));
    }

    public function destroy(Request $request, $id)
    {
        $user_id = auth()->id();
        $quiz = Quiz::where('id', $id)
            ->where('creator_id', $user_id)
            ->first();

        if (!$quiz) {
            abort(404);
        }
        $quiz->delete();
        return apiResponse2(1, 'deleted', trans('public.deleted'));
    }


    public function filters(Request $request, $query)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $quiz_id = $request->get('quiz_id');
        $total_mark = $request->get('total_mark');
        $status = $request->get('status');
        $active_quizzes = $request->get('active_quizzes');


        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($quiz_id) and $quiz_id != 'all') {
            $query->where('id', $quiz_id);
        }

        if ($status and $status !== 'all') {
            $query->where('status', strtolower($status));
        }

        if (!empty($active_quizzes)) {
            $query->where('status', 'active');
        }

        if ($total_mark) {
            $query->where('total_mark', '>=', $total_mark);
        }

        return $query;
    }

    public function create()
    {
        $user = auth()->user();
        $webinars = Webinar::where(function ($query) use ($user) {
            $query->where('teacher_id', $user->id)
                ->orWhere('creator_id', $user->id);
        })->get();

        $data = [
            'pageTitle' => trans('quiz.new_quiz_page_title'),
            'webinars' => $webinars,
        ];

        return view(getTemplate() . '.panel.quizzes.create', $data);
    }


    public function edit($id)
    {
        $user = auth()->user();
        $webinars = Webinar::where(function ($query) use ($user) {
            $query->where('teacher_id', $user->id)
                ->orWhere('creator_id', $user->id);
        })->get();

        $quiz = Quiz::where('id', $id)
            ->where('creator_id', $user->id)
            ->with([
                'quizQuestions' => function ($query) {
                    $query->with('quizzesQuestionsAnswers');
                },
            ])->first();

        if (!empty($quiz)) {
            $chapters = collect();

            if (!empty($quiz->webinar)) {
                $chapters = $quiz->webinar->chapters;
            }

            $data = [
                'pageTitle' => trans('public.edit') . ' ' . $quiz->title,
                'webinars' => $webinars,
                'quiz' => $quiz,
                'quizQuestions' => $quiz->quizQuestions,
                'chapters' => $chapters,
            ];

            return view(getTemplate() . '.panel.quizzes.create', $data);
        }

        abort(404);
    }


    public function start(Request $request, $id)
    {
        $quiz = Quiz::where('id', $id)
            ->with([
                'quizQuestions' => function ($query) {
                    $query->with('quizzesQuestionsAnswers');
                },
            ])
            ->first();

        $user = auth()->user();

        if ($quiz) {
            $userQuizDone = QuizzesResult::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->get();

            $status_pass = false;
            foreach ($userQuizDone as $result) {
                if ($result->status == QuizzesResult::$passed) {
                    $status_pass = true;
                }
            }

            if (!isset($quiz->attempt) or ($userQuizDone->count() < $quiz->attempt and !$status_pass)) {
                $newQuizStart = QuizzesResult::create([
                    'quiz_id' => $quiz->id,
                    'user_id' => $user->id,
                    'results' => '',
                    'user_grade' => 0,
                    'status' => 'waiting',
                    'created_at' => time()
                ]);

                $data = [
                    'pageTitle' => trans('quiz.quiz_start'),
                    'quiz' => $quiz,
                    'quizQuestions' => $quiz->quizQuestions,
                    'attempt_count' => $userQuizDone->count() + 1,
                    'newQuizStart' => $newQuizStart
                ];

                return view(getTemplate() . '.panel.quizzes.start', $data);
            } else {
                return back()->with('msg', trans('quiz.cant_start_quiz'));
            }
        }
        abort(404);
    }

    public function quizzesStoreResult(Request $request, $id)
    {
        $user = auth()->user();
        $quiz = Quiz::where('id', $id)->first();

        if ($quiz) {
            $results = $request->get('question');
            $quizResultId = $request->get('quiz_result_id');

            if (!empty($quizResultId)) {

                $quizResult = QuizzesResult::where('id', $quizResultId)
                    ->where('user_id', $user->id)
                    ->first();

                if (!empty($quizResult)) {

                    $passMark = $quiz->pass_mark;
                    $totalMark = 0;
                    $status = '';

                    if (!empty($results)) {
                        foreach ($results as $questionId => $result) {

                            if (!is_array($result)) {
                                unset($results[$questionId]);

                            } else {

                                $question = QuizzesQuestion::where('id', $questionId)
                                    ->where('quiz_id', $quiz->id)
                                    ->first();

                                if ($question and !empty($result['answer'])) {
                                    $answer = QuizzesQuestionsAnswer::where('id', $result['answer'])
                                        ->where('question_id', $question->id)
                                        ->where('creator_id', $quiz->creator_id)
                                        ->first();

                                    $results[$questionId]['status'] = false;
                                    $results[$questionId]['grade'] = $question->grade;

                                    if ($answer and $answer->correct) {
                                        $results[$questionId]['status'] = true;
                                        $totalMark += (int)$question->grade;
                                    }

                                    if ($question->type == 'descriptive') {
                                        $status = 'waiting';
                                    }
                                }
                            }
                        }
                    }

                    if (empty($status)) {
                        $status = ($totalMark >= $passMark) ? QuizzesResult::$passed : QuizzesResult::$failed;
                    }

                    $results["attempt_number"] = $request->get('attempt_number');

                    $quizResult->update([
                        'results' => json_encode($results),
                        'user_grade' => $totalMark,
                        'status' => $status,
                        'created_at' => time()
                    ]);

                    if ($quizResult->status == QuizzesResult::$waiting) {
                        $notifyOptions = [
                            '[c.title]' => $quiz->webinar ? $quiz->webinar->title : '-',
                            '[student.name]' => $user->full_name,
                            '[q.title]' => $quiz->title,
                        ];
                        sendNotification('waiting_quiz', $notifyOptions, $quiz->creator_id);
                    }

                    return redirect()->route('quiz_status', ['quizResultId' => $quizResult]);
                }
            }
        }
        abort(404);
    }

    public function status($quizResultId)
    {
        $user = auth()->user();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->where('user_id', $user->id)
            ->with(['quiz' => function ($query) {
                $query->with(['quizQuestions']);
            }])
            ->first();

        if ($quizResult) {
            $quiz = $quizResult->quiz;
            $attemptCount = $quiz->attempt;

            $userQuizDone = QuizzesResult::where('quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->count();

            $canTryAgain = false;
            if ($userQuizDone < $attemptCount) {
                $canTryAgain = true;
            }

            $data = [
                'pageTitle' => trans('quiz.quiz_status'),
                'quizResult' => $quizResult,
                'quiz' => $quiz,
                'quizQuestions' => $quiz->quizQuestions,
                'attempt_count' => $userQuizDone,
                'canTryAgain' => $canTryAgain,
            ];

            return view(getTemplate() . '.panel.quizzes.status', $data);
        }
        abort(404);
    }

    public function myResults(Request $request)
    {
        $query = QuizzesResult::where('user_id', auth()->user()->id);

        $quizResultsCount = deepClone($query)->count();
        $passedCount = deepClone($query)->where('status', \App\Models\QuizzesResult::$passed)->count();
        $failedCount = deepClone($query)->where('status', \App\Models\QuizzesResult::$failed)->count();
        $waitingCount = deepClone($query)->where('status', \App\Models\QuizzesResult::$waiting)->count();

        $query = $this->resultFilters($request, deepClone($query));

        $quizResults = $query->with([
            'quiz' => function ($query) {
                $query->with(['quizQuestions', 'creator', 'webinar']);
            }
        ])->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($quizResults->groupBy('quiz_id') as $quiz_id => $quizResult) {
            $canTryAgainQuiz = false;

            $result = $quizResult->first();
            $quiz = $result->quiz;

            if (!isset($quiz->attempt) or (count($quizResult) < $quiz->attempt and $result->status !== 'passed')) {
                $canTryAgainQuiz = true;
            }

            foreach ($quizResult as $item) {
                $item->can_try = $canTryAgainQuiz;
                if ($canTryAgainQuiz and isset($quiz->attempt)) {
                    $item->count_can_try = $quiz->attempt - count($quizResult);
                }
            }
        }

        $data = [
            'pageTitle' => trans('quiz.my_results'),
            'quizzesResults' => $quizResults,
            'quizzesResultsCount' => $quizResultsCount,
            'passedCount' => $passedCount,
            'failedCount' => $failedCount,
            'waitingCount' => $waitingCount
        ];

        return view(getTemplate() . '.panel.quizzes.my_results', $data);
    }

    public function opens(Request $request)
    {
        $user = auth()->user();

        $webinarIds = $user->getPurchasedCoursesIds();

        $query = Quiz::whereIn('webinar_id', $webinarIds)
            ->where('status', 'active')
            ->whereDoesntHave('quizResults', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        $query = $this->resultFilters($request, deepClone($query));

        $quizzes = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('quiz.open_quizzes'),
            'quizzes' => $quizzes
        ];

        return view(getTemplate() . '.panel.quizzes.opens', $data);
    }

    public function results(Request $request)
    {
        $user = apiAuth();

        if (!$user->isUser()) {
            $quizzes = Quiz::where('creator_id', $user->id)
                ->where('status', 'active')->with([
                    'webinar','quizQuestions'
                ])
                ->get();

            $quizzesIds = $quizzes->pluck('id')->toArray();

            $query = QuizzesResult::whereIn('quiz_id', $quizzesIds);

            $studentsIds = $query->pluck('user_id')->toArray();
            $allStudents = User::select('id', 'full_name')->whereIn('id', $studentsIds)->get();

            $quizResultsCount = $query->count();
            $quizAvgGrad = round($query->avg('user_grade'), 2);
            $waitingCount = deepClone($query)->where('status', \App\Models\QuizzesResult::$waiting)->count();
            $passedCount = deepClone($query)->where('status', \App\Models\QuizzesResult::$passed)->count();
            $successRate = ($quizResultsCount > 0) ? round($passedCount / $quizResultsCount * 100) : 0;

            $query = $this->resultFilters($request, deepClone($query));

            $quizzesResults = $query->with([
                'quiz' => function ($query) {
                    $query->with(['quizQuestions', 'creator', 'webinar']);
                }, 'user'
            ])->orderBy('created_at', 'desc')
                ->get();

            $quizzesResults = $quizzesResults->toArray();
            foreach($quizzesResults as &$quiz){
                if (isset($quiz["quiz"]["webinar"])){
                    $quiz["quiz"]["webinar"]["thumbnail"] = url($quiz["quiz"]["webinar"]["thumbnail"]);
                    $quiz["quiz"]["webinar"]["image_cover"] = url($quiz["quiz"]["webinar"]["image_cover"]);
                    $quiz["quiz"]["webinar"]["video_demo"] = url($quiz["quiz"]["webinar"]["video_demo"]);
                }
                if (isset($quiz["user"])){
                    $quiz["user"]["avatar"] = url($quiz["user"]["avatar"]);
                    $quiz["user"]["cover_img"] = url($quiz["user"]["cover_img"]);
                    $quiz["user"]["identity_scan"] = url($quiz["user"]["identity_scan"]);
                    $quiz["user"]["certificate"] = url($quiz["user"]["certificate"]);
                }
            }
            $quizzes = $quizzes->toArray();
            foreach($quizzes as &$quiz){
                if (isset($quiz["webinar"])){
                    $quiz["webinar"]["thumbnail"] = url($quiz["webinar"]["thumbnail"]);
                    $quiz["webinar"]["image_cover"] = url($quiz["webinar"]["image_cover"]);
                    $quiz["webinar"]["video_demo"] = url($quiz["webinar"]["video_demo"]);
                }
            }

            $data = [
             //   'pageTitle' => trans('quiz.results'),
                'quizzes_results' => $quizzesResults,
                'quiz_results_count' => $quizResultsCount,
                'success-rate' => $successRate,
                'quiz_avg_grad' => $quizAvgGrad,
                'waiting_count' => $waitingCount,
                'quizzes' => $quizzes,
                'all_students' => $allStudents
            ];
            return apiResponse2(1, 'retrieved', trans('public.retrieved'),$data);

            return view(getTemplate() . '.panel.quizzes.results', $data);
        }

        abort(404);
    }

    public function resultFilters(Request $request, $query)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $quiz_id = $request->get('quiz_id', null);
        $total_mark = $request->get('total_mark', null);
        $status = $request->get('status', null);
        $user_id = $request->get('user_id', null);
        $instructor = $request->get('instructor', null);
        $open_results = $request->get('open_results', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($quiz_id) and $quiz_id != 'all') {
            $query->where('quiz_id', $quiz_id);
        }

        if ($total_mark) {
            $query->where('total_mark', $total_mark);
        }

        if (!empty($user_id) and $user_id != 'all') {
            $query->where('user_id', $user_id);
        }

        if ($instructor) {
            $userIds = User::whereIn('role_name', [Role::$teacher, Role::$organization])
                ->where('full_name', 'like', '%' . $instructor . '%')
                ->pluck('id')->toArray();

            $query->whereIn('creator_id', $userIds);
        }

        if ($status and $status != 'all') {
            $query->where('status', strtolower($status));
        }

        if (!empty($open_results)) {
            $query->where('status', 'waiting');
        }

        return $query;
    }

    public function showResult($quizResultId)
    {
        $user = auth()->user();

        $quizzesIds = Quiz::where('creator_id', $user->id)->pluck('id')->toArray();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->where(function ($query) use ($user, $quizzesIds) {
                $query->where('user_id', $user->id)
                    ->orWhereIn('quiz_id', $quizzesIds);
            })->with([
                'quiz' => function ($query) {
                    $query->with(['quizQuestions', 'webinar']);
                }
            ])->first();

        if (!empty($quizResult)) {
            $numberOfAttempt = QuizzesResult::where('quiz_id', $quizResult->quiz->id)
                ->where('user_id', $quizResult->user_id)
                ->count();

            $data = [
                'pageTitle' => trans('quiz.result'),
                'quizResult' => $quizResult,
                'userAnswers' => json_decode($quizResult->results, true),
                'numberOfAttempt' => $numberOfAttempt,
                'questionsSumGrade' => $quizResult->quiz->quizQuestions->sum('grade'),
            ];

            return view(getTemplate() . '.panel.quizzes.quiz_result', $data);
        }

        abort(404);
    }

    public function destroyQuizResult($quizResultId)
    {
        $user = auth()->user();

        $quizzesIds = Quiz::where('creator_id', $user->id)->pluck('id')->toArray();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->whereIn('quiz_id', $quizzesIds)
            ->first();

        if (!empty($quizResult)) {
            $quizResult->delete();

            return response()->json([
                'code' => 200
            ], 200);
        }

        return response()->json([], 422);
    }

    public function editResult($quizResultId)
    {
        $user = auth()->user();

        $quizzesIds = Quiz::where('creator_id', $user->id)->pluck('id')->toArray();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->whereIn('quiz_id', $quizzesIds)
            ->with([
                'quiz' => function ($query) {
                    $query->with([
                        'quizQuestions' => function ($query) {
                            $query->orderBy('type', 'desc');
                        },
                        'webinar'
                    ]);
                }
            ])->first();

        if (!empty($quizResult)) {
            $numberOfAttempt = QuizzesResult::where('quiz_id', $quizResult->quiz->id)
                ->where('user_id', $quizResult->user_id)
                ->count();

            $data = [
                'pageTitle' => trans('quiz.result'),
                'teacherReviews' => true,
                'quizResult' => $quizResult,
                'newQuizStart' => $quizResult,
                'userAnswers' => json_decode($quizResult->results, true),
                'numberOfAttempt' => $numberOfAttempt,
                'questionsSumGrade' => $quizResult->quiz->quizQuestions->sum('grade'),
            ];

            return view(getTemplate() . '.panel.quizzes.quiz_result', $data);
        }

        abort(404);
    }

    public function updateResult(Request $request, $id)
    {
        $user = auth()->user();
        $quiz = Quiz::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($quiz)) {
            $reviews = $request->get('question');
            $quizResultId = $request->get('quiz_result_id');

            if (!empty($quizResultId)) {

                $quizResult = QuizzesResult::where('id', $quizResultId)
                    ->where('quiz_id', $quiz->id)
                    ->first();

                if (!empty($quizResult)) {

                    $oldResults = json_decode($quizResult->results, true);
                    $totalMark = 0;
                    $status = '';
                    $user_grade = $quizResult->user_grade;

                    if (!empty($oldResults) and count($oldResults)) {
                        foreach ($oldResults as $question_id => $result) {
                            foreach ($reviews as $question_id2 => $review) {
                                if ($question_id2 == $question_id) {
                                    $question = QuizzesQuestion::where('id', $question_id)
                                        ->where('creator_id', $user->id)
                                        ->first();

                                    if ($question->type == 'descriptive') {
                                        if (!empty($result['status']) and $result['status']) {
                                            $user_grade = $user_grade - (isset($result['grade']) ? (int)$result['grade'] : 0);
                                            $user_grade = $user_grade + (isset($review['grade']) ? (int)$review['grade'] : (int)$question->grade);
                                        } else if (isset($result['status']) and !$result['status']) {
                                            $user_grade = $user_grade + (isset($review['grade']) ? (int)$review['grade'] : (int)$question->grade);
                                            $oldResults[$question_id]['grade'] = isset($review['grade']) ? $review['grade'] : $question->grade;
                                        }

                                        $oldResults[$question_id]['status'] = true;
                                    }
                                }
                            }
                        }
                    } elseif (!empty($reviews) and count($reviews)) {
                        foreach ($reviews as $questionId => $review) {

                            if (!is_array($review)) {
                                unset($reviews[$questionId]);
                            } else {
                                $question = QuizzesQuestion::where('id', $questionId)
                                    ->where('quiz_id', $quiz->id)
                                    ->first();

                                if ($question and $question->type == 'descriptive') {
                                    $user_grade += (isset($review['grade']) ? (int)$review['grade'] : 0);
                                }
                            }
                        }

                        $oldResults = $reviews;
                    }

                    $quizResult->user_grade = $user_grade;
                    $passMark = $quiz->pass_mark;

                    if ($quizResult->user_grade >= $passMark) {
                        $quizResult->status = QuizzesResult::$passed;
                    } else {
                        $quizResult->status = QuizzesResult::$failed;
                    }

                    $quizResult->results = json_encode($oldResults);

                    $quizResult->save();

                    $notifyOptions = [
                        '[c.title]' => $quiz->webinar ? $quiz->webinar->title : '-',
                        '[q.title]' => $quiz->title,
                        '[q.result]' => $quizResult->status,
                    ];
                    sendNotification('waiting_quiz_result', $notifyOptions, $quizResult->user_id);

                    return redirect('panel/quizzes/results');
                }
            }
        }

        abort(404);
    }
}
