<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mixins\RegistrationBonus\RegistrationBonusAccounting;
use App\Models\Affiliate;
use App\Models\Verification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VerificationController extends Controller
{
    public function index()
    {
        $verificationId = session()->get('verificationId', null);

        if (!empty($verificationId)) {
            $verification = Verification::where('id', $verificationId)
                ->whereNull('verified_at')
                ->where('expired_at', '>', time())
                ->first();

            if (!empty($verification)) {

                $user = User::find($verification->user_id);

                if (!empty($user) and $user->status != User::$active) {
                    $data = [
                        'pageTitle' => trans('auth.email_confirmation'),
                        'username' => !empty($verification->mobile) ? 'mobile' : 'email',
                        'usernameValue' => !empty($verification->mobile) ? $verification->mobile : $verification->email,
                    ];

                    return view('web.default.auth.verification', $data);
                }
            }
        }

        return redirect('/login');
    }

    public function resendCode()
    {
        $verificationId = session()->get('verificationId', null);

        if (!empty($verificationId)) {
            $verification = Verification::where('id', $verificationId)
                ->whereNull('verified_at')
                ->where('expired_at', '>', time())
                ->first();
            if (!empty($verification)) {
                if (!empty($verification->mobile)) {
                    $verification->sendSMSCode();
                } else {
                    $verification->sendEmailCode();
                }

                return redirect('/verification');
            }
        }

        return redirect('/login');
    }

    public function checkConfirmed($user, $username, $value)
    {
        $disableRegistrationVerificationProcess = getGeneralOptionsSettings('disable_registration_verification_process');

        if (!empty($disableRegistrationVerificationProcess)) {
            return [
                'status' => 'verified'
            ];
        }

        if (!empty($value)) {
            $verification = Verification::where($username, $value)
                ->where('expired_at', '>', time())
                ->where(function ($query) {
                    $query->whereNull('user_id')
                        ->orWhereHas('user');
                })
                ->first();

            $data = [];
            $time = time();

            if (!empty($verification)) {
                if (!empty($verification->verified_at)) {
                    return [
                        'status' => 'verified'
                    ];
                } else {
                    $data['created_at'] = $time;
                    $data['expired_at'] = $time + Verification::EXPIRE_TIME;

                    if (time() > $verification->expired_at) {
                        $data['code'] = $this->getNewCode();
                    } else {
                        $data['code'] = $verification->code;
                    }
                }
            } else {
                $data[$username] = $value;
                $data['code'] = $this->getNewCode();
                $data['user_id'] = !empty($user) ? $user->id : (auth()->check() ? auth()->id() : null);
                $data['created_at'] = $time;
                $data['expired_at'] = $time + Verification::EXPIRE_TIME;
            }

            $data['verified_at'] = null;

            $verification = Verification::updateOrCreate([$username => $value], $data);

            session()->put('verificationId', $verification->id);

            if ($username == 'mobile') {
                $verification->sendSMSCode();
            } else {
                $verification->sendEmailCode();
            }

            return [
                'status' => 'send'
            ];
        }

        abort(404);
    }

    public function confirmCode(Request $request)
    {
        $value = $request->get('username');
        $code = $request->get('code');
        $username = $this->username($value);
        $request[$username] = $value;
        $time = time();

        Verification::where($username, $value)
            ->whereNull('verified_at')
            ->where('code', $code)
            ->where('created_at', '>', $time - 24 * 60 * 60)
            ->update([
                'verified_at' => $time,
                'expired_at' => $time + 50,
            ]);

        $rules = [
            'code' => [
                'required',
                Rule::exists('verifications')->where(function ($query) use ($value, $code, $time, $username) {
                    $query->where($username, $value)
                        ->where('code', $code)
                        ->whereNotNull('verified_at')
                        ->where('expired_at', '>', $time);
                }),
            ],
        ];

        if ($username == 'mobile') {
            $rules['mobile'] = 'required';
            $value = ltrim($value, '+');
        } else {
            $rules['email'] = 'required|email';
        }

        $this->validate($request, $rules, [], [
            'mobile' => trans('auth.mobile'),
            'email' => trans('auth.email'),
            'code' => trans('auth.code'),
        ]);

        $authUser = auth()->check() ? auth()->user() : null;

        $referralCode = session()->get('referralCode', null);

        if (empty($authUser)) {
            $authUser = User::where($username, $value)
                ->first();

            $loginController = new LoginController();

            if (!empty($authUser)) {
                if (\Auth::loginUsingId($authUser->id)) {

                    if (!empty($referralCode)) {
                        Affiliate::storeReferral($authUser, $referralCode);
                    }

                    $enableRegistrationBonus = false;
                    $registrationBonusAmount = null;
                    $registrationBonusSettings = getRegistrationBonusSettings();
                    if (!empty($registrationBonusSettings['status']) and !empty($registrationBonusSettings['registration_bonus_amount'])) {
                        $enableRegistrationBonus = true;
                        $registrationBonusAmount = $registrationBonusSettings['registration_bonus_amount'];
                    }

                    $authUser->update([
                        'enable_registration_bonus' => $enableRegistrationBonus,
                        'registration_bonus_amount' => $registrationBonusAmount,
                    ]);

                    $registrationBonusAccounting = new RegistrationBonusAccounting();
                    $registrationBonusAccounting->storeRegistrationBonusInstantly($authUser);

                    return $loginController->afterLogged($request, true);
                }
            }

            return $loginController->sendFailedLoginResponse($request);
        }
    }

    private function username($value)
    {
        $username = 'email';
        $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

        if (preg_match($email_regex, $value)) {
            $username = 'email';
        } elseif (is_numeric($value)) {
            $username = 'mobile';
        }

        return $username;
    }

    private function getNewCode()
    {
        return rand(10000, 99999);
    }
}
