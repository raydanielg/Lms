<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Setting extends Model implements TranslatableContract
{
    use Translatable;

    //TODO:: To use the site settings, please use the functions of the helper.php file.
    // If you have created new settings, please use the same structure to optimize
    // the number of requests to the database, because this system does not use cache.

    protected $table = 'settings';
    public $timestamps = false;
    protected $guarded = ['id'];


    public $translatedAttributes = ['value'];

    public function getValueAttribute()
    {
        return getTranslateAttributeValue($this, 'value');
    }

    // The result is stored in these variables
    // If you use each function more than once per page, the database will be requested only once.
    static $seoMetas, $socials,
        $footer, $general, $homeSections, $features,
        $financial, $offlineBanks, $referral, $currencySettings,
        $homeHero, $homeHero2, $homeVideoOrImage,
        $pageBackground, $customCssJs,
        $reportReasons, $notificationTemplates,
        $contactPage, $Error404Page, $navbarLink, $panelSidebar, $findInstructors, $rewardProgram, $rewardsSettings, $storeSettings,
        $registrationPackagesGeneral, $registrationPackagesInstructors, $registrationPackagesOrganizations, $becomeInstructorSection,
        $themeColors, $themeFonts, $forumHomeSection, $cookieSettings, $mobileAppSettings, $remindersSettings, $generalSecuritySettings, $advertisingModal,
        $othersPersonalization, $installmentsSettings, $installmentsTermsSettings, $registrationBonusSettings, $registrationBonusTermsSettings, $statisticsSettings,
        $maintenanceSettings, $restrictionSettings, $generalOptions, $giftsGeneralSettings, $aiContentsSettings, $certificateSettings, $abandonedCartSettings, $smsChannelsSettings,
        $commissionSettings;

    // settings name , Using these keys, values are taken from the settings table
    static $seoMetasName = 'seo_metas';
    static $socialsName = 'socials';
    static $footerName = 'footer';
    static $generalName = 'general';
    static $featuresName = 'features';
    static $homeSectionsName = 'home_sections';
    static $financialName = 'financial';
    static $offlineBanksName = 'offline_banks';
    static $referralName = 'referral';
    static $currencySettingsName = 'currency_settings';
    static $commissionSettingsName = 'commission_settings';
    static $homeHeroName = 'home_hero';
    static $homeHeroName2 = 'home_hero2';
    static $homeVideoOrImageName = 'home_video_or_image_box';
    static $pageBackgroundName = 'page_background';
    static $customCssJsName = 'custom_css_js';
    static $reportReasonsName = 'report_reasons';
    static $notificationTemplatesName = 'notifications';
    static $contactPageName = 'contact_us';
    static $Error404PageName = '404';
    static $navbarLinkName = 'navbar_links';
    static $panelSidebarName = 'panel_sidebar';
    static $findInstructorsName = 'find_instructors';
    static $rewardProgramName = 'reward_program';
    static $rewardsSettingsName = 'rewards_settings';
    static $storeSettingsName = 'store_settings';
    static $registrationPackagesGeneralName = 'registration_packages_general';
    static $registrationPackagesInstructorsName = 'registration_packages_instructors';
    static $registrationPackagesOrganizationsName = 'registration_packages_organizations';
    static $becomeInstructorSectionName = 'become_instructor_section';
    static $themeColorsName = 'theme_colors';
    static $themeFontsName = 'theme_fonts';
    static $forumHomeSectionName = 'forums_section';
    static $cookieSettingsName = 'cookie_settings';
    static $mobileAppSettingsName = 'mobile_app';
    static $remindersSettingsName = 'reminders';
    static $generalSecuritySettingsName = 'security';
    static $advertisingModalName = 'advertising_modal';
    static $othersPersonalizationName = 'others_personalization';
    static $installmentsSettingsName = 'installments_settings';
    static $installmentsTermsSettingsName = 'installments_terms_settings';
    static $registrationBonusSettingsName = 'registration_bonus_settings';
    static $registrationBonusTermsSettingsName = 'registration_bonus_terms_settings';
    static $statisticsSettingsName = 'statistics';
    static $maintenanceSettingsName = 'maintenance_settings';
    static $restrictionSettingsName = 'restriction_settings';
    static $generalOptionsName = 'general_options';
    static $giftsGeneralSettingsName = 'gifts_general_settings';
    static $aiContentsSettingsName = 'ai_contents_settings';
    static $abandonedCartSettingsName = 'abandoned_cart_settings';
    static $certificateSettingsName = 'certificate_settings';
    static $smsChannelsSettingName = 'sms_channels';

    //statics
    static $pagesSeoMetas = ['home', 'search', 'tags', 'categories', 'classes', 'login', 'register', 'contact', 'blog', 'certificate_validation',
        'instructors', 'organizations', 'instructor_finder_wizard', 'instructor_finder', 'reward_courses', 'products_lists', 'reward_products',
        'forum', 'upcoming_courses_lists'
    ];
    static $mainSettingSections = ['general', 'financial', 'payment', 'home_hero', 'home_hero2', 'page_background', 'home_video_or_image_box'];
    static $mainSettingPages = ['general', 'financial', 'personalization', 'notifications', 'seo', 'customization', 'other'];

    static $defaultSettingsLocale = 'en'; // Because the settings table uses translation and some settings do not need to be translated, so we save them with a default locale

    static $rootColors = ['primary', "primary-border", "primary-hover", "primary-border-hover",
        "primary-btn-shadow", "primary-btn-shadow-hover", "primary-btn-color", "primary-btn-color-hover",
        'secondary', "secondary-border", "secondary-hover", "secondary-border-hover", "secondary-btn-shadow", "secondary-btn-shadow-hover",
        "secondary-btn-color", "secondary-btn-color-hover"];

    static $rootAdminColors = ['primary'];

    static function getSettingsWithDefaultLocal(): array
    {
        return [
            self::$seoMetasName,
            self::$socialsName,
            self::$generalName,
            self::$financialName,
            self::$offlineBanksName,
            self::$referralName,
            self::$pageBackgroundName,
            self::$homeSectionsName,
            self::$notificationTemplatesName,
            self::$customCssJsName,
            self::$Error404PageName,
            self::$contactPageName,
        ];
    }

    // functions
    static function getSetting(&$static, $name, $key = null)
    {
        if (!isset($static)) {
            $static = cache()->remember('settings.' . $name, 24 * 60 * 60, function () use ($name) {
                return self::where('name', $name)->first();
            });
        }

        $value = [];

        if (!empty($static) and !empty($static->value) and isset($static->value)) {
            $value = json_decode($static->value, true);
        }

        if (!empty($value) and !empty($key)) {
            if (isset($value[$key])) {
                return $value[$key];
            } else {
                return null;
            }
        }

        if (!empty($key) and (empty($value) or count($value) < 1)) {
            return '';
        }

        return $value;
    }

    /**
     * @param null $page => home, search, categories, login, register, about, contact
     * @return array => [title, description]
     */
    static function getSeoMetas($page = null)
    {
        return self::getSetting(self::$seoMetas, self::$seoMetasName, $page);
    }

    /**
     * @return array [title, image, link]
     */
    static function getSocials()
    {
        return self::getSetting(self::$socials, self::$socialsName);
    }


    /**
     * @return array [title, items => [title, link]]
     */
    static function getFooterColumns()
    {
        return self::getSetting(self::$footer, self::$footerName);
    }


    /**
     * @return array [site_name, site_email, site_language, user_languages, rtl_languages, fav_icon, logo, footer_logo, rtl_layout, home hero1 is active, home hero2 is active, content_translate ]
     */
    static function getGeneralSettings($key = null)
    {
        return self::getSetting(self::$general, self::$generalName, $key);
    }

    /**
     * @return array []
     */
    static function getFeaturesSettings($key = null)
    {
        return self::getSetting(self::$features, self::$featuresName, $key);
    }

    /**
     * @return array []
     */
    static function getCookieSettings($key = null)
    {
        return self::getSetting(self::$cookieSettings, self::$cookieSettingsName, $key);
    }


    /**
     * @param $key
     * @return array|[commission, tax, minimum_payout, currency]
     */
    static function getFinancialSettings($key = null)
    {
        return self::getSetting(self::$financial, self::$financialName, $key);
    }

    /**
     * @param $key
     *
     * @return array|string
     */
    static function getFinancialCurrencySettings($key = null)
    {
        return self::getSetting(self::$currencySettings, self::$currencySettingsName, $key);
    }

    /**
     * @param $key
     *
     * @return array|string
     */
    static function getCommissionSettings($key = null)
    {
        return self::getSetting(self::$commissionSettings, self::$commissionSettingsName, $key);
    }


    /**
     * @param string $section
     * @return array|[title, description, hero_background]
     */
    static function getHomeHeroSettings($section = '1')
    {
        if ($section == "2") {
            return self::getSetting(self::$homeHero2, self::$homeHeroName2);
        }

        return self::getSetting(self::$homeHero, self::$homeHeroName);
    }

    /**
     * @return array|[title, description, background]
     */
    static function getHomeVideoOrImageBoxSettings()
    {
        return self::getSetting(self::$homeVideoOrImage, self::$homeVideoOrImageName);
    }


    /**
     * @param null $page => login, register, remember_pass, search, categories, become_instructor, blog, instructors, user_avatar, user_cover
     * @return string|array => [all pages]
     */
    static function getPageBackgroundSettings($page = null)
    {
        return self::getSetting(self::$pageBackground, self::$pageBackgroundName, $page);
    }


    /**
     * @param null $key => css, js
     * @return string|array => {css, js}
     */
    static function getCustomCssAndJs($key = null)
    {
        return self::getSetting(self::$customCssJs, self::$customCssJsName, $key);
    }

    /**
     * @return array
     */
    static function getReportReasons()
    {
        return self::getSetting(self::$reportReasons, self::$reportReasonsName);
    }

    /**
     * @param $template {String|nullable}
     * @return array
     */
    static function getNotificationTemplates($template = null)
    {
        return self::getSetting(self::$notificationTemplates, self::$notificationTemplatesName, $template);
    }

    /**
     * @return array
     */
    static function getOfflineBankSettings($key = null)
    {
        return self::getSetting(self::$offlineBanks, self::$offlineBanksName, $key);
    }

    /**
     * @return array
     */
    static function getReferralSettings()
    {
        return self::getSetting(self::$referral, self::$referralName);
    }

    /**
     * @param $key
     * @return array
     */
    static function getContactPageSettings($key = null)
    {
        return self::getSetting(self::$contactPage, self::$contactPageName, $key);
    }

    /**
     * @param $key
     * @return array
     */
    static function get404ErrorPageSettings($key = null)
    {
        return self::getSetting(self::$Error404Page, self::$Error404PageName, $key);
    }

    /**
     * @param $key
     * @return array
     */
    static function getHomeSectionsSettings($key = null)
    {
        return self::getSetting(self::$homeSections, self::$homeSectionsName, $key);
    }

    /**
     * @param $key
     * @return array
     */
    static function getNavbarLinksSettings($key = null)
    {
        return self::getSetting(self::$navbarLink, self::$navbarLinkName, $key);
    }

    /**
     * @return array
     */
    static function getPanelSidebarSettings()
    {
        return self::getSetting(self::$panelSidebar, self::$panelSidebarName);
    }

    /**
     * @return array
     */
    static function getFindInstructorsSettings()
    {
        return self::getSetting(self::$findInstructors, self::$findInstructorsName);
    }

    /**
     * @return array
     */
    static function getRewardProgramSettings()
    {
        return self::getSetting(self::$rewardProgram, self::$rewardProgramName);
    }

    /**
     * @return array
     */
    static function getRewardsSettings()
    {
        return self::getSetting(self::$rewardsSettings, self::$rewardsSettingsName);
    }

    /**
     * @return array
     */
    static function getStoreSettings($key = null)
    {
        return self::getSetting(self::$storeSettings, self::$storeSettingsName, $key);
    }

    static function getBecomeInstructorSectionSettings()
    {
        return self::getSetting(self::$becomeInstructorSection, self::$becomeInstructorSectionName);
    }

    static function getForumSectionSettings()
    {
        return self::getSetting(self::$forumHomeSection, self::$forumHomeSectionName);
    }

    static function getRegistrationPackagesGeneralSettings($key = null)
    {
        return self::getSetting(self::$registrationPackagesGeneral, self::$registrationPackagesGeneralName, $key);
    }

    static function getRegistrationPackagesInstructorsSettings($key = null)
    {
        return self::getSetting(self::$registrationPackagesInstructors, self::$registrationPackagesInstructorsName, $key);
    }

    static function getRegistrationPackagesOrganizationsSettings($key = null)
    {
        return self::getSetting(self::$registrationPackagesOrganizations, self::$registrationPackagesOrganizationsName, $key);
    }

    static function getThemeColorsSettings()
    {
        return self::getSetting(self::$themeColors, self::$themeColorsName);
    }

    static function getThemeFontsSettings()
    {
        return self::getSetting(self::$themeFonts, self::$themeFontsName);
    }

    static function getMobileAppSettings($key = null)
    {
        return self::getSetting(self::$mobileAppSettings, self::$mobileAppSettingsName, $key);
    }

    static function getRemindersSettings($key = null)
    {
        return self::getSetting(self::$remindersSettings, self::$remindersSettingsName, $key);
    }

    static function getGeneralSecuritySettings($key = null)
    {
        return self::getSetting(self::$generalSecuritySettings, self::$generalSecuritySettingsName, $key);
    }

    static function getAdvertisingModalSettings($key = null)
    {
        return self::getSetting(self::$advertisingModal, self::$advertisingModalName, $key);
    }

    static function getOthersPersonalizationSettings($key = null)
    {
        return self::getSetting(self::$othersPersonalization, self::$othersPersonalizationName, $key);
    }

    static function getInstallmentsSettings($key = null)
    {
        return self::getSetting(self::$installmentsSettings, self::$installmentsSettingsName, $key);
    }

    static function getInstallmentsTermsSettings($key = null)
    {
        return self::getSetting(self::$installmentsTermsSettings, self::$installmentsTermsSettingsName, $key);
    }

    static function getRegistrationBonusSettings($key = null)
    {
        return self::getSetting(self::$registrationBonusSettings, self::$registrationBonusSettingsName, $key);
    }

    static function getRegistrationBonusTermsSettings($key = null)
    {
        return self::getSetting(self::$registrationBonusTermsSettings, self::$registrationBonusTermsSettingsName, $key);
    }

    static function getStatisticsSettings($key = null)
    {
        return self::getSetting(self::$statisticsSettings, self::$statisticsSettingsName, $key);
    }

    static function getMaintenanceSettings($key = null)
    {
        return self::getSetting(self::$maintenanceSettings, self::$maintenanceSettingsName, $key);
    }

    static function getRestrictionSettings($key = null)
    {
        return self::getSetting(self::$restrictionSettings, self::$restrictionSettingsName, $key);
    }

    static function getGeneralOptionsSettings($key = null)
    {
        return self::getSetting(self::$generalOptions, self::$generalOptionsName, $key);
    }

    static function getSMSChannelsSettings($key = null)
    {
        return self::getSetting(self::$smsChannelsSettings, self::$smsChannelsSettingName, $key);
    }

    static function getGiftsGeneralSettings($key = null)
    {
        return self::getSetting(self::$giftsGeneralSettings, self::$giftsGeneralSettingsName, $key);
    }

    static function getAiContentsSettingsName($key = null)
    {
        return self::getSetting(self::$aiContentsSettings, self::$aiContentsSettingsName, $key);
    }

    static function getCertificateMainSettings($key = null)
    {
        return self::getSetting(self::$certificateSettings, self::$certificateSettingsName, $key);
    }

    static function getAbandonedCartSettings($key = null)
    {
        return self::getSetting(self::$abandonedCartSettings, self::$abandonedCartSettingsName, $key);
    }
}
