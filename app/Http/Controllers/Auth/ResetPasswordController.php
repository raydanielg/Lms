<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function showResetForm(Request $request, $token)
    {
        $updatePassword = DB::table('password_resets')
            ->where(['email' => $request->email, 'token' => $token])
            ->first();

        if (!empty($updatePassword)) {
            return view(getTemplate() . '.auth.reset_password', ['token' => $token]);
        }

        abort(404);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [],[
            'email' => trans('auth.email'),
            'password' => trans('auth.password'),
            'password_confirmation' => trans('auth.password_repeat'),
        ]);

        $data = $request->all();

        $updatePassword = DB::table('password_resets')
            ->where(['email' => $data['email'], 'token' => $data['token']])
            ->first();

        if (!empty($updatePassword)) {
            $user = User::where('email', $data['email'])
                ->update(['password' => Hash::make($data['password'])]);

            DB::table('password_resets')->where(['email' => $data['email']])->delete();

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('auth.reset_password_success'),
                'status' => 'success'
            ];
            return redirect('/login')->with(['toast' => $toastData]);
        }

        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('auth.reset_password_token_invalid'),
            'status' => 'error'
        ];
        return back()->withInput()->with(['toast' => $toastData]);
    }
}
