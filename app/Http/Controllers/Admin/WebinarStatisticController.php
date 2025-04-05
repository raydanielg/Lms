<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CourseForum;
use App\Models\CourseLearning;
use App\Models\Gift;
use App\Models\InstallmentOrder;
use App\Models\Quiz;
use App\Models\QuizzesResult;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Webinar;
use App\Models\WebinarAssignment;
use App\Models\WebinarAssignmentHistory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WebinarStatisticController extends Controller
{
    public function index(Request $request, $webinarId)
    {
        $this->authorize('admin_webinar_statistics');

        $webinar = Webinar::where('id', $webinarId)
            ->with([
                'chapters' => function ($query) {
                    $query->where('status', 'active');
                },
                'sessions' => function ($query) {
                    $query->where('status', 'active');
                },
                'assignments' => function ($query) {
                    $query->where('status', 'active');
                },
                'quizzes' => function ($query) {
                    $query->where('status', 'active');
                },
                'files' => function ($query) {
                    $query->where('status', 'active');
                },
                'reviews' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->first();

        if (!empty($webinar)) {
            $studentsIds = Sale::where('webinar_id', $webinarId)
                ->whereNull('refund_at')
                ->pluck('buyer_id')
                ->toArray();

            $gifts = Gift::query()->where('webinar_id', $webinar->id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('date');
                    $query->orWhere('date', '<', time());
                })
                ->whereHas('sale')
                ->get();

            $installmentStudentIds = InstallmentOrder::query()
                ->where('webinar_id', $webinar->id)
                ->where('status', 'open')
                ->pluck('user_id')
                ->toArray();

            $studentsIds = array_merge($studentsIds, $installmentStudentIds);

            $getStudents = $this->getStudents($request, $webinar, $studentsIds, $gifts);

            $data = [
                'pageTitle' => trans('update.course_statistics'),
                'webinar' => $webinar,
                'students' => $getStudents['users'],
                'unregisteredUsers' => $getStudents['unregisteredUsers'],
                'studentsCount' => (count(array_unique($studentsIds)) + count($gifts)),
                'commentsCount' => $this->getCommentsCount($webinarId),
                'salesCount' => (count($studentsIds) + count($gifts)),
                'salesAmount' => $this->getSalesAmounts($webinarId, $gifts->pluck('id')->toArray()),
                'chaptersCount' => $webinar->chapters->count(),
                'sessionsCount' => $webinar->sessions->count(),
                'pendingQuizzesCount' => $this->getPendingQuizzesCount($webinarId),
                'pendingAssignmentsCount' => $this->getPendingAssignmentsCount($webinarId),
                'courseRate' => $webinar->getRate(),
                'courseRateCount' => $webinar->reviews->count(),
                'quizzesAverageGrade' => $this->getQuizzesAverageGrade($webinarId),
                'assignmentsAverageGrade' => $this->getAssignmentsAverageGrade($webinarId),
                'courseForumsMessagesCount' => $this->getCourseForumsMessagesCount($webinarId),
                'courseForumsStudentsCount' => $this->getCourseForumsStudentsCount($webinarId),
                'studentsUserRolesChart' => $this->handleStudentsUserRolesChart($studentsIds),
                'courseProgressChart' => $this->handleCourseProgressChart($webinar, $studentsIds),
                'quizStatusChart' => $this->handleQuizStatusChart($webinar),
                'assignmentsStatusChart' => $this->handleAssignmentsStatusChart($webinar),
                'monthlySalesChart' => $this->getMonthlySalesChart($webinarId),
                'courseProgressLineChart' => $this->handleCourseProgressLineChart($webinar, $studentsIds),
            ];

            return view('admin.webinars.course_statistics.index', $data);
        }

        abort(404);
    }

    private function getStudents(Request $request, $webinar, $studentsIds, $gifts)
    {
        $receiptsGift = [];
        $unregisteredGift = [];

        foreach ($gifts as $gift) {
            $receipt = $gift->receipt;

            if (!empty($receipt)) {
                $receiptsGift[] = $receipt->id;
            } else {
                $unregisteredGift[] = $gift;
            }
        }

        $studentsIds = array_merge($studentsIds, $receiptsGift);

        $users = User::whereIn('id', $studentsIds)
            ->paginate(10);

        $quizzesIds = $webinar->quizzes->pluck('id')->toArray();
        $assignmentsIds = $webinar->assignments->pluck('id')->toArray();

        foreach ($users as $user) {
            $user->course_progress = $this->getCourseProgressForStudent($webinar, $user->id);

            $user->passed_quizzes = Quiz::whereIn('quizzes.id', $quizzesIds)
                ->join('quizzes_results', 'quizzes_results.quiz_id', 'quizzes.id')
                ->select(DB::raw('count(quizzes_results.id) as count'))
                ->where('quizzes_results.user_id', $user->id)
                ->where('quizzes_results.status', QuizzesResult::$passed)
                ->first()->count;

            $assignmentsHistoriesCount = WebinarAssignmentHistory::whereIn('assignment_id', $assignmentsIds)
                ->where('student_id', $user->id)
                ->count();

            $user->unsent_assignments = count($assignmentsIds) - $assignmentsHistoriesCount;

            $user->pending_assignments = WebinarAssignmentHistory::whereIn('assignment_id', $assignmentsIds)
                ->where('student_id', $user->id)
                ->where('status', WebinarAssignmentHistory::$pending)
                ->count();
        }

        $unregisteredUsers = Collection::make(new User());

        if (count($unregisteredGift) and $request->get('page', 1) == 1) {
            foreach ($unregisteredGift as $item) {
                $newUser = new User();
                $newUser->full_name = $item->name;
                $newUser->email = $item->email;

                $unregisteredUsers = $unregisteredUsers->push($newUser);
            }
        }

        return [
            'users' => $users,
            'unregisteredUsers' => $unregisteredUsers,
        ];
    }

    private function getCommentsCount($webinarId)
    {
        return Comment::where('webinar_id', $webinarId)
            ->where('status', 'active')
            ->count();
    }

    private function getSalesAmounts($webinarId, $giftsIds)
    {
        return Sale::query()
            ->where(function ($query) use ($webinarId, $giftsIds) {
                $query->where('webinar_id', $webinarId);
                $query->orWhereIn('gift_id', $giftsIds);
            })
            ->whereNull('refund_at')
            ->sum('total_amount');
    }

    private function getPendingQuizzesCount($webinarId)
    {
        return Quiz::where('webinar_id', $webinarId)
            ->where('status', 'active')
            ->whereHas('quizResults', function ($query) {
                $query->where('status', 'waiting');
            })
            ->count();
    }

    private function getPendingAssignmentsCount($webinarId)
    {
        return WebinarAssignment::where('webinar_id', $webinarId)
            ->where('status', 'active')
            ->whereHas('assignmentHistory', function ($query) {
                $query->where('status', 'pending');
            })
            ->count();
    }

    private function getQuizzesAverageGrade($webinarId)
    {
        $quizzes = Quiz::where('webinar_id', $webinarId)
            ->join('quizzes_results', 'quizzes_results.quiz_id', 'quizzes.id')
            ->select(DB::raw('avg(quizzes_results.user_grade) as result_grade'))
            ->whereIn('quizzes_results.status', ['passed', 'failed'])
            ->groupBy('quizzes_results.quiz_id')
            ->get();

        return $quizzes->avg('result_grade');
    }

    private function getAssignmentsAverageGrade($webinarId)
    {
        $assignments = WebinarAssignment::where('webinar_id', $webinarId)
            ->join('webinar_assignment_history', 'webinar_assignment_history.assignment_id', 'webinar_assignments.id')
            ->select(DB::raw('avg(webinar_assignment_history.grade) as result_grade'))
            ->whereIn('webinar_assignment_history.status', ['passed', 'not_passed'])
            ->groupBy('webinar_assignment_history.assignment_id')
            ->get();

        return $assignments->avg('result_grade') ?? 0;
    }

    private function getCourseForumsMessagesCount($webinarId)
    {
        $forums = CourseForum::where('webinar_id', $webinarId)
            ->join('course_forum_answers', 'course_forum_answers.forum_id', 'course_forums.id')
            ->select(DB::raw('count(course_forum_answers.id) as count'))
            ->groupBy('course_forum_answers.forum_id')
            ->get();

        return $forums->sum('count') ?? 0;
    }

    private function getCourseForumsStudentsCount($webinarId)
    {
        $forums = CourseForum::where('webinar_id', $webinarId)
            ->join('course_forum_answers', 'course_forum_answers.forum_id', 'course_forums.id')
            ->select(DB::raw('count(distinct course_forum_answers.user_id) as count'))
            ->groupBy('course_forum_answers.forum_id')
            ->get();

        return $forums->sum('count') ?? 0;
    }

    private function handleStudentsUserRolesChart($studentsIds)
    {
        $labels = [
            trans('public.students'),
            trans('public.instructors'),
            trans('home.organizations'),
        ];

        $users = User::whereIn('id', $studentsIds)
            ->select('id', 'role_name', DB::raw('count(id) as count'))
            ->groupBy('role_name')
            ->get();

        $data = [0, 0, 0];

        foreach ($users as $user) {
            if ($user->role_name == Role::$user) {
                $data[0] = $user->count;
            } else if ($user->role_name == Role::$teacher) {
                $data[1] = $user->count;
            } else if ($user->role_name == Role::$organization) {
                $data[2] = $user->count;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function handleQuizStatusChart($webinar)
    {
        $labels = [
            trans('quiz.passed'),
            trans('public.pending'),
            trans('quiz.failed'),
        ];

        $data[0] = 0; // passed
        $data[1] = 0; // pending
        $data[2] = 0; // failed

        $quizzes = $webinar->quizzes;

        foreach ($quizzes as $quiz) {
            $passed = $quiz->quizResults()->where('status', QuizzesResult::$passed)->count();
            $pending = $quiz->quizResults()->where('status', QuizzesResult::$waiting)->count();
            $failed = $quiz->quizResults()->where('status', QuizzesResult::$failed)->count();

            $data[0] += $passed;
            $data[1] += $pending;
            $data[2] += $failed;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function handleAssignmentsStatusChart($webinar)
    {
        $labels = [
            trans('quiz.passed'),
            trans('public.pending'),
            trans('quiz.failed'),
        ];

        $data[0] = 0; // passed
        $data[1] = 0; // pending
        $data[2] = 0; // failed

        $assignments = $webinar->assignments;

        foreach ($assignments as $quiz) {
            $passed = $quiz->assignmentHistory()->where('status', WebinarAssignmentHistory::$passed)->count();
            $pending = $quiz->assignmentHistory()->where('status', WebinarAssignmentHistory::$pending)->count();
            $failed = $quiz->assignmentHistory()->where('status', WebinarAssignmentHistory::$notPassed)->count();

            $data[0] += $passed;
            $data[1] += $pending;
            $data[2] += $failed;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getMonthlySalesChart($webinarId)
    {
        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(date('Y'), $month);

            $start_date = $date->timestamp;
            $end_date = $date->copy()->endOfMonth()->timestamp;

            $labels[] = trans('panel.month_' . $month);

            $amount = Sale::whereNull('refund_at')
                ->whereBetween('created_at', [$start_date, $end_date])
                ->where('webinar_id', $webinarId)
                ->sum('total_amount');

            $data[] = round($amount, 2);
        }


        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getCourseProgressForStudent($webinar, $userId)
    {
        $progress = 0;

        $filesStat = $webinar->getFilesLearningProgressStat($userId);
        $sessionsStat = $webinar->getSessionsLearningProgressStat($userId);
        $textLessonsStat = $webinar->getTextLessonsLearningProgressStat($userId);
        $assignmentsStat = $webinar->getAssignmentsLearningProgressStat($userId);
        $quizzesStat = $webinar->getQuizzesLearningProgressStat($userId);

        $passed = $filesStat['passed'] + $sessionsStat['passed'] + $textLessonsStat['passed'] + $assignmentsStat['passed'] + $quizzesStat['passed'];
        $count = $filesStat['count'] + $sessionsStat['count'] + $textLessonsStat['count'] + $assignmentsStat['count'] + $quizzesStat['count'];

        if ($passed > 0 and $count > 0) {
            $progress = ($passed * 100) / $count;
        }

        return round($progress, 2);
    }

    public function handleCourseProgressChart($webinar, $studentsIds)
    {
        $labels = [
            trans('update.completed'),
            trans('webinars.in_progress'),
            trans('update.not_started'),
        ];

        $data[0] = 0; // completed
        $data[1] = 0; // in_progress
        $data[2] = 0; // not_started

        foreach ($studentsIds as $userId) {

            $progress = $this->getCourseProgressForStudent($webinar, $userId);

            if ($progress > 0 and $progress < 100) {
                $data[1] += 1;
            } elseif ($progress == 100) {
                $data[0] += 1;
            } else {
                $data[2] += 1;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function handleCourseProgressLineChart($webinar, $studentsIds)
    {
        $labels = [];
        $data = [];

        $progress = [];

        foreach ($studentsIds as $userId) {
            $progress[] = $this->getCourseProgressForStudent($webinar, $userId);
        }

        for ($percent = 0; $percent < 100; $percent += 10) {
            $endPercent = $percent + 10;
            $labels[] = $percent . '-' . $endPercent;

            $count = 0;

            foreach ($progress as $value) {
                if ($value >= $percent and $value < $endPercent) {
                    $count += 1;
                }
            }

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
