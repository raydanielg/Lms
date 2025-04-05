<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\Api\Quiz;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Role;
use App\Models\WebinarChapter;
use App\User;
use App\Models\Webinar;
use App\Models\Api\QuizzesResult;
use App\Models\Api\QuizzesQuestion;
use App\Models\Api\QuizzesQuestionsAnswer;
use Doctrine\Inflector\Rules\English\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class QuizzesResultController extends Controller
{

    public function myResults(Request $request)
    {
        $quizResults = apiAuth()->quizResults()->handleFilters()
            ->orderBy('created_at', 'desc')
            ->get()->map(function ($quizResult) {
                return $quizResult->details;
            });

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'results' => $quizResults
        ]);

    }

    public function myStudentResult(Request $request)
    {
        $user = apiAuth();
        $quizzes_id = Quiz::where('creator_id', $user->id)
            ->where('status', 'active')
            ->get()->pluck('id')->toArray();

        $quizResults = QuizzesResult::whereIn('quiz_id', $quizzes_id)->handleFilters()
            ->orderBy('created_at', 'desc')
            ->get()->map(function ($quizResult) {
                return $quizResult->details;
            });
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'results' => $quizResults
        ]);

    }

    public function showResult($quizResultId)
    {
        $user = apiAuth();

        $quizzesIds = Quiz::where('creator_id', $user->id)->pluck('id')->toArray();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->where(function ($query) use ($user, $quizzesIds) {
                $query->where('user_id', $user->id)
                    ->orWhereIn('quiz_id', $quizzesIds);
            })->with([
                'quiz' => function ($query) {
                    $query->with(['webinar']);
                    $query->with([
                        'quizQuestions' => function ($query) {
                            $query->with('quizzesQuestionsAnswers');
                        }
                    ]);
                }
            ])->first();

        if (!$quizResult) {
            abort(404);
        }
        $numberOfAttempt = QuizzesResult::where('quiz_id', $quizResult->quiz->id)
            ->where('user_id', $quizResult->user_id)
            ->count();

        $quizQuestions = $quizResult->getQuestions();


        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'quizResult' => $quizResult,
            'quizResultDetails' => $quizResult->details,
            'results' => $quizResult->results = json_decode($quizResult->results, true),
            'questionsSumGrade' => $quizQuestions->sum('grade'),
            'quizQuestions' => $quizQuestions,
            'numberOfAttempt' => $numberOfAttempt,
        ]);
    }
    public function status($quizResultId)
    {
        $user = apiAuth();

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->where('user_id', $user->id)
            ->first();

        if (!$quizResult) {
            abort(404);
        }

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), [
            'result' => $quizResult->details
        ]);

    }

    public function start(Request $request, $id)
    {
        $user = apiAuth();
        $quiz = Quiz::find($id);
        if (!$quiz) {
            abort(404);
        }
        $auth_can_take_quiz_status = $quiz->auth_can_take_quiz_status;

        if ($auth_can_take_quiz_status != 'ok') {
            return apiResponse2(0, $auth_can_take_quiz_status, trans('api.public.stored'));
        }

        $userQuizDone = QuizzesResult::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->get();

        $newQuizStart = QuizzesResult::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'results' => '',
            'user_grade' => 0,
            'status' => 'waiting',
            'created_at' => time()
        ]);

        return apiResponse2(1, 'stored', trans('api.public.stored'),
            [
                'quiz_result_id' => $newQuizStart->id,
                'quiz' => $quiz->details, 'attempt_number' => $userQuizDone->count() + 1]);


    }

    public function quizzesStoreResult(Request $request, $id)
    {

        $user = apiAuth();
        $quiz = Quiz::where('id', $id)->first();
        abort_unless($quiz, 404);

        validateParam($request->all(), [
            'quiz_result_id' => [
                'required', Rule::exists('quizzes_results', 'id')->where('user_id', $user->id)
            ],
            'answer_sheet' => ['nullable', 'array', 'min:0'],

            'answer_sheet.*.question_id' => ['required', Rule::exists('quizzes_questions', 'id')
                ->where('quiz_id', $quiz->id)
            ],
            'answer_sheet.*.answer' => ['nullable',

                // Rule::exists('quizzes_questions_answers', 'id')

            ],

        ]);

        $auth_can_take_quiz_status = $quiz->auth_can_take_quiz_status;


        $answer_sheet = $request->get('answer_sheet');
        $quizResultId = $request->get('quiz_result_id');

        $questionn = [];
        foreach ($answer_sheet as $sheet) {

            $questionn[$sheet['question_id']] = [
                'answer' => $sheet['answer']
            ];
        }
        //  dd($questionn);


        if ($quiz) {
            $results = $questionn;

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

                    $attempt_count = QuizzesResult::where('quiz_id', $quiz->id)
                        ->where('user_id', $user->id)
                        ->count();

                    $results["attempt_number"] = $attempt_count;

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
                    }

                    if ($quiz->certificate) {
                        $certificateReward = RewardAccounting::calculateScore(Reward::CERTIFICATE);
                        RewardAccounting::makeRewardAccounting($quizResult->user_id, $certificateReward, Reward::CERTIFICATE, $quiz->id, true);
                    }

                    return apiResponse2(1, 'stored', trans('api.public.stored'), [
                        'result' => $quizResult->details
                    ]);
                    //  return redirect()->route('quiz_status', ['quizResultId' => $quizResult]);
                }
            }
        }
        abort(404);
    }

    public function updateResult(Request $request, $quizResultId)
    {

        $user = apiAuth();

        $quizResult = QuizzesResult::where('id', $quizResultId)->first();
        abort_unless($quizResult, 404);


        $quiz = $quizResult->quiz()->where('creator_id', $user->id)->first();
        abort_unless($quiz, 404);


        validateParam($request->all(), [
            // 'correction'=>'array|required' ,
            '*.question_id' => ['required', Rule::exists('quizzes_questions', 'id')
                ->where('quiz_id', $quiz->id)->where('type', 'descriptive')
            ],

            //  'correction.*.answer'=>'required' ,
            '*.grade' => 'required',

        ]);

        if (!$quizResult->reviewable) {
            return apiResponse2(0, 'unreviewable', trans('api.quiz.retrieved'));
        }
        $reviews = [];

        foreach ($request->all() as $re) {
            if (!empty($re['question_id'])) {
                $reviews[$re['question_id']] = [
                    'correct_answer' => $re['answer'],
                    'grade' => $re['grade']
                ];
            }

        }

        $quizResult = QuizzesResult::where('id', $quizResultId)
            ->where('quiz_id', $quiz->id)
            ->first();

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
            RewardAccounting::makeRewardAccounting($quizResult->user_id, $passTheQuizReward, Reward::PASS_THE_QUIZ, $passTheQuizReward->id, true);
        }

        if ($quiz->certificate) {
            $certificateReward = RewardAccounting::calculateScore(Reward::CERTIFICATE);
            RewardAccounting::makeRewardAccounting($quizResult->user_id, $certificateReward, Reward::CERTIFICATE, $quiz->id, true);
        }
        return apiResponse2(1, 'stored', trans('api.public.stored'));


    }


}
