<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Translation\WebinarExtraDescriptionTranslation;
use App\Models\UpcomingCourse;
use App\Models\Webinar;
use App\Models\WebinarExtraDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebinarExtraDescriptionController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'type' => 'required|in:' . implode(',', WebinarExtraDescription::$types),
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $canStore = $this->checkItem($user, $data);

        if ($canStore) {
            $columnName = !empty($data['webinar_id']) ? 'webinar_id' : 'upcoming_course_id';
            $columnValue = !empty($data['webinar_id']) ? $data['webinar_id'] : $data['upcoming_course_id'];

            $order = WebinarExtraDescription::query()
                    ->where($columnName, $columnValue)
                    ->where('type', $data['type'])
                    ->count() + 1;

            $webinarExtraDescription = WebinarExtraDescription::create([
                'creator_id' => $user->id,
                'webinar_id' => !empty($data['webinar_id']) ? $data['webinar_id'] : null,
                'upcoming_course_id' => !empty($data['upcoming_course_id']) ? $data['upcoming_course_id'] : null,
                'type' => $data['type'],
                'order' => $order,
                'created_at' => time()
            ]);

            if (!empty($webinarExtraDescription)) {
                WebinarExtraDescriptionTranslation::updateOrCreate([
                    'webinar_extra_description_id' => $webinarExtraDescription->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'value' => $data['value'],
                ]);
            }

            return response()->json([
                'code' => 200,
            ], 200);
        }

        abort(403);
    }

    private function checkItem($user, $data)
    {
        $canStore = false;

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::find($data['webinar_id']);

            if (!empty($webinar) and $webinar->canAccess($user)) {
                $canStore = true;
            }
        } elseif (!empty($data['upcoming_course_id'])) {
            $upcomingCourse = UpcomingCourse::find($data['upcoming_course_id']);

            if (!empty($upcomingCourse) and $upcomingCourse->canAccess($user)) {
                $canStore = true;
            }
        }

        return $canStore;
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'type' => 'required|in:' . implode(',', WebinarExtraDescription::$types),
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $canStore = $this->checkItem($user, $data);

        if ($canStore) {
            $columnName = !empty($data['webinar_id']) ? 'webinar_id' : 'upcoming_course_id';
            $columnValue = !empty($data['webinar_id']) ? $data['webinar_id'] : $data['upcoming_course_id'];

            $webinarExtraDescription = WebinarExtraDescription::where('id', $id)
                ->where(function ($query) use ($user, $columnName, $columnValue) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere($columnName, $columnValue);
                })
                ->first();

            if (!empty($webinarExtraDescription)) {

                WebinarExtraDescriptionTranslation::updateOrCreate([
                    'webinar_extra_description_id' => $webinarExtraDescription->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'value' => $data['value'],
                ]);

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        abort(403);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        $webinarExtraDescription = WebinarExtraDescription::where('id', $id)
            ->first();

        if (!empty($webinarExtraDescription)) {
            $item = null;
            if (!empty($webinarExtraDescription->webinar_id)) {
                $item = Webinar::query()->find($webinarExtraDescription->webinar_id);
            } else if (!empty($webinarExtraDescription->upcoming_course_id)) {
                $item = UpcomingCourse::find($webinarExtraDescription->upcoming_course_id);
            }

            if ($webinarExtraDescription->creator_id == $user->id or (!empty($item) and $item->canAccess($user))) {
                $webinarExtraDescription->delete();
            }
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
