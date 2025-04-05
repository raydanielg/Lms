<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelatedCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RelatedCoursesController extends Controller
{

    public function getForm(Request $request, $id = null)
    {
        $relatedCourse = null;

        if (!empty($id)) {
            $relatedCourse = RelatedCourse::query()->findOrFail($id);
        }

        $data = [
            'itemId' => $request->get('item'),
            'itemType' => $request->get('item_type'),
            'relatedCourse' => $relatedCourse,
        ];

        $html = (string)view()->make("admin.webinars.relatedCourse.related_course_modal", $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

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
            'creator_id' => null,
            'targetable_id' => $data['item_id'],
            'targetable_type' => $type,
            'course_id' => $data['course_id']
        ], [
            'order' => null,
        ]);

        return response()->json([
            'code' => 200,
            'title' => trans('update.saved_successfully')
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

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
            'title' => trans('update.saved_successfully')
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $item = RelatedCourse::query()
            ->where('id', $id)
            ->first();

        if (!empty($item)) {
            $item->delete();
        }

        return redirect()->back();
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
