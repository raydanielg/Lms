<?php

namespace App\Http\Controllers\Admin\traits;

use App\Models\Accounting;
use App\Models\Comment;
use App\Models\Meeting;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Support;
use App\Models\Webinar;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Web;

trait DashboardTrait
{
    public function dailySalesTypeStatistics()
    {
        $this->authorize('admin_general_dashboard_daily_sales_statistics');

        $beginOfDay = strtotime("today", time());
        $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;

        $webinarsSales = Sale::whereNull('refund_at')
            ->where('type', Sale::$webinar)
            ->whereBetween('created_at', [$beginOfDay, $endOfDay])
            ->whereHas('webinar', function ($query) {
                $query->where('type', Webinar::$webinar);
            })->count();

        $courseSales = Sale::whereNull('refund_at')
            ->where('type', Sale::$webinar)
            ->whereBetween('created_at', [$beginOfDay, $endOfDay])
            ->whereHas('webinar', function ($query) {
                $query->where('type', Webinar::$course);
            })->count();

        $appointmentSales = Sale::whereNull('refund_at')
            ->where('type', Sale::$meeting)
            ->whereBetween('created_at', [$beginOfDay, $endOfDay])
            ->count();

        $allSales = Sale::whereNull('refund_at')
            ->whereIn('type', [Sale::$webinar, Sale::$meeting])
            ->whereBetween('created_at', [$beginOfDay, $endOfDay])
            ->count();


        return [
            'webinarsSales' => $webinarsSales,
            'courseSales' => $courseSales,
            'appointmentSales' => $appointmentSales,
            'allSales' => $allSales,
        ];
    }

    public function getIncomeStatistics()
    {
        $this->authorize('admin_general_dashboard_income_statistics');

        $dateStartAndEnd = $this->getAllDateStartAndEnd();

        $beginOfDay = $dateStartAndEnd['today']['start'];
        $endOfDay = $dateStartAndEnd['today']['end'];

        $beginOfMonth = $dateStartAndEnd['month']['start'];
        $endOfMonth = $dateStartAndEnd['month']['end'];

        $beginOfYear = $dateStartAndEnd['year']['start'];
        $endOfYear = $dateStartAndEnd['year']['end'];

        $totalSales = $this->getIncomes();

        $todaySales = $this->getIncomes($beginOfDay, $endOfDay);

        $monthSales = $this->getIncomes($beginOfMonth, $endOfMonth);

        $yearSales = $this->getIncomes($beginOfYear, $endOfYear);

        return [
            'totalSales' => $totalSales,
            'todaySales' => $todaySales,
            'monthSales' => $monthSales,
            'yearSales' => $yearSales,
        ];
    }

    private function getIncomes($from = null, $to = null)
    {
        $query = Accounting::where(function ($query) {
            $query->where('system', true)
                ->orWhere('tax', true);
        });

        $query = fromAndToDateFilter($from, $to, $query, 'created_at', false);

        $additions = deepClone($query)
            ->where('type', Accounting::$addiction)
            ->sum('amount');

        $deductions = deepClone($query)
            ->where('type', Accounting::$deduction)
            ->sum('amount');

        $income = $additions - $deductions;
        return $income > 0 ? $income : 0;
    }

    private function getAllDateStartAndEnd()
    {
        $time = time();
        $beginOfDay = strtotime("today", $time);
        $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;

        $monday = strtotime('next Monday -1 week');
        $beginOfWeek = date('w', $monday) == date('w') ? strtotime(date("Y-m-d", $monday) . " +7 days") : $monday;
        $endOfWeek = strtotime(date("Y-m-d", $beginOfWeek) . " +7 days") - 1;

        $beginOfMonth = strtotime(date('Y-m-01', $time));// First day of the month.
        $endOfMonth = strtotime(date('Y-m-t', $time));// Last day of the month.

        $beginOfYear = strtotime(date('Y-01-01', $time));// First day of the year.
        $endOfYear = strtotime(date('Y-m-d', strtotime('12/31'))); // Last day of the year.

        return [
            'today' => [
                'start' => $beginOfDay,
                'end' => $endOfDay,
            ],
            'week' => [
                'start' => $beginOfWeek,
                'end' => $endOfWeek,
            ],
            'month' => [
                'start' => $beginOfMonth,
                'end' => $endOfMonth,
            ],
            'year' => [
                'start' => $beginOfYear,
                'end' => $endOfYear,
            ],
        ];
    }

