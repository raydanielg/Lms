<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebinarAssignmentHistoryResource;
use App\Http\Resources\WebinarAssignmentResource;
use App\Models\Sale;
use App\Models\Webinar;
use App\Models\Api\WebinarAssignment;
use App\Models\Api\WebinarAssignmentHistory;
use App\Models\WebinarAssignmentHistoryMessage;
use App\Models\WebinarChapter;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        if (!getFeaturesSettings('webinar_assignment_status')) {
            abort(403);
        }

        $user = apiAuth();

        $purchasedCoursesIds = Sale::where('buyer_id', $user->id)
            ->whereNotNull('webinar_id')
            ->whereNull('refund_at')
            ->pluck('webinar_id')
            ->toArray();


        $query = WebinarAssignment::whereIn('webinar_id', $purchasedCoursesIds)
            ->where('status', 'active')
            ->with(['assignmentHistory' => function ($d) use ($user) {
                $d->where('student_id', $user->id);
            }]);


        $assignments = $query->handleFilters()->orderBy('created_at', 'desc')
            ->get()->map(function ($assignment) use ($user) {
                //  dd($assignment->assignmentHistory->where('student_id', $user->id)->get()) ;
                return $assignment->assignmentHistory;
            });
        //dd($assignments);

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            [

                'assignments' => WebinarAssignmentHistoryResource::collection($assignments),

            ]);

    }

    public function show($id)
    {
        $user = apiAuth();
        $assignmnet = WebinarAssignment::where('id', $id)
            /*  ->where(function ($q) use ($user) {
                  $q->whereHas('assignmentHistory', function ($q) use ($user) {
                      $q->where('student_id', $user->id);
                  });
              })*/
            ->with(['assignmentHistory' => function ($d) use ($user) {
                $d->where('student_id', $user->id);
            }])
            ->where('status', WebinarChapter::$chapterActive)->first();
        abort_unless($assignmnet, 404);

        $purchasedCoursesIds = Sale::where('buyer_id', $user->id)
            ->whereNotNull('webinar_id')
            ->whereNull('refund_at')
            ->pluck('webinar_id')
            ->toArray();
        if (!in_array($assignmnet->webinar->id,$purchasedCoursesIds)){
            abort(404);
    }

        if ($error = $assignmnet->canViewError()) {
            //       return $this->failure($error, 403, 403);
        }
        $resource = new WebinarAssignmentHistoryResource($assignmnet->assignmentHistory);
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $resource);
    }


}
