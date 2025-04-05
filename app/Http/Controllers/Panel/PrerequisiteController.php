<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Prerequisite;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Validator;

class PrerequisiteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'prerequisite_id' => 'required|unique:prerequisites,prerequisite_id,null,id,webinar_id,' . $data['webinar_id'],
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($webinar) and $webinar->canAccess()) {

            $required = (!empty($data['required']) and $data['required'] == 'on') ? true : false;

            Prerequisite::create([
                'webinar_id' => $data['webinar_id'],
                'prerequisite_id' => $data['prerequisite_id'],
                'required' => $required,
                'created_at' => time()
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
            'webinar_id' => 'required',
            'prerequisite_id' => 'required|unique:prerequisites,prerequisite_id,' . $id . ',id,webinar_id,' . $data['webinar_id'],
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($webinar) and $webinar->canAccess($user)) {

            $required = (!empty($data['required']) and $data['required'] == 'on') ? true : false;

            $webinarIds = $user->webinars()->pluck('id')->toArray();

            if (in_array($data['webinar_id'], $webinarIds)) {
                $prerequisite = Prerequisite::where('id', $id)
                    ->where('webinar_id', $data['webinar_id'])
                    ->first();

                if (!empty($prerequisite)) {
                    $prerequisite->update([
                        'webinar_id' => $data['webinar_id'],
                        'prerequisite_id' => $data['prerequisite_id'],
                        'required' => $required,
                        'updated_at' => time()
                    ]);

                    return response()->json([
                        'code' => 200,
                    ], 200);
                }
            }
        }

        abort(403);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        $webinarIds = $user->webinars()->pluck('id')->toArray();

        $prerequisite = Prerequisite::where('id', $id)
            ->whereIn('webinar_id', $webinarIds)
            ->first();

        if (!empty($prerequisite)) {
            $prerequisite->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
