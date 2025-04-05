<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;


class ForgotPasswordController extends Controller
{

    public function sendEmail(Request $request)
    {
        validateParam($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        $token = \Illuminate\Support\Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $request->input('email'),
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $generalSettings = getGeneralSettings();
        $emailData = [
            'token' => $token,
            'generalSettings' => $generalSettings,
            'email' => $request->input('email')
        ];
        try {
            Mail::send('web.default.auth.password_verify', $emailData, function ($message) use ($request) {
                $message->from(!empty($generalSettings['site_email']) ? $generalSettings['site_email'] : env('MAIL_FROM_ADDRESS'));
                $message->to($request->input('email'));
                $message->subject('Reset Password Notification');
            });


            return apiResponse2(1, 'done',trans('auth.forget_password'));

        } catch (\Exception  $e) {
             return apiResponse2(0, 'failure',trans('auth.forget_password_failure'));

        }
    }
}
