<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WaitlistController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->all();

        $rules = [
            'slug' => 'required|exists:webinars,slug'
        ];

        if (empty($user)) {
            $rules['name'] = 'required|string';
            $rules['email'] = 'required|email';
            $rules['phone'] = 'required';
            $rules['captcha'] = 'required|captcha';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        $webinar = Webinar::query()->where('slug', $data['slug'])->first();

        if (!empty($webinar)) {
            $userId = !empty($user) ? $user->id : null;
            $fullName = $data['name'] ?? null;
            $email = $data['email'] ?? null;
            $phone = $data['phone'] ?? null;

            Waitlist::query()->updateOrCreate([
                'webinar_id' => $webinar->id,
                'user_id' => $userId,
                'email' => $email,
                'phone' => $phone
            ], [
                'full_name' => $fullName,
                'created_at' => time()
            ]);

            $notifyOptions = [
                '[c.title]' => $webinar->title,
                '[u.name]' => !empty($fullName) ? $fullName : (!empty($user) ? $user->full_name : 'User'),
            ];

            sendNotification("waitlist_submission_for_admin", $notifyOptions, 1);

            if (!empty($user)) {
                sendNotification("waitlist_submission", $notifyOptions, $user->id);
            } else {
                sendNotificationToEmail("waitlist_submission", $notifyOptions, $email);
            }

            return response()->json([
                'code' => 200,
                'msg' => trans('update.course_added_to_waitlists_successful')
            ]);
        }

        abort(404);
    }
}
