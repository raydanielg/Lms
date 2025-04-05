<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\Api\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $user = apiAuth();
        $query = Sale::where('seller_id', $user->id)
            ->whereNull('refund_at');

        $studentIds = deepClone($query)->pluck('buyer_id')->toArray();
       
        $getStudentCount = count($studentIds);
        $getWebinarsCount = count(array_filter(deepClone($query)->pluck('webinar_id')->toArray()));
        $getMeetingCount = count(array_filter(deepClone($query)->pluck('meeting_id')->toArray()));

 
        $sales = $query->handleFilters()->orderBy('created_at', 'desc')
            ->get()->map(function ($sale) {
               
                return $sale->details ;
            });

        return apiResponse2(1, 'retrieved', trans('public.retrieved'), [
            'sales' => $sales,
            'students_count' => $getStudentCount,
            'webinars_count' => $getWebinarsCount,
            'meetings_count' => $getMeetingCount,
            'total_sales' => $user->getSaleAmounts(),
            'class_sales'=>$user->classesSaleAmount() ,
            'meeting_sales'=>$user->meetingsSaleAmount()

        ]);
       
    }
 
}