    public function getTotalSalesStatistics()
    {
        $dateStartAndEnd = $this->getAllDateStartAndEnd();

        $beginOfDay = $dateStartAndEnd['today']['start'];
        $endOfDay = $dateStartAndEnd['today']['end'];

        $beginOfMonth = $dateStartAndEnd['month']['start'];
        $endOfMonth = $dateStartAndEnd['month']['end'];

        $beginOfYear = $dateStartAndEnd['year']['start'];
        $endOfYear = $dateStartAndEnd['year']['end'];

        $totalSales = Sale::whereNull('refund_at')->count();

        $todaySales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$beginOfDay, $endOfDay])
            ->count();

        $monthSales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$beginOfMonth, $endOfMonth])
            ->count();

        $yearSales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$beginOfYear, $endOfYear])
            ->count();

        return [
            'totalSales' => $totalSales,
            'todaySales' => $todaySales,
            'monthSales' => $monthSales,
            'yearSales' => $yearSales,
        ];
    }

    public function getNewSalesCount()
    {
        $this->authorize('admin_general_dashboard_new_sales');

        return Sale::whereNull('refund_at')
            ->whereDoesntHave('saleLog')
            ->count();
    }

    public function getNewCommentsCount()
    {
        $this->authorize('admin_general_dashboard_new_comments');

        return Comment::where('status', 'pending')
            ->count();
    }

    public function getNewTicketsCount()
    {
        $this->authorize('admin_general_dashboard_new_tickets');

        $time = time();
        $beginOfDay = strtotime("today", $time);
        $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;

        return Support::whereNotNull('department_id')
            ->whereIn('status', ['replied', 'open'])
            ->whereBetween('updated_at', [$beginOfDay, $endOfDay])
            ->count();
    }

    public function getPendingReviewCount()
    {
        $this->authorize('admin_general_dashboard_new_reviews');

        return Webinar::where('status', 'pending')
            ->count();
    }

    public function getMonthAndYearSalesChart($type = 'month_of_year')
    {
        $labels = [];
        $data = [];

        if ($type == 'day_of_month') {

            for ($day = 1; $day <= 31; $day++) {
                $startDay = strtotime(date('Y-m-' . $day));
                $endDay = strtotime('-1 second', strtotime('+1 day', $startDay));

                $labels[] = str_pad($day, 2, 0, STR_PAD_LEFT);

                $amount = Sale::whereNull('refund_at')
                    ->whereBetween('created_at', [$startDay, $endDay])
                    ->sum('total_amount');
                $data[] = round($amount, 2);
            }

        } elseif ($type == 'month_of_year') {
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create(date('Y'), $month);

                $start_date = $date->timestamp;
                $end_date = $date->copy()->endOfMonth()->timestamp;

                $labels[] = trans('panel.month_' . $month);

                $amount = Sale::whereNull('refund_at')
                    ->whereBetween('created_at', [$start_date, $end_date])
                    ->sum('total_amount');

                $data[] = round($amount, 2);
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getMonthAndYearSalesChartStatistics()
    {
        $dateStartAndEnd = $this->getAllDateStartAndEnd();

        $beginOfDay = $dateStartAndEnd['today']['start'];
        $endOfDay = $dateStartAndEnd['today']['end'];

        $beginOfWeek = $dateStartAndEnd['week']['start'];
        $endOfWeek = $dateStartAndEnd['week']['end'];

        $beginOfMonth = $dateStartAndEnd['month']['start'];
        $endOfMonth = $dateStartAndEnd['month']['end'];

        $beginOfYear = $dateStartAndEnd['year']['start'];
        $endOfYear = $dateStartAndEnd['year']['end'];

        $lastDayStart = $beginOfDay - 24 * 60 * 60;
        $lastDayEnd = $endOfDay - 24 * 60 * 60;

        $lastWeekStart = $beginOfWeek - 7 * 24 * 60 * 60;
        $lastWeekEnd = $endOfWeek - 7 * 24 * 60 * 60;

        $time = time();
        $lastMonthStart = strtotime(date('Y-m-01', strtotime('last month', $time))); // First day of the last month.
        $lastMonthEnd = strtotime(date('Y-m-t', strtotime('last month', $time))); // Last day of the last month.

        $lastYearStart = $beginOfYear - 365 * 24 * 60 * 60;
        $lastYearEnd = $endOfYear - 365 * 24 * 60 * 60;


        $todaySales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$beginOfDay, $endOfDay])
            ->sum('total_amount');

        $lastDaySales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$lastDayStart, $lastDayEnd])
            ->sum('total_amount');

        $weekSales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$beginOfWeek, $endOfWeek])
            ->sum('total_amount');

        $lastWeekSales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->sum('total_amount');

        $monthSales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$beginOfMonth, $endOfMonth])
            ->sum('total_amount');

        $lastMonthSales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');

        $yearSales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$beginOfYear, $endOfYear])
            ->sum('total_amount');

        $lastYearSales = Sale::whereNull('refund_at')
            ->whereBetween('created_at', [$lastYearStart, $lastYearEnd])
            ->sum('total_amount');

        return [
            'todaySales' => [
                'amount' => $todaySales,
                'grow_percent' => $this->getGrowPercent($lastDaySales, $todaySales),
            ],
            'weekSales' => [
                'amount' => $weekSales,
                'grow_percent' => $this->getGrowPercent($lastWeekSales, $weekSales),
            ],
            'monthSales' => [
                'amount' => $monthSales,
                'grow_percent' => $this->getGrowPercent($lastMonthSales, $monthSales),
            ],
            'yearSales' => [
                'amount' => $yearSales,
                'grow_percent' => $this->getGrowPercent($lastYearSales, $yearSales),
            ],
        ];
    }

    private function getGrowPercent($last, $new)
    {
        $percent = 'No previous value';
        $status = 'up';

        if ($last != 0) {
            $tmp = ($new - $last);
            $abs = abs($last);

            $res = ($tmp / $abs * 100);

            $percent = round($res, 3) . '%';
            $status = $res > 0 ? 'up' : 'down';
        }

        return [
            'percent' => $percent,
            'status' => $status
        ];
    }

    public function getRecentComments()
    {
        $this->authorize('admin_general_dashboard_recent_comments');

        return Comment::orderBy('created_at', 'desc')->limit(6)->get();
    }

    public function getRecentTickets()
    {
        $this->authorize('admin_general_dashboard_recent_tickets');

        $tickets = Support::whereNotNull('department_id')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingReply = Support::whereNotNull('department_id')
            ->whereIn('status', ['open', 'replied'])
            ->count();

        return [
            'tickets' => $tickets,
            'pendingReply' => $pendingReply,
        ];
    }

    public function getRecentWebinars()
    {
        $this->authorize('admin_general_dashboard_recent_webinars');


        $webinars = Webinar::where('type', Webinar::$webinar)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingReviews = Webinar::where('type', Webinar::$webinar)
            ->where('status', 'pending')
            ->count();

        return [
            'webinars' => $webinars,
            'pendingReviews' => $pendingReviews,
        ];
    }

    public function getRecentCourses()
    {
        $this->authorize('admin_general_dashboard_recent_courses');


        $courses = Webinar::where('type', Webinar::$course)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingReviews = Webinar::where('type', Webinar::$course)
            ->where('status', 'pending')
            ->count();

        return [
            'courses' => $courses,
            'pendingReviews' => $pendingReviews,
        ];
    }

    public function usersStatisticsChart()
    {
        $labels = [];
        $data = [];

        for ($day = 1; $day <= 31; $day++) {
            $startDay = strtotime(date('Y-m-' . $day));
            $endDay = strtotime('-1 second', strtotime('+1 day', $startDay));

            $labels[] = str_pad($day, 2, 0, STR_PAD_LEFT);

            $data[] = User::whereBetween('created_at', [$startDay, $endDay])
                ->count();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getClassesStatistics()
    {
        $labels = [Webinar::$webinar, Webinar::$course, Webinar::$textLesson];
        $data = [];

        $query = Webinar::where('status', Webinar::$active);
        $allClasses = $query->count();

        foreach ($labels as $label) {
            $count = deepClone($query)->where('type', $label)->count();
            $percent = !empty($allClasses) ? ($count * 100) / $allClasses : 0;
            $data[] = round($percent, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getNetProfitChart($type = 'month_of_year')
    {
        $labels = [];
        $data = [];

        $query = Accounting::where('system', 1)
            ->where('tax', 0);

        if ($type == 'day_of_month') {

            for ($day = 1; $day <= 31; $day++) {
                $startDay = strtotime(date('Y-m-' . $day));
                $endDay = strtotime('-1 second', strtotime('+1 day', $startDay));

                $labels[] = str_pad($day, 2, 0, STR_PAD_LEFT);

                $amount = $this->computingAccounting(deepClone($query), $startDay, $endDay);
                $data[] = round($amount, 2);
            }
        } elseif ($type == 'month_of_year') {
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create(date('Y'), $month);

                $start_date = $date->timestamp;
                $end_date = $date->copy()->endOfMonth()->timestamp;

                $labels[] = trans('panel.month_' . $month);

                $amount = $this->computingAccounting(deepClone($query), $start_date, $end_date);
                $data[] = round($amount, 2);
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function computingAccounting($query, $start, $end)
    {
        $additions = deepClone($query)->whereBetween('created_at', [$start, $end])
            ->where('type', Accounting::$addiction)
            ->sum('amount');

        $deductions = deepClone($query)->whereBetween('created_at', [$start, $end])
            ->where('type', Accounting::$deduction)
            ->sum('amount');

        $charge = $additions - $deductions;
        return $charge > 0 ? $charge : 0;
    }

    public function getNetProfitStatistics()
    {
        $dateStartAndEnd = $this->getAllDateStartAndEnd();

        $beginOfDay = $dateStartAndEnd['today']['start'];
        $endOfDay = $dateStartAndEnd['today']['end'];

        $beginOfWeek = $dateStartAndEnd['week']['start'];
        $endOfWeek = $dateStartAndEnd['week']['end'];

        $beginOfMonth = $dateStartAndEnd['month']['start'];
        $endOfMonth = $dateStartAndEnd['month']['end'];

        $beginOfYear = $dateStartAndEnd['year']['start'];
        $endOfYear = $dateStartAndEnd['year']['end'];

        $lastDayStart = $beginOfDay - 24 * 60 * 60;
        $lastDayEnd = $endOfDay - 24 * 60 * 60;

        $lastWeekStart = $beginOfWeek - 7 * 24 * 60 * 60;
        $lastWeekEnd = $endOfWeek - 7 * 24 * 60 * 60;


        $time = time();
        $lastMonthStart = strtotime(date('Y-m-01', strtotime('last month', $time))); // First day of the last month.
        $lastMonthEnd = strtotime(date('Y-m-t', strtotime('last month', $time))); // Last day of the last month.

        $lastYearStart = $beginOfYear - 365 * 24 * 60 * 60;
        $lastYearEnd = $endOfYear - 365 * 24 * 60 * 60;

        $query = Accounting::where('system', 1)
            ->where('tax', 0);


        $todayIncome = $this->computingAccounting(deepClone($query), $beginOfDay, $endOfDay);

        $lastDayIncome = $this->computingAccounting(deepClone($query), $lastDayStart, $lastDayEnd);

        $weekIncome = $this->computingAccounting(deepClone($query), $beginOfWeek, $endOfWeek);

        $lastWeekIncome = $this->computingAccounting(deepClone($query), $lastWeekStart, $lastWeekEnd);

        $monthIncome = $this->computingAccounting(deepClone($query), $beginOfMonth, $endOfMonth);

        $lastMonthIncome = $this->computingAccounting(deepClone($query), $lastMonthStart, $lastMonthEnd);

        $yearIncome = $this->computingAccounting(deepClone($query), $beginOfYear, $endOfYear);

        $lastYearIncome = $this->computingAccounting(deepClone($query), $lastYearStart, $lastYearEnd);

        return [
            'todayIncome' => [
                'amount' => $todayIncome,
                'grow_percent' => $this->getGrowPercent($lastDayIncome, $todayIncome),
            ],
            'weekIncome' => [
                'amount' => $weekIncome,
                'grow_percent' => $this->getGrowPercent($lastWeekIncome, $weekIncome),
            ],
            'monthIncome' => [
                'amount' => $monthIncome,
                'grow_percent' => $this->getGrowPercent($lastMonthIncome, $monthIncome),
            ],
            'yearIncome' => [
                'amount' => $yearIncome,
                'grow_percent' => $this->getGrowPercent($lastYearIncome, $yearIncome),
            ],
        ];
    }

    public function getTopSellingClasses()
    {
        return Webinar::where('status', Webinar::$active)
            ->join('sales', 'webinars.id', '=', 'sales.webinar_id')
            ->select('webinars.*', 'sales.webinar_id',
                DB::raw('count(sales.webinar_id) as sales_count'),
                DB::raw('sum(sales.total_amount) as sales_amount')
            )->whereNull('sales.refund_at')
            ->where('sales.amount', '>', '0')
            ->groupBy('sales.webinar_id')
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();
    }

    public function getTopSellingAppointments()
    {
        return Meeting::where('disabled', false)
            ->join('sales', 'meetings.id', '=', 'sales.meeting_id')
            ->select('meetings.*', 'sales.meeting_id',
                DB::raw('count(sales.meeting_id) as sales_count'),
                DB::raw('sum(sales.total_amount) as sales_amount')
            )->whereNull('sales.refund_at')
            ->where('sales.amount', '>', '0')
            ->groupBy('sales.meeting_id')
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();
    }

    public function getTopSellingTeachersAndOrganizations($role = 'teachers')
    {
        $users = User::where('status', 'active')
            ->join('sales', 'users.id', '=', 'sales.seller_id')
            ->select('users.*', 'sales.seller_id',
                DB::raw('count(sales.seller_id) as sales_count'),
                DB::raw('sum(sales.total_amount) as sales_amount')
            )->whereNull('sales.refund_at')
            ->where('sales.amount', '>', '0')
            ->where('users.role_name', (($role == 'teachers') ? Role::$teacher : Role::$organization))
            ->groupBy('sales.seller_id')
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();

        foreach ($users as $user) {
            $duration = Webinar::where('status', Webinar::$active)
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })->sum('duration');

            $user->classes_durations = $duration;
        }

        return $users;
    }

    public function getMostActiveStudents()
    {
        return User::where('status', 'active')
            ->join('sales', 'users.id', '=', 'sales.buyer_id')
            ->select('users.*', 'sales.buyer_id',
                DB::raw('count(sales.webinar_id) as purchased_classes'),
                DB::raw('count(sales.meeting_id) as reserved_appointments'),
                DB::raw('sum(sales.total_amount) as total_cost')
            )->whereNull('sales.refund_at')
            ->where('sales.amount', '>', '0')
            ->where('users.role_name', Role::$user)
            ->groupBy('sales.buyer_id')
            ->orderBy('purchased_classes', 'desc')
            ->orderBy('reserved_appointments', 'desc')
            ->limit(5)
            ->get();
    }
}
