<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\BundleWebinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BundleWebinarsController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'bundle_id' => 'required',
            'webinar_id' => 'required|exists:webinars,id',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bundle = Bundle::find($data['bundle_id']);

        if (!empty($bundle) and $bundle->canAccess()) {
            $check = BundleWebinar::where('creator_id', $user->id)
                ->where('webinar_id', $data['webinar_id'])
                ->first();

            if (!empty($check)) {
                return response([
                    'code' => 422,
                    'errors' => [
                        'webinar_id' => [trans('update.this_course_has_been_used_before')]
                    ],
                ], 422);
            }

            BundleWebinar::create([
                'creator_id' => $user->id,
                'bundle_id' => $data['bundle_id'],
                'webinar_id' => $data['webinar_id'],
            ]);

            return response()->json([
                'code' => 200,
            ], 200);
        }

        abort(403);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'bundle_id' => 'required',
            'webinar_id' => 'required|exists:webinars,id',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $bundle = Bundle::find($data['bundle_id']);

        if (!empty($bundle) and $bundle->canAccess($user)) {
            $check = BundleWebinar::where('creator_id', $user->id)
                ->where('id', '!=', $id)
                ->where('webinar_id', $data['webinar_id'])
                ->first();

            if (!empty($check)) {
                return response([
                    'code' => 422,
                    'errors' => [
                        'webinar_id' => [trans('update.this_course_has_been_used_before')]
                    ],
                ], 422);
            }

            $bundleWebinar = BundleWebinar::where('id', $id)
                ->where('creator_id', $user->id)
                ->where('bundle_id', $data['bundle_id'])
                ->first();

            if (!empty($bundleWebinar)) {
                $bundleWebinar->update([
                    'bundle_id' => $data['bundle_id'],
                    'webinar_id' => $data['webinar_id'],
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

        $bundleWebinar = BundleWebinar::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

        if (!empty($bundleWebinar)) {
            $bundle = Bundle::where('id', $bundleWebinar->bundle_id)
                ->where(function ($query) use ($user) {
                    $query->where('creator_id', $user->id)
                        ->orWhere('teacher_id', $user->id);
                })
                ->first();

            if (!empty($bundle)) {
                $bundleWebinar->delete();
            }
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
