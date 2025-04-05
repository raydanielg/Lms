<?php

namespace App\Http\Controllers\Api\Auth;

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

    //   use ResetsPasswords;

    public function updatePassword(Request $request,$token)
    {

        validateParam($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $data = $request->all();

        $updatePassword = DB::table('password_resets')
         //   ->where(['email' => $data['email'], 'token' => $data['token']])
            ->where(['email' => $data['email'], 'token' => $token])
            ->first();

        if (!empty($updatePassword)) {
            $user = User::where('email', $data['email'])
                ->update(['password' => Hash::make($data['password'])]);
            DB::table('password_resets')->where(['email' => $data['email']])->delete();

           return apiResponse(1, 'password reset.');
        }
        return apiResponse(1, 'there is not such request to reset password');

    }
}
