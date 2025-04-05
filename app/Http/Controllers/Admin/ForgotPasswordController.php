<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Psy\Util\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        $data = [
            'pageTitle' => trans('auth.forget_password'),
        ];

        return view('admin.auth.forgot_password', $data);
    }

    public function forgot(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users',
        ];

        if (!empty(getGeneralSecuritySettings('captcha_for_admin_forgot_pass'))) {
            $rules['captcha'] = 'required|captcha';
        }

        $request->validate($rules);

        $adminRoleIds = Role::where('is_admin', true)->pluck('id')->toArray();
        $user = User::where('email', $request->get('email'))->first();

        if (empty($user) or !in_array($user->role_id, $adminRoleIds)) {
            return back()->withErrors([
                'email' => [trans('validation.exists', ['attribute' => trans('auth.email')])]
            ]);
        }

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

        Mail::send('admin.auth.password_verify', $emailData, function ($message) use ($request) {
            $message->from(!empty($generalSettings['site_email']) ? $generalSettings['site_email'] : env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->to($request->input('email'));
            $message->subject('Reset Password Notification');
        });

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('auth.send_email_for_reset_password'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
