<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mixins\Logs\UserLoginHistoryMixin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this->redirectTo = getAdminPanelUrl();
    }

    public function showLoginForm()
    {
        $data = [
            'pageTitle' => trans('auth.login'),
        ];


        return view('admin.auth.login', $data);
    }

    /**
     * Check either username or email.
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Validate the user login.
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
                'email' => 'required|email|exists:users,email,status,active',
                'password' => 'required|min:4',
            ]
        );
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $request->session()->put('login_error', trans('auth.failed'));
        throw ValidationException::withMessages(
            [
                'error' => [trans('auth.failed')],
            ]
        );
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users,email,status,active',
            'password' => 'required|min:4',
        ];

        if (!empty(getGeneralSecuritySettings('captcha_for_admin_login'))) {
            $rules['captcha'] = 'required|captcha';
        }

        // validate the form data
        $this->validate($request, $rules);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $user = auth()->user();

            if (!empty($user)) {
                $userLoginHistoryMixin = new UserLoginHistoryMixin();
                $userLoginHistoryMixin->storeUserLoginHistory($user);
            }

            return Redirect::to(getAdminPanelUrl());
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors([
            'password' => 'Wrong password or this account not approved yet.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        $userLoginHistoryMixin = new UserLoginHistoryMixin();
        $userLoginHistoryMixin->storeUserLogoutHistory($user->id);

        Auth::logout();
        return redirect(getAdminPanelUrl() . '/login');
    }
}
