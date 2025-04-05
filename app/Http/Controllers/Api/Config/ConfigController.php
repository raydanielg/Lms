<?php

namespace App\Http\Controllers\Api\Config;

use App\Api\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\traits\UserFormFieldsTrait;
use App\Models\PaymentChannel;
use Illuminate\Http\Request as HttpRequest;

class ConfigController extends Controller
{
    use UserFormFieldsTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function list(Request $request)
    {
        return self::get();
    }

    public static function get()
    {
        $registerMethod = getGeneralSettings('register_method') ?? 'mobile';

        $userLanguages = getGeneralSettings('user_languages');
        if (!empty($userLanguages) and is_array($userLanguages)) {
            $userLanguages = getLanguages($userLanguages);
        } else {
            $userLanguages = [];
        }
        $paymentChannels = PaymentChannel::all()->groupBy('status');
        $getFinancialSettings = getFinancialSettings() ['minimum_payout'];
        $currency = [
            'sign' => currencySign(),
            'name' => currency()
        ];
        $showOtherRegisterMethod = getFeaturesSettings('show_other_register_method') ?? false;

        $selectRolesDuringRegistration = getFeaturesSettings('select_the_role_during_registration') ?? null;

        $data = [
            'register_method' => $registerMethod,
            'selectRolesDuringRegistration' => $selectRolesDuringRegistration,
            'offline_bank_account' => getOfflineBanksTitle() ?? null,
            'user_language' => $userLanguages,
            'payment_channels' => $paymentChannels,
            'minimum_payout_amount' => $getFinancialSettings,
            'currency' => $currency,
            'price_display' => getFinancialSettings('price_display') ?? 'only_price',
            'currency_position' => getFinancialSettings('currency_position'),
            'currency_decimal' => getFinancialCurrencySettings('currency_decimal'),
            'forum_settings' => getForumSectionSettings(),
            'course_forum_status' => getFeaturesSettings("course_forum_status"),
            'show_google_login_button' => !empty(getFeaturesSettings('show_google_login_button')),
            'show_facebook_login_button' => !empty(getFeaturesSettings('show_facebook_login_button')),
            'showOtherRegisterMethod' => $showOtherRegisterMethod,
            'webinar_private_content_status' => getFeaturesSettings('webinar_private_content_status'),
            'sequence_content_status' => getFeaturesSettings('sequence_content_status'),
            'course_notes_status' => getFeaturesSettings('course_notes_status'),
            'course_notes_attachment' => getFeaturesSettings('course_notes_attachment'),
        ];
        return $data;

    }
    public function getRegisterConfig(HttpRequest $request,$type)
    {
        $registerMethod = getGeneralSettings('register_method') ?? 'mobile';
        $userLanguages = getGeneralSettings('user_languages');
        if (!empty($userLanguages) and is_array($userLanguages)) {
            $userLanguages = getLanguages($userLanguages);
        } else {
            $userLanguages = [];
        }
        $showOtherRegisterMethod = getFeaturesSettings('show_other_register_method') ?? false;
        $referralSettings = getReferralSettings();
        $formFields = $this->getFormFieldsByType($type);
        $showCertificateAdditionalInRegister = getFeaturesSettings('show_certificate_additional_in_register') ?? false;
        $selectRolesDuringRegistration = getFeaturesSettings('select_the_role_during_registration') ?? null;
        $selectedTimezone = getGeneralSettings('default_time_zone');
        $config = [
            'selectedTimezone' => $selectedTimezone,
            'selectRolesDuringRegistration' => $selectRolesDuringRegistration,
            'showCertificateAdditionalInRegister' => $showCertificateAdditionalInRegister,
            'showOtherRegisterMethod' => $showOtherRegisterMethod,
            'referralSettings' => $referralSettings,
            'formFields' => $formFields,
            'register_method' => $registerMethod,
            'user_language' => $userLanguages,
            'show_google_login_button' => !empty(getFeaturesSettings('show_google_login_button')),
            'show_facebook_login_button' => !empty(getFeaturesSettings('show_facebook_login_button'))
        ];
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),
            $config
        );
    }


}
