<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Role;
use App\Models\Translation\QuizTranslation;
use App\Models\WebinarChapter;
use App\Models\WebinarChapterItem;
use App\User;
use App\Models\Webinar;
use App\Models\QuizzesResult;
use App\Models\QuizzesQuestion;
use App\Models\QuizzesQuestionsAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize("panel_quizzes_lists");

        $user = auth()->user();

        $allQuizzesLists = Quiz::select('id', 'webinar_id')
            ->where('creator_id', $user->id)
            ->where('status', 'active')
            ->get();


        $query = Quiz::where('creator_id', $user->id);

        $quizzesCount = deepClone($query)->count();

        $quizFilters = $this->filters($request, $query);

        $quizzes = $quizFilters->with([
            'webinar',
            'quizQuestions',
            'quizResults',
        ])->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $userSuccessRate = [];
        $questionsCount = 0;
        $userCount = 0;

        foreach ($quizzes as $quiz) {

            $countSuccess = $quiz->quizResults
                ->where('status', \App\Models\QuizzesResult::$passed)
                ->pluck('user_id')
                ->count();

            $rate = 0;
            if ($countSuccess) {
                $rate = round($countSuccess / $quiz->quizResults->count() * 100);
            }

            $quiz->userSuccessRate = $rate;

            $questionsCount += $quiz->quizQuestions->count();
            $userCount += $quiz->quizResults
                ->pluck('user_id')
                ->count();
        }

        $data = [
            'pageTitle' => trans('quiz.quizzes_list_page_title'),
            'quizzes' => $quizzes,
            'userSuccessRate' => $userSuccessRate,
            'questionsCount' => $questionsCount,
            'quizzesCount' => $quizzesCount,
            'userCount' => $userCount,
            'allQuizzesLists' => $allQuizzesLists
        ];

        return view(getTemplate() . '.panel.quizzes.lists', $data);
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

    public function create(Request $request)
    {
        $this->authorize("panel_quizzes_create");

        $user = auth()->user();
        $webinars = Webinar::where(function ($query) use ($user) {
            $query->where('teacher_id', $user->id)
                ->orWhere('creator_id', $user->id)
                ->orWhereHas('webinarPartnerTeacher', function ($query) use ($user) {
                    $query->where('teacher_id', $user->id);
                });
        })->get();

        $locale = $request->get('locale', app()->getLocale());

        $data = [
            'pageTitle' => trans('quiz.new_quiz_page_title'),
            'webinars' => $webinars,
            'userLanguages' => getUserLanguagesLists(),
            'locale' => mb_strtolower($locale),
            'defaultLocale' => getDefaultLocale(),
        ];

        return view(getTemplate() . '.panel.quizzes.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("panel_quizzes_create");

        $data = $request->get('ajax')['new'];
        $locale = $data['locale'] ?? getDefaultLocale();

        $rules = [
            'title' => 'required|max:255',
            'webinar_id' => 'nullable',
            'pass_mark' => 'required',
        ];

        $validate = Validator::make($data, $rules);

        if ($validate->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validate->errors()
            ], 422);
        }

        $user = auth()->user();

        $webinar = null;
        $chapter = null;
        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])
                ->where(function ($query) use ($user) {
                    $query->where('teacher_id', $user->id)
                        ->orWhere('creator_id', $user->id)
                        ->orWhereHas('webinarPartnerTeacher', function ($query) use ($user) {
                            $query->where('teacher_id', $user->id);
                        });
                })->first();

            if (!empty($webinar) and !empty($data['chapter_id'])) {
                $chapter = WebinarChapter::where('id', $data['chapter_id'])
                    ->where('webinar_id', $webinar->id)
                    ->first();
            }
        }

        $quiz = Quiz::create([
            'webinar_id' => !empty($webinar) ? $webinar->id : null,
            'chapter_id' => !empty($chapter) ? $chapter->id : null,
            'creator_id' => $user->id,
            'attempt' => $data['attempt'] ?? null,
            'pass_mark' => $data['pass_mark'],
            'time' => $data['time'] ?? null,
            'status' => (!empty($data['status']) and $data['status'] == 'on') ? Quiz::ACTIVE : Quiz::INACTIVE,
            'certificate' => (!empty($data['certificate']) and $data['certificate'] == 'on'),
            'display_questions_randomly' => (!empty($data['display_questions_randomly']) and $data['display_questions_randomly'] == 'on'),
            'expiry_days' => (!empty($data['expiry_days']) and $data['expiry_days'] > 0) ? $data['expiry_days'] : null,
            'created_at' => time(),
        ]);

        if (!empty($quiz)) {
            QuizTranslation::updateOrCreate([
                'quiz_id' => $quiz->id,
                'locale' => mb_strtolower($locale),
            ], [
                'title' => $data['title'],
            ]);

            if (!empty($quiz->chapter_id)) {
                WebinarChapterItem::makeItem($quiz->creator_id, $quiz->chapter_id, $quiz->id, WebinarChapterItem::$chapterQuiz);
            }
        }

        // Send Notification To All Students
        if (!empty($webinar)) {
            $webinar->sendNotificationToAllStudentsForNewQuizPublished($quiz);
        }

        if ($request->ajax()) {

            $redirectUrl = '';

            if (empty($data['is_webinar_page'])) {
                $redirectUrl = '/panel/quizzes/' . $quiz->id . '/edit';
            }

            return response()->json([
                'code' => 200,
                'redirect_url' => $redirectUrl
            ]);
        } else {
            return redirect()->route('panel_edit_quiz', ['id' => $quiz->id]);
        }
    }

    public function edit(Request $request, $id)
    {
        $this->authorize("panel_quizzes_create");

        $user = auth()->user();
        $webinars = Webinar::where(function ($query) use ($user) {
            $query->where('teacher_id', $user->id)
                ->orWhere('creator_id', $user->id)
                ->orWhereHas('webinarPartnerTeacher', function ($query) use ($user) {
                    $query->where('teacher_id', $user->id);
                });
        })->get();

        $webinarIds = $webinars->pluck('id')->toArray();

        $quiz = Quiz::where('id', $id)
            ->where('creator_id', $user->id)
            ->where(function ($query) use ($user, $webinarIds) {
                $query->where('creator_id', $user->id);
                $query->orWhereIn('webinar_id', $webinarIds);
            })
            ->with([
                'quizQuestions' => function ($query) {
                    $query->orderBy('order', 'asc');
                    $query->with('quizzesQuestionsAnswers');
                },
            ])->first();

        if (!empty($quiz)) {
            $chapters = collect();

            if (!empty($quiz->webinar)) {
                $chapters = $quiz->webinar->chapters;
            }

            $locale = $request->get('locale', app()->getLocale());

            $data = [
                'pageTitle' => trans('public.edit') . ' ' . $quiz->title,
                'webinars' => $webinars,
                'quiz' => $quiz,
                'quizQuestions' => $quiz->quizQuestions,
                'chapters' => $chapters,
                'userLanguages' => getUserLanguagesLists(),
                'locale' => mb_strtolower($locale),
                'defaultLocale' => getDefaultLocale(),
            ];

            return view(getTemplate() . '.panel.quizzes.create', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("panel_quizzes_create");

        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $webinar = null;
        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])
                ->where(function ($query) use ($user) {
                    $query->where('teacher_id', $user->id)
                        ->orWhere('creator_id', $user->id)
                        ->orWhereHas('webinarPartnerTeacher', function ($query) use ($user) {
                            $query->where('teacher_id', $user->id);
                        });
                })->first();
        }


        $quiz = Quiz::query()->where('id', $id)
            ->where(function ($query) use ($user, $webinar) {
                $query->where('creator_id', $user->id);

                if (!empty($webinar)) {
                    $query->orWhere('webinar_id', $webinar->id);
                }
            })
            ->first();

        if (!empty($quiz)) {
            $quizQuestionsCount = $quiz->quizQuestions->count();

            $locale = $data['locale'] ?? getDefaultLocale();

            $rules = [
                'title' => 'required|max:255',
                'webinar_id' => 'nullable',
                'pass_mark' => 'required',
                'display_number_of_questions' => 'required_if:display_limited_questions,on|nullable|between:1,' . $quizQuestionsCount
            ];

            $validate = Validator::make($data, $rules);

            if ($validate->fails()) {
                return response()->json([
                    'code' => 422,
                    'errors' => $validate->errors()
                ], 422);
            }


            if (!empty($webinar) and !empty($data['chapter_id'])) {
                $chapter = WebinarChapter::where('id', $data['chapter_id'])
                    ->where('webinar_id', $webinar->id)
                    ->first();
            }

            $quiz->update([
                'webinar_id' => !empty($webinar) ? $webinar->id : null,
                'chapter_id' => !empty($chapter) ? $chapter->id : null,
                'attempt' => $data['attempt'] ?? null,
                'pass_mark' => $data['pass_mark'],
                'time' => $data['time'] ?? null,
                'status' => (!empty($data['status']) and $data['status'] == 'on') ? Quiz::ACTIVE : Quiz::INACTIVE,
                'certificate' => (!empty($data['certificate']) and $data['certificate'] == 'on'),
                'display_limited_questions' => (!empty($data['display_limited_questions']) and $data['display_limited_questions'] == 'on'),
                'display_number_of_questions' => (!empty($data['display_limited_questions']) and $data['display_limited_questions'] == 'on' and !empty($data['display_number_of_questions'])) ? $data['display_number_of_questions'] : null,
                'display_questions_randomly' => (!empty($data['display_questions_randomly']) and $data['display_questions_randomly'] == 'on'),
                'expiry_days' => (!empty($data['expiry_days']) and $data['expiry_days'] > 0) ? $data['expiry_days'] : null,
                'updated_at' => time(),
            ]);


            $checkChapterItem = WebinarChapterItem::query()
                ->where('item_id', $quiz->id)
                ->where('type', WebinarChapterItem::$chapterQuiz)
                ->first();

            if (!empty($quiz->chapter_id)) {
                if (empty($checkChapterItem)) {
                    WebinarChapterItem::makeItem($quiz->creator_id, $quiz->chapter_id, $quiz->id, WebinarChapterItem::$chapterQuiz);
                } elseif ($checkChapterItem->chapter_id != $quiz->chapter_id) {
                    $checkChapterItem->delete(); // remove quiz from old chapter and assign it to new chapter

                    WebinarChapterItem::makeItem($quiz->creator_id, $quiz->chapter_id, $quiz->id, WebinarChapterItem::$chapterQuiz);
                }
            } else if (!empty($checkChapterItem)) {
                $checkChapterItem->delete();
            }

            QuizTranslation::updateOrCreate([
                'quiz_id' => $quiz->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'code' => 200
                ]);
            } else {
                return redirect('panel/quizzes');
            }
        }

        abort(404);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize("panel_quizzes_delete");

        $user = auth()->user();
        $quiz = Quiz::where('id', $id)
            ->first();

        if (!empty($quiz)) {

            $webinar = null;
            if (!empty($quiz->webinar_id)) {
                $webinar = Webinar::query()->find($quiz->webinar_id);
            }

            if ($quiz->creator_id == $user->id or (!empty($webinar) and $webinar->canAccess($user))) {
                if ($quiz->delete()) {
                    $checkChapterItem = WebinarChapterItem::where('user_id', $user->id)
                        ->where('item_id', $id)
                        ->where('type', WebinarChapterItem::$chapterQuiz)
                        ->first();

                    if (!empty($checkChapterItem)) {
                        $checkChapterItem->delete();
                    }

                    return response()->json([
                        'code' => 200
                    ], 200);
                }
            }
        }

        return response()->json([], 422);
    }

    public function start(Request $request, $id)
    {
        $quiz = Quiz::where('id', $id)->first();

        $user = auth()->user();

        if ($quiz) {

            if (!empty($quiz->webinar_id)) {
                $webinar = $quiz->webinar;

                $checkUserHasBought = $webinar->checkUserHasBought($user);

                if (!$checkUserHasBought) {
                    $toastData = [
                        'title' => trans('public.request_failed'),
                        'msg' => trans('cart.you_not_purchased_this_course'),
                        'status' => 'error'
                    ];
                    return back()->with(['toast' => $toastData]);
                }

                if (!empty($quiz->expiry_days)) {
                    $hasAccess = false;
                    $sale = $webinar->getSaleItem($user);

                    if (!empty($sale)) {
                        $purchaseDate = $sale->created_at;
                        $gift = $sale->gift;

                        if (!empty($gift) and !empty($gift->date)) {
                            $purchaseDate = $gift->date;
                        }

                        $time = time();
                        $hasAccess = (strtotime("+{$quiz->expiry_days} days", $purchaseDate) > $time);
                    }

                    if (!$hasAccess) {
                        $toastData = [
                            'title' => trans('public.request_failed'),
                            'msg' => trans('update.your_access_to_this_quiz_has_been_expired'),
                            'status' => 'error'
                        ];
                        return back()->with(['toast' => $toastData]);
                    }
                }
            }

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

                $quizQuestionsQuery = QuizzesQuestion::query()
                    ->where('quiz_id', $quiz->id)
                    ->with('quizzesQuestionsAnswers');

                if ($quiz->display_questions_randomly) {
                    $quizQuestionsQuery->inRandomOrder();
                } else {
                    $quizQuestionsQuery->orderBy('order', 'asc');
                }

                if (($quiz->display_limited_questions and !empty($quiz->display_number_of_questions))) {
                    $totalQuestionsCount = $quiz->display_number_of_questions;

                    $quizQuestions = $quizQuestionsQuery->take($totalQuestionsCount)->get();
                } else {
                    $quizQuestions = $quizQuestionsQuery->get();
                    $totalQuestionsCount = $quizQuestions->count();
                }

                $data = [
                    'pageTitle' => trans('quiz.quiz_start'),
                    'quiz' => $quiz,
                    'quizQuestions' => $quizQuestions,
                    'attempt_count' => $userQuizDone->count() + 1,
                    'newQuizStart' => $newQuizStart,
                    'totalQuestionsCount' => $totalQuestionsCount
                ];

                return view(getTemplate() . '.panel.quizzes.start', $data);
            } else {
                $toastData = [
                    'title' => trans('public.request_failed'),
                    'msg' => trans('quiz.cant_start_quiz'),
                    'status' => 'error'
                ];
                return back()->with(['toast' => $toastData]);
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

                    if ($quizResult->status == QuizzesResult::$passed) {
                        $passTheQuizReward = RewardAccounting::calculateScore(Reward::PASS_THE_QUIZ);
                        RewardAccounting::makeRewardAccounting($quizResult->user_id, $passTheQuizReward, Reward::PASS_THE_QUIZ, $quiz->id, true);

                        if ($quiz->certificate) {
                            $certificateReward = RewardAccounting::calculateScore(Reward::CERTIFICATE);
                            RewardAccounting::makeRewardAccounting($quizResult->user_id, $certificateReward, Reward::CERTIFICATE, $quiz->id, true);
                        }
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

            $quizQuestions = $quizResult->getQuestions();
            $totalQuestionsCount = $quizQuestions->count();

            $data = [
                'pageTitle' => trans('quiz.quiz_status'),
                'quizResult' => $quizResult,
                'quiz' => $quiz,
                'quizQuestions' => $quizQuestions,
                'attempt_count' => $userQuizDone,
                'canTryAgain' => $canTryAgain,
                'totalQuestionsCount' => $totalQuestionsCount
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
        $user = auth()->user();

        if (!$user->isUser()) {
            $quizzes = Quiz::where('creator_id', $user->id)
                ->where('status', 'active')
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
                ->paginate(10);

            $data = [
                'pageTitle' => trans('quiz.results'),
                'quizzesResults' => $quizzesResults,
                'quizResultsCount' => $quizResultsCount,
                'successRate' => $successRate,
                'quizAvgGrad' => $quizAvgGrad,
                'waitingCount' => $waitingCount,
                'quizzes' => $quizzes,
                'allStudents' => $allStudents
            ];

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

            $quizQuestions = $quizResult->getQuestions();


            $data = [
                'pageTitle' => trans('quiz.result'),
                'quiz' => $quizResult->quiz,
                'quizResult' => $quizResult,
                'userAnswers' => json_decode($quizResult->results, true),
                'numberOfAttempt' => $numberOfAttempt,
                'questionsSumGrade' => $quizQuestions->sum('grade'),
                'quizQuestions' => $quizQuestions,
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

            $quiz = $quizResult->quiz;
            $quizQuestions = $quizResult->getQuestions();

            $data = [
                'pageTitle' => trans('quiz.result'),
                'teacherReviews' => true,
                'quiz' => $quiz,
                'quizResult' => $quizResult,
                'newQuizStart' => $quizResult,
                'userAnswers' => json_decode($quizResult->results, true),
                'numberOfAttempt' => $numberOfAttempt,
                'questionsSumGrade' => $quizQuestions->sum('grade'),
                'quizQuestions' => $quizQuestions
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

                    if ($quizResult->status == QuizzesResult::$passed) {
                        $passTheQuizReward = RewardAccounting::calculateScore(Reward::PASS_THE_QUIZ);
                        RewardAccounting::makeRewardAccounting($quizResult->user_id, $passTheQuizReward, Reward::PASS_THE_QUIZ, $quizResult->id, true);

                        if ($quiz->certificate) {
                            $certificateReward = RewardAccounting::calculateScore(Reward::CERTIFICATE);
                            RewardAccounting::makeRewardAccounting($quizResult->user_id, $certificateReward, Reward::CERTIFICATE, $quiz->id, true);
                        }
                    }

                    return redirect('panel/quizzes/results');
                }
            }
        }

        abort(404);
    }

    public function orderItems(Request $request, $quizId)
    {
        $user = auth()->user();
        $data = $request->all();

        $validator = Validator::make($data, [
            'items' => 'required',
            'table' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $quiz = Quiz::query()->where('id', $quizId)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($quiz)) {
            $tableName = $data['table'];
            $itemIds = explode(',', $data['items']);

            if (!is_array($itemIds) and !empty($itemIds)) {
                $itemIds = [$itemIds];
            }

            if (!empty($itemIds) and is_array($itemIds) and count($itemIds)) {
                switch ($tableName) {
                    case 'quizzes_questions':
                        foreach ($itemIds as $order => $id) {
                            QuizzesQuestion::where('id', $id)
                                ->where('quiz_id', $quiz->id)
                                ->update(['order' => ($order + 1)]);
                        }
                        break;
                }
            }
        }

        return response()->json([
            'title' => trans('public.request_success'),
            'msg' => trans('update.items_sorted_successful')
        ]);
    }

}
