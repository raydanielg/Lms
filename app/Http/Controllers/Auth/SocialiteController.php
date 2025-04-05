<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\User;

class SocialiteController extends Controller
{

    public function __construct()
    {
        $settings = getFeaturesSettings();

        \Config::set('services.google.client_id', !empty($settings['google_client_id']) ? $settings['google_client_id'] : '');
        \Config::set('services.google.client_secret', !empty($settings['google_client_secret']) ? $settings['google_client_secret'] : '');
        \Config::set('services.google.redirect', url("/google/callback"));

        \Config::set('services.facebook.client_id', !empty($settings['facebook_client_id']) ? $settings['facebook_client_id'] : '');
        \Config::set('services.facebook.client_secret', !empty($settings['facebook_client_secret']) ? $settings['facebook_client_secret'] : '');
        \Config::set('services.facebook.redirect', url("/facebook/callback"));

    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $account = Socialite::driver('google')->user();

            $user = User::where('google_id', $account->id)
                ->orWhere('email', $account->email)
                ->first();

            if (empty($user)) {
                $user = User::create([
                    'full_name' => $account->name,
                    'email' => $account->email,
                    'google_id' => $account->id,
                    'role_id' => Role::getUserRoleId(),
                    'role_name' => Role::$user,
                    'status' => User::$active,
                    'verified' => false,
                    'created_at' => time(),
                    'password' => null
                ]);
            } else {
                $checkLoginDeviceLimit = $this->checkLoginDeviceLimit($user);

                if ($checkLoginDeviceLimit != "ok") {
                    Auth::logout();

                    $request->session()->flush();
                    $request->session()->regenerate();

                    return $this->sendMaximumActiveSessionResponse();
                }
            }

            $user->update([
                'google_id' => $account->id,
            ]);

            Auth::login($user);

            return redirect('/');
        } catch (Exception $e) {
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('auth.fail_login_by_google'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }
    }

    /**
     * Create a redirect method to facebook api.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Return a callback method from facebook api.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleFacebookCallback(Request $request)
    {
        try {
            $account = Socialite::driver('facebook')->user();

            $user = User::where('facebook_id', $account->id)->first();

            if (empty($user)) {
                $user = User::create([
                    'full_name' => $account->name,
                    'email' => $account->email,
                    'facebook_id' => $account->id,
                    'role_id' => Role::getUserRoleId(),
                    'role_name' => Role::$user,
                    'status' => User::$active,
                    'verified' => false,
                    'created_at' => time(),
                    'password' => null
                ]);
            } else {
                $checkLoginDeviceLimit = $this->checkLoginDeviceLimit($user);

                if ($checkLoginDeviceLimit != "ok") {
                    Auth::logout();

                    $request->session()->flush();
                    $request->session()->regenerate();

                    return $this->sendMaximumActiveSessionResponse();
                }
            }

            Auth::login($user);
            return redirect('/');
        } catch (Exception $e) {
            $toastData = [
                'title' => trans('public.request_failed'),
                'msg' => trans('auth.fail_login_by_facebook'),
                'status' => 'error'
            ];
            return back()->with(['toast' => $toastData]);
        }
    }


    private function checkLoginDeviceLimit($user)
    {
        $securitySettings = getGeneralSecuritySettings();

        if (!empty($securitySettings) and !empty($securitySettings['login_device_limit'])) {
            $limitCount = !empty($securitySettings['number_of_allowed_devices']) ? $securitySettings['number_of_allowed_devices'] : 1;

            $count = $user->logged_count;

            if ($count >= $limitCount) {
                return "no";
            }
        }

        return 'ok';
    }

    protected function sendMaximumActiveSessionResponse()
    {
        $toastData = [
            'title' => trans('update.login_failed'),
            'msg' => trans('update.device_limit_reached_please_try_again'),
            'status' => 'error'
        ];

        return redirect('/login')->with(['login_failed_active_session' => $toastData]);
    }

}
