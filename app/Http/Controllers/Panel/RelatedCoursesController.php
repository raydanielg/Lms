<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\RelatedCourse;
use Illuminate\Http\Request;
use Validator;

class RelatedCoursesController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'item_id' => 'required',
            'item_type' => 'required|in:webinar,bundle,product,upcomingCourse',
            'course_id' => 'required|exists:webinars,id',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $type = $this->getTargetType($data);

        RelatedCourse::query()->updateOrCreate([
            'creator_id' => $user->id,
            'targetable_id' => $data['item_id'],
            'targetable_type' => $type,
            'course_id' => $data['course_id']
        ], [
            'order' => null,
        ]);

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'item_id' => 'required',
            'item_type' => 'required|in:webinar,bundle,product,upcomingCourse',
            'course_id' => 'required|exists:webinars,id',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $type = $this->getTargetType($data);

        $item = RelatedCourse::query()
            ->where('creator_id', $user->id)
            ->where('targetable_id', $data['item_id'])
            ->where('targetable_type', $type)
            ->where('id', $id)
            ->first();

        if (!empty($item)) {
            $item->update([
                'course_id' => $data['course_id']
            ]);
        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        $item = RelatedCourse::query()
            ->where('creator_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!empty($item)) {
            $item->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
    }

    private function getTargetType($data)
    {
        $type = null;

        switch ($data['item_type']) {
            case 'webinar':
                $type = "App\Models\Webinar";
                break;

            case 'bundle':
                $type = "App\Models\Bundle";
                break;

            case 'product':
                $type = "App\Models\Product";
                break;

            case 'upcomingCourse':
                $type = "App\Models\UpcomingCourse";
                break;
        }

        return $type;
    }

}
