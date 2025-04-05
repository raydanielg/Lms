<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize("panel_financial_sales_reports");

        $user = auth()->user();

        $query = Sale::where('seller_id', $user->id)
            ->whereNull('refund_at');

        $studentIds = deepClone($query)->pluck('buyer_id')->toArray();
        $students = User::select('id', 'full_name')
            ->whereIn('id', array_unique($studentIds))
            ->get();

        $getStudentCount = count($studentIds);
        $getWebinarsCount = count(array_filter(deepClone($query)->pluck('webinar_id')->toArray()));
        $getMeetingCount = count(array_filter(deepClone($query)->pluck('meeting_id')->toArray()));


        $query = $this->filters($query, $request);

        $sales = $query->orderBy('created_at', 'desc')
            ->with([
                'webinar',
                'productOrder',
                'bundle',
                'registrationPackage',
                'promotion',
                'subscribe'
            ])
            ->paginate(10);

        $userWebinars = Webinar::select('id')
            ->where('status', 'active')
            ->where(function ($query) use ($user) {
                $query->where('creator_id', $user->id)
                    ->orWhere('teacher_id', $user->id);
            })->get();

        $data = [
            'pageTitle' => trans('admin/pages/financial.sales_page_title'),
            'sales' => $sales,
            'studentCount' => $getStudentCount,
            'webinarCount' => $getWebinarsCount,
            'meetingCount' => $getMeetingCount,
            'totalSales' => $user->getSaleAmounts(),
            'userWebinars' => $userWebinars,
            'students' => $students,
        ];

        return view(getTemplate() . '.panel.financial.sales', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $student_id = $request->input('student_id');
        $webinar_id = $request->input('webinar_id');
        $type = $request->input('type');

        if (!empty($from) and !empty($to)) {
            $from = strtotime($from);
            $to = strtotime($to);

            $query->whereBetween('created_at', [$from, $to]);
        } else {
            if (!empty($from)) {
                $from = strtotime($from);
                $query->where('created_at', '>=', $from);
            }

            if (!empty($to)) {
                $to = strtotime($to);

                $query->where('created_at', '<', $to);
            }
        }

        if (isset($type) && $type !== 'all') {
            $query->where('type', $type);
        }

        if (!empty($student_id) and $student_id != 'all') {
            $query->where('buyer_id', $student_id);
        }

        if (!empty($webinar_id) and $webinar_id != 'all') {
            $query->where('webinar_id', $webinar_id);
        }

        return $query;
    }
}
