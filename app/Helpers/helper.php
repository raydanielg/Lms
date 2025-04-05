<?php

use App\Mixins\Financial\MultiCurrency;
use Illuminate\Support\Facades\Cookie;

function getTemplate()
{
    /*$template = cache()->remember('view.template', 7 * 24 * 60 * 60, function () {
        return \App\Models\ViewTemplate::where('status', true)->first();
    });*/
    if (!empty($template) and $template->count() > 0) {
        return 'web.' . $template->folder;
    }
    return 'web.default';
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

/**
 * @param $timestamp
 * @param string $format
 *
 * // Use this format everywhere : j:day , M:month, Y:year, H:hour, i:minute => {j M Y} or {j M Y H:i}
 * */
function dateTimeFormat($timestamp, $format = 'H:i', $useAdminSetting = true, $applyTimezone = true, $timezone = null)
{
    if ($applyTimezone and empty($timezone)) {
        $timezone = getTimezone();
    }

    if ($useAdminSetting) {
        $format = handleDateAndTimeFormat($format);
    }

    if (empty($timezone)) {
        $timezone = "UTC";
    }

    $carbon = (new Carbon\Carbon())
        ->setTimezone($timezone)
        ->setTimestamp($timestamp);

    return $useAdminSetting ? $carbon->translatedFormat($format) : $carbon->format($format);
}

function dateTimeFormatForHumans($timestamp, $applyTimezone = true, $timezone = "UTC", $parts = 3)
{
    if ($applyTimezone) {
        $timezone = getTimezone();
    }

    if (empty($timezone)) {
        $timezone = "UTC";
    }

    $carbon = (new Carbon\Carbon())
        ->setTimezone($timezone)
        ->setTimestamp($timestamp);

    return $carbon->diffForHumans(null, null, false, $parts);
}

function getTimezone()
{
    $timezone = getGeneralSettings('default_time_zone');

    if (auth()->check() or apiAuth()) {
        $user = auth()->user();

        if (!$user) {
            $user = apiAuth();
        }

        if (!empty($user) and !empty($user->timezone)) {
            $timezone = $user->timezone;
        }
    }

    return $timezone;
}

function handleDateAndTimeFormat($format)
{
    $dateFormat = getGeneralSettings('date_format') ?? 'textual';
    $timeFormat = getGeneralSettings('time_format') ?? '24_hours';

    if ($dateFormat == 'numerical') {
        $format = str_replace('M', 'm', $format);
        $format = str_replace('j ', 'j/', $format);
        $format = str_replace('m ', 'm/', $format);
    } else {
        $format = str_replace('m', 'M', $format);
    }

    if ($timeFormat == '12_hours') {
        $format = str_replace('H', 'h', $format);

        if (strpos($format, 'h')) {
            $format .= ' a';
        }
    } else {
        $format = str_replace('h', 'H', $format);
        $format = str_replace('a', '', $format);
    }

    return $format;
}

function diffTimestampDay($firstTime, $lastTime)
{
    return ($firstTime - $lastTime) / (24 * 60 * 60);
}

function convertMinutesToHourAndMinute($minutes)
{
    return intdiv($minutes, 60) . ':' . (str_pad($minutes % 60, 2, 0, STR_PAD_LEFT));
}

function getListOfTimezones()
{
    return DateTimeZone::listIdentifiers();
}

function toGmtOffset($timezone): string
{
    $userTimeZone = new DateTimeZone($timezone);
    $offset = $userTimeZone->getOffset(new DateTime("now", new DateTimeZone('GMT'))); // Offset in seconds
    $seconds = abs($offset);
    $sign = $offset > 0 ? '+' : '-';
    $hours = floor($seconds / 3600);
    $mins = floor($seconds / 60 % 60);
    $secs = floor($seconds % 60);
    return sprintf("GMT $sign%02d:%02d", $hours, $mins, $secs);
}

//this function convert string to UTC time zone
function convertTimeToUTCzone($str, $userTimezone = null, $format = false)
{
    if (empty($userTimezone)) {
        $userTimezone = getTimezone();
    }

    $new_str = new DateTime($str, new DateTimeZone($userTimezone));

    $new_str->setTimeZone(new DateTimeZone('UTC'));

    if ($format) {
        return $new_str->format("Y-m-d H:i");
    }

    return $new_str;
}

function x_week_range()
{
    $start = strtotime(date('Y-m-d', strtotime("last Saturday")));
    return array(
        $start,
        strtotime(date('Y-m-d', strtotime('next Friday', $start)))
    );
}

function getTimeByDay($title)
{
    $start = date('Y-m-d', strtotime("last Saturday"));
    $time = 0;
    switch ($title) {
        case "saturday":
            $time = strtotime(date('Y-m-d', strtotime($start)));
            break;
        case "sunday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+1 days")));
            break;
        case "monday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+2 days")));
            break;
        case "tuesday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+3 days")));
            break;
        case "wednesday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+4 days")));
            break;
        case "thursday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+5 days")));
            break;
        case "friday":
            $time = strtotime(date('Y-m-d', strtotime($start . "+6 days")));
            break;
    }
    return $time;
}

function convertDayToNumber($times)
{
    $numbers = [
        'sunday' => 1,
        'monday' => 2,
        'tuesday' => 3,
        'wednesday' => 4,
        'thursday' => 5,
        'friday' => 6,
        'saturday' => 7
    ];

    $numberDay = [];

    foreach ($times as $day => $time) {
        $numberDay[] = $numbers[$day];
    }

    return $numberDay;
}

function getBindedSQL($query)
{
    $fullQuery = $query->toSql();
    $replaces = $query->getBindings();
    foreach ($replaces as $replace) {
        $fullQuery = Str::replaceFirst('?', $replace, $fullQuery);
    }

    return $fullQuery;
}

function getUserLanguagesLists()
{
    $generalSettings = getGeneralSettings();
    $userLanguages = ($generalSettings and !empty($generalSettings['user_languages'])) ? $generalSettings['user_languages'] : null;

    if (!empty($userLanguages) and is_array($userLanguages)) {
        $userLanguages = getLanguages($userLanguages);
    } else {
        $userLanguages = [];
    }

    if (count($userLanguages) > 0) {
        foreach ($userLanguages as $locale => $language) {
            if (mb_strtolower($locale) == mb_strtolower(app()->getLocale())) {
                $firstKey = array_key_first($userLanguages);

                if ($firstKey != $locale) {
                    $firstValue = $userLanguages[$firstKey];

                    unset($userLanguages[$locale]);
                    unset($userLanguages[$firstKey]);

                    $userLanguages = array_merge([
                        $locale => $language,
                        $firstKey => $firstValue
                    ], $userLanguages);
                }
            }
        }
    }

    return $userLanguages;
}

function getLanguages($lang = null)
{
    $languages = [
        "AA" => 'Afar',
        "AF" => 'Afrikanns',
        "SQ" => 'Albanian',
        "AM" => 'Amharic',
        "AR" => 'Arabic',
        "HY" => 'Armenian',
        "AY" => 'Aymara',
        "AZ" => 'Azerbaijani',
        "EU" => 'Basque',
        "DZ" => 'Bhutani',
        "BH" => 'Bihari',
        "BI" => 'Bislama',
        "BR" => 'Breton',
        "BG" => 'Bulgarian',
        "MY" => 'Burmese',
        "BE" => 'Byelorussian',
        "BN" => 'Bangla',
        "KM" => 'Cambodian',
        "CA" => 'Catalan',
        "ZH" => 'Chinese',
        "HR" => 'Croation',
        "CS" => 'Czech',
        "DA" => 'Danish',
        "NL" => 'Dutch',
        "EN" => 'English',
        "ET" => 'Estonian',
        "FO" => 'Faeroese',
        "FJ" => 'Fiji',
        "FI" => 'Finnish',
        "FR" => 'French',
        "KA" => 'Georgian',
        "DE" => 'German',
        "DV" => 'Dhivehi',
        "EL" => 'Greek',
        "KL" => 'Greenlandic',
        "GN" => 'Guarani',
        "HI" => 'Hindi',
        "HE" => 'Hebrew',
        "HU" => 'Hungarian',
        "IS" => 'Icelandic',
        "ID" => 'Indonesian',
        "IT" => 'Italian',
        "JA" => 'Japanese',
        "KK" => 'Kazakh',
        "RW" => 'Kinyarwanda',
        "KY" => 'Kirghiz',
        "KO" => 'Korean',
        "KU" => 'Kurdish',
        "LO" => 'Laothian',
        "LA" => 'Latin',
        "LV" => 'Latvian',
        "LT" => 'Lithuanian',
        "MK" => 'Macedonian',
        "MG" => 'Malagasy',
        "MS" => 'Malay',
        "MT" => 'Maltese',
        "MI" => 'Maori',
        "MN" => 'Mongolian',
        "NA" => 'Nauru',
        "NE" => 'Nepali',
        "NO" => 'Norwegian',
        "OM" => 'Oromo',
        "PS" => 'Pashto',
        "FA" => 'Persian',
        "PL" => 'Polish',
        "PT" => 'Portuguese',
        "QU" => 'Quechua',
        "RM" => 'Rhaeto',
        "RO" => 'Romanian',
        "RU" => 'Russian',
        "SM" => 'Samoan',
        "SG" => 'Sangro',
        "SR" => 'Serbian',
        "TN" => 'Setswana',
        "SN" => 'Shona',
        "SI" => 'Singhalese',
        "SS" => 'Siswati',
        "SK" => 'Slovak',
        "SL" => 'Slovenian',
        "SO" => 'Somali',
        "ES" => 'Spanish',
        "SV" => 'Swedish',
        "TL" => 'Tagalog',
        "TG" => 'Tajik',
        "TA" => 'Tamil',
        "TH" => 'Thai',
        "TI" => 'Tigrinya',
        "TR" => 'Turkish',
        "TK" => 'Turkmen',
        "TW" => 'Twi',
        "UK" => 'Ukranian',
        "UR" => 'Urdu',
        "UZ" => 'Uzbek',
        "VI" => 'Vietnamese',
        "XH" => 'Xhosa',
    ];

    if (!empty($lang) and is_array($lang)) {
        return array_flip(array_intersect(array_flip($languages), $lang));
    } elseif (!empty($lang)) {
        return $languages[$lang];
    }

    return $languages;
}

function localeToCountryCode($code, $revers = false)
{
    $languages = [
        "AA" => 'DJ', // language code => country code
        "AF" => 'ZA',
        "SQ" => 'AL',
        "AM" => 'ET',
        "AR" => 'IQ',
        "HY" => 'AM',
        "AY" => 'BO',
        "AZ" => 'AZ',
        "EU" => 'ES',
        "BN" => 'BD',
        "DZ" => 'BT',
        "BI" => 'VU',
        "BG" => 'BG',
        "MY" => 'MM',
        "BE" => 'BY',
        "KM" => 'KH',
        "CA" => 'ES',
        "ZH" => 'CN',
        "HR" => 'HR',
        "CS" => 'CZ',
        "DA" => 'DK',
        "DV" => 'MV',
        "NL" => 'NL',
        "EN" => 'US',
        "ET" => 'EE',
        "FO" => 'FO',
        "FJ" => 'FJ',
        "FI" => 'FI',
        "FR" => 'FR',
        "KA" => 'GE',
        "DE" => 'DE',
        "EL" => 'GR',
        "KL" => 'GL',
        "GN" => 'GN',
        "HI" => 'IN',
        "HE" => 'IL',
        "HU" => 'HU',
        "IS" => 'IS',
        "ID" => 'ID',
        "IT" => 'IT',
        "JA" => 'JP',
        "KK" => 'KZ',
        "RW" => 'RW',
        "KY" => 'KG',
        "KO" => 'KR',
        "LO" => 'LA',
        "LA" => 'RS',
        "LV" => 'LV',
        "LT" => 'LT',
        "MK" => 'MK',
        "MG" => 'MG',
        "MS" => 'MS',
        "MT" => 'MT',
        "MI" => 'NZ',
        "MN" => 'MN',
        "NA" => 'NR',
        "NE" => 'NP',
        "NO" => 'NO',
        "OM" => 'ET',
        "PS" => 'AF',
        "FA" => 'IR',
        "PL" => 'PL',
        "PT" => 'PT',
        "QU" => 'BO',
        "RM" => 'CH',
        "RO" => 'RO',
        "RU" => 'RU',
        "SM" => 'WS',
        "SG" => 'CG',
        "SR" => 'SR',
        "TN" => 'BW',
        "SN" => 'ZW',
        "SI" => 'LK',
        "SS" => 'SZ',
        "SK" => 'SK',
        "SL" => 'SI',
        "SO" => 'SO',
        "ES" => 'ES',
        "SV" => 'SE',
        "TL" => 'PH',
        "TG" => 'TJ',
        "TA" => 'LK',
        "TH" => 'TH',
        "TI" => 'ER',
        "TR" => 'TR',
        "TK" => 'TM',
        "TW" => 'TW',
        "UK" => 'UA',
        "UR" => 'PK',
        "UZ" => 'UZ',
        "VI" => 'VN',
        "XH" => 'ZA',
        "KU" => 'KU',
    ];

    if ($revers) {
        $languages = array_flip($languages);
        return !empty($languages[$code]) ? $languages[$code] : '';
    }

    return !empty($languages[$code]) ? $languages[$code] : '';
}

function getMoneyUnits($unit = null)
{
    $units = [
        "USD" => 'United States Dollar',
        "EUR" => 'Euro Member Countries',
        "AUD" => 'Australia Dollar',
        "AED" => 'United Arab Emirates dirham',
        "KAD" => 'KAD',
        "JPY" => 'Japan Yen',
        "CNY" => 'China Yuan Renminbi',
        "SAR" => 'Saudi Arabia Riyal',
        "KRW" => 'Korea (South) Won',
        "INR" => 'India Rupee',
        "RUB" => 'Russia Ruble',
        "Lek" => 'Albania Lek',
        "AFN" => 'Afghanistan Afghani',
        "ARS" => 'Argentina Peso',
        "AWG" => 'Aruba Guilder',
        "AZN" => 'Azerbaijan Manat',
        "BDT" => 'Bangladeshi taka',
        "BSD" => 'Bahamas Dollar',
        "BBD" => 'Barbados Dollar',
        "BYN" => 'Belarus Ruble',
        "BZD" => 'Belize Dollar',
        "BMD" => 'Bermuda Dollar',
        "BOB" => 'Bolivia Bolíviano',
        "BAM" => 'Bosnia and Herzegovina Convertible Mark',
        "BWP" => 'Botswana Pula',
        "BGN" => 'Bulgaria Lev',
        "BRL" => 'Brazil Real',
        "BND" => 'Brunei Darussalam Dollar',
        "KHR" => 'Cambodia Riel',
        "CAD" => 'Canada Dollar',
        "KYD" => 'Cayman Islands Dollar',
        "CLP" => 'Chile Peso',
        "COP" => 'Colombia Peso',
        "CRC" => 'Costa Rica Colon',
        "HRK" => 'Croatia Kuna',
        "CUP" => 'Cuba Peso',
        "CZK" => 'Czech Republic Koruna',
        "DKK" => 'Denmark Krone',
        "DZD" => 'Algerian Dinar',
        "DOP" => 'Dominican Republic Peso',
        "XCD" => 'East Caribbean Dollar',
        "EGP" => 'Egypt Pound',
        "GTQ" => 'Guatemala Quetzal',
        "GHS" => 'Ghanaian cedi',
        "HKD" => 'Hong Kong Dollar',
        "HUF" => 'Hungary Forint',
        "IDR" => 'Indonesia Rupiah',
        "IRR" => 'Iran Rial',
        "ILS" => 'Israel Shekel',
        "LBP" => 'Lebanon Pound',
        "MAD" => 'Moroccan dirham',
        "MYR" => 'Malaysia Ringgit',
        "MVR" => 'Maldivian Rufiyaa',
        "NGN" => 'Nigeria Naira',
        "NPR" => 'Nepalese Rupee',
        "NOK" => 'Norway Krone',
        "OMR" => 'Oman Rial',
        "PKR" => 'Pakistan Rupee',
        "PHP" => 'Philippines Peso',
        "PLN" => 'Poland Zloty',
        "RON" => 'Romania Leu',
        "ZAR" => 'South Africa Rand',
        "LKR" => 'Sri Lanka Rupee',
        "SEK" => 'Sweden Krona',
        "SGD" => 'Singapore Dollar',
        "CHF" => 'Switzerland Franc',
        "THB" => 'Thailand Baht',
        "TRY" => 'Turkey Lira',
        "UAH" => 'Ukraine Hryvnia',
        "GBP" => 'United Kingdom Pound',
        "TWD" => 'Taiwan New Dollar',
        "VND" => 'Viet Nam Dong',
        "UZS" => 'Uzbekistan Som',
        "KZT" => 'Kazakhstani Tenge',
        "TZS" => 'Tanzanian shilling',
        "ETB" => 'Ethiopian Birr',
        "KWD" => 'Kuwaiti Dinar',
        "EGP" => 'Egyptian Pound',
        "BIF" => 'Burundian Francs',
        "CDF" => 'Congolese Franc',
        "NAD" => 'Namibian Dollar',
    ];

    if (!empty($unit)) {
        return $units[$unit];
    }

    return $units;
}

function currenciesLists($sing = null)
{
    $lists = [
        "USD" => 'United States Dollar',
        "EUR" => 'Euro Member Countries',
        "AUD" => 'Australia Dollar',
        "AED" => 'United Arab Emirates dirham',
        "KAD" => 'KAD',
        "JPY" => 'Japan Yen',
        "CNY" => 'China Yuan Renminbi',
        "SAR" => 'Saudi Arabia Riyal',
        "KRW" => 'Korea (South) Won',
        "INR" => 'India Rupee',
        "RUB" => 'Russia Ruble',
        "Lek" => 'Albania Lek',
        "AFN" => 'Afghanistan Afghani',
        "ARS" => 'Argentina Peso',
        "AWG" => 'Aruba Guilder',
        "AZN" => 'Azerbaijan Manat',
        "BSD" => 'Bahamas Dollar',
        "BBD" => 'Barbados Dollar',
        "BDT" => 'Bangladeshi taka',
        "BYN" => 'Belarus Ruble',
        "BZD" => 'Belize Dollar',
        "BMD" => 'Bermuda Dollar',
        "BOB" => 'Bolivia Bolíviano',
        "BAM" => 'Bosnia and Herzegovina Convertible Mark',
        "BWP" => 'Botswana Pula',
        "BGN" => 'Bulgaria Lev',
        "BRL" => 'Brazil Real',
        "BND" => 'Brunei Darussalam Dollar',
        "KHR" => 'Cambodia Riel',
        "CAD" => 'Canada Dollar',
        "KYD" => 'Cayman Islands Dollar',
        "CLP" => 'Chile Peso',
        "COP" => 'Colombia Peso',
        "CRC" => 'Costa Rica Colon',
        "HRK" => 'Croatia Kuna',
        "CUP" => 'Cuba Peso',
        "CZK" => 'Czech Republic Koruna',
        "DKK" => 'Denmark Krone',
        "DZD" => 'Algerian Dinar',
        "DOP" => 'Dominican Republic Peso',
        "XCD" => 'East Caribbean Dollar',
        "EGP" => 'Egypt Pound',
        "GTQ" => 'Guatemala Quetzal',
        "GHS" => 'Ghanaian cedi',
        "HKD" => 'Hong Kong Dollar',
        "HUF" => 'Hungary Forint',
        "IDR" => 'Indonesia Rupiah',
        "IRR" => 'Iran Rial',
        "ILS" => 'Israel Shekel',
        "LBP" => 'Lebanon Pound',
        "MAD" => 'Moroccan dirham',
        "MYR" => 'Malaysia Ringgit',
        "MVR" => 'Maldivian Rufiyaa',
        "NGN" => 'Nigeria Naira',
        "NPR" => 'Nepalese Rupee',
        "NOK" => 'Norway Krone',
        "OMR" => 'Oman Rial',
        "PKR" => 'Pakistan Rupee',
        "PHP" => 'Philippines Peso',
        "PLN" => 'Poland Zloty',
        "RON" => 'Romania Leu',
        "ZAR" => 'South Africa Rand',
        "LKR" => 'Sri Lanka Rupee',
        "SEK" => 'Sweden Krona',
        "SGD" => 'Singapore Dollar',
        "CHF" => 'Switzerland Franc',
        "THB" => 'Thailand Baht',
        "TRY" => 'Turkey Lira',
        "UAH" => 'Ukraine Hryvnia',
        "GBP" => 'United Kingdom Pound',
        "TWD" => 'Taiwan New Dollar',
        "VND" => 'Viet Nam Dong',
        "UZS" => 'Uzbekistan Som',
        "KZT" => 'Kazakhstani Tenge',
        "TZS" => 'Tanzanian shilling',
        "ETB" => 'Ethiopian Birr',
        "KWD" => 'Kuwaiti Dinar',
        "EGP" => 'Egyptian Pound',
        "BIF" => 'Burundian Francs',
        "CDF" => 'Congolese Franc',
        "NAD" => 'Namibian Dollar',

    ];

    if (!empty($sing)) {
        return $lists[$sing];
    }

    return $lists;
}


function currency($user = null)
{
    if (empty($user)) {
        $user = auth()->user();
    }

    if (!empty($user) and !empty($user->currency)) {
        return $user->currency;
    } else if (empty($user)) {
        $checkCookie = Cookie::get('user_currency');

        if (!empty($checkCookie)) {
            return $checkCookie;
        }
    }

    return getDefaultCurrency();
}

function getDefaultCurrency()
{
    return getFinancialCurrencySettings('currency') ?? 'USD';
}

function currencySign($currency = null)
{
    if (empty($currency)) {
        $currency = currency();
    }

    switch ($currency) {
        case 'USD':
            return '$';
            break;
        case 'EUR':
            return '€';
            break;
        case 'JPY':
        case 'CNY':
            return '¥';
            break;
        case 'AED':
            return 'د.إ';
            break;
        case 'SAR':
            return 'ر.س';
            break;
        case 'KRW':
            return '₩';
            break;
        case 'INR':
            return '₹';
            break;
        case 'RUB':
            return '₽';
            break;
        case 'Lek':
            return 'Lek';
            break;
        case 'AFN':
            return '؋';
            break;
        case 'ARS':
            return '$';
            break;
        case 'AWG':
            return 'ƒ';
            break;
        case 'AUD':
            return '$';
            break;
        case 'AZN':
            return '₼';
            break;
        case 'BSD':
            return '$';
            break;
        case 'BBD':
            return '$';
            break;
        case 'BDT':
            return '৳';
            break;
        case 'BYN':
            return 'Br';
            break;
        case 'BZD':
            return 'BZ$';
            break;
        case 'BMD':
            return '$';
            break;
        case 'BOB':
            return '$b';
            break;
        case 'BAM':
            return 'KM';
            break;
        case 'BWP':
            return 'P';
            break;
        case 'BGN':
            return 'лв';
            break;
        case 'BRL':
            return 'R$';
            break;
        case 'BND':
            return '$';
            break;
        case 'COP':
            return '$';
            break;
        case 'CRC':
            return '₡';
            break;
        case 'CZK':
            return 'Kč';
            break;
        case 'CUP':
            return '₱';
            break;
        case 'DKK':
            return 'kr';
            break;
        case 'DZD':
            return 'دج';
            break;
        case 'DOP':
            return 'RD$';
            break;
        case 'XCD':
            return '$';
            break;
        case 'EGP':
            return '£';
            break;
        case 'GTQ':
            return 'Q';
            break;
        case 'HKD':
            return '$';
            break;
        case 'HUF':
            return 'Ft';
            break;
        case 'IDR':
            return 'Rp';
            break;
        case 'IRR':
            return '﷼';
            break;
        case 'ILS':
            return '₪';
            break;
        case 'LBP':
            return '£';
            break;
        case 'MAD':
            return 'DH';
            break;
        case 'MYR':
            return 'RM';
            break;
        case 'MVR':
            return 'MVR';
            break;
        case 'NGN':
            return '₦';
            break;
        case 'NPR':
            return 'रू';
            break;
        case 'NOK':
            return 'kr';
            break;
        case 'OMR':
            return '﷼';
            break;
        case 'PKR':
            return '₨';
            break;
        case 'PHP':
            return '₱';
            break;
        case 'PLN':
            return 'zł';
            break;
        case 'RON':
            return 'lei';
            break;
        case 'ZAR':
            return 'R';
            break;
        case 'LKR':
            return '₨';
            break;
        case 'SEK':
            return 'kr';
            break;
        case 'SGD':
            return '$';
            break;
        case 'CHF':
            return 'CHF';
            break;
        case 'THB':
            return '฿';
            break;
        case 'TRY':
            return '₺';
            break;
        case 'UAH':
            return '₴';
            break;
        case 'GBP':
            return '£';
            break;
        case 'GHS':
            return 'GH₵';
            break;
        case 'VND':
            return '₫';
            break;
        case 'TWD':
            return 'NT$';
            break;
        case 'UZS':
            return 'лв';
            break;
        case 'KZT':
            return '₸';
            break;
        case 'TZS':
            return 'TSh';
            break;
        case 'ETB':
            return 'Br';
            break;
        case 'KWD':
            return 'KD';
            break;
        case 'EGP':
            return 'ج.م';
            break;
        case 'NAD':
            return 'NAD';
            break;
        case 'CDF':
            return 'CDF';
            break;
        case 'BIF':
            return 'BIF';
            break;
        default:
            return '$';
    }

    return '$';
}

function getCountriesMobileCode()
{
    return [
        'USA (+1)' => '+1',
        'UK (+44)' => '+44',
        'Algeria (+213)' => '+213',
        'Andorra (+376)' => '+376',
        'Angola (+244)' => '+244',
        'Anguilla (+1264)' => '+1264',
        'Antigua &amp; Barbuda (+1268)' => '+1268',
        'Argentina (+54)' => '+54',
        'Armenia (+374)' => '+374',
        'Aruba (+297)' => '+297',
        'Australia (+61)' => '+61',
        'Austria (+43)' => '+43',
        'Azerbaijan (+994)' => '+994',
        'Bahamas (+1242)' => '+1242',
        'Bahrain (+973)' => '+973',
        'Bangladesh (+880)' => '+880',
        'Barbados (+1246)' => '+1246',
        'Belarus (+375)' => '+375',
        'Belgium (+32)' => '+32',
        'Belize (+501)' => '+501',
        'Benin (+229)' => '+229',
        'Bermuda (+1441)' => '+1441',
        'Bhutan (+975)' => '+975',
        'Bolivia (+591)' => '+591',
        'Bosnia Herzegovina (+387)' => '+387',
        'Botswana (+267)' => '+267',
        'Brazil (+55)' => '+55',
        'Brunei (+673)' => '+673',
        'Bulgaria (+359)' => '+359',
        'Burkina Faso (+226)' => '+226',
        'Burundi (+257)' => '+257',
        'Cambodia (+855)' => '+855',
        'Cameroon (+237)' => '+237',
        'Canada (+1)' => '+1',
        'Cape Verde Islands (+238)' => '+238',
        'Cayman Islands (+1345)' => '+1345',
        'Central African Republic (+236)' => '+236',
        'Chile (+56)' => '+56',
        'China (+86)' => '+86',
        'Colombia (+57)' => '+57',
        'Comoros (+269)' => '+269',
        'Congo (+242)' => '+242',
        'Cook Islands (+682)' => '+682',
        'Costa Rica (+506)' => '+506',
        'Croatia (+385)' => '+385',
        'Cuba (+53)' => '+53',
        'Cyprus - North (+90)' => '+90',
        'Cyprus - South (+357)' => '+357',
        'Czech Republic (+420)' => '+420',
        'Denmark (+45)' => '+45',
        'Djibouti (+253)' => '+253',
        'Dominica (+1809)' => '+1809',
        'Dominican Republic (+1809)' => '+1809',
        'Ecuador (+593)' => '+593',
        'Egypt (+20)' => '+20',
        'El Salvador (+503)' => '+503',
        'Equatorial Guinea (+240)' => '+240',
        'Eritrea (+291)' => '+291',
        'Estonia (+372)' => '+372',
        'Ethiopia (+251)' => '+251',
        'Falkland Islands (+500)' => '+500',
        'Faroe Islands (+298)' => '+298',
        'Fiji (+679)' => '+679',
        'Finland (+358)' => '+358',
        'France (+33)' => '+33',
        'French Guiana (+594)' => '+594',
        'French Polynesia (+689)' => '+689',
        'Gabon (+241)' => '+241',
        'Gambia (+220)' => '+220',
        'Georgia (+7880)' => '+7880',
        'Germany (+49)' => '+49',
        'Ghana (+233)' => '+233',
        'Gibraltar (+350)' => '+350',
        'Greece (+30)' => '+30',
        'Greenland (+299)' => '+299',
        'Grenada (+1473)' => '+1473',
        'Guadeloupe (+590)' => '+590',
        'Guam (+671)' => '+671',
        'Guatemala (+502)' => '+502',
        'Guinea (+224)' => '+224',
        'Guinea - Bissau (+245)' => '+245',
        'Guyana (+592)' => '+592',
        'Haiti (+509)' => '+509',
        'Honduras (+504)' => '+504',
        'Hong Kong (+852)' => '+852',
        'Hungary (+36)' => '+36',
        'Iceland (+354)' => '+354',
        'India (+91)' => '+91',
        'Indonesia (+62)' => '+62',
        'Iraq (+964)' => '+964',
        'Iran (+98)' => '+98',
        'Ireland (+353)' => '+353',
        'Israel (+972)' => '+972',
        'Italy (+39)' => '+39',
        'Jamaica (+1876)' => '+1876',
        'Japan (+81)' => '+81',
        'Jordan (+962)' => '+962',
        'Kazakhstan (+7)' => '+7',
        'Kenya (+254)' => '+254',
        'Kiribati (+686)' => '+686',
        'Korea - North (+850)' => '+850',
        'Korea - South (+82)' => '+82',
        'Kuwait (+965)' => '+965',
        'Kyrgyzstan (+996)' => '+996',
        'Laos (+856)' => '+856',
        'Latvia (+371)' => '+371',
        'Lebanon (+961)' => '+961',
        'Lesotho (+266)' => '+266',
        'Liberia (+231)' => '+231',
        'Libya (+218)' => '+218',
        'Liechtenstein (+417)' => '+417',
        'Lithuania (+370)' => '+370',
        'Luxembourg (+352)' => '+352',
        'Macao (+853)' => '+853',
        'Macedonia (+389)' => '+389',
        'Madagascar (+261)' => '+261',
        'Malawi (+265)' => '+265',
        'Malaysia (+60)' => '+60',
        'Maldives (+960)' => '+960',
        'Mali (+223)' => '+223',
        'Malta (+356)' => '+356',
        'Marshall Islands (+692)' => '+692',
        'Martinique (+596)' => '+596',
        'Mauritania (+222)' => '+222',
        'Mayotte (+269)' => '+269',
        'Mexico (+52)' => '+52',
        'Micronesia (+691)' => '+691',
        'Moldova (+373)' => '+373',
        'Monaco (+377)' => '+377',
        'Mongolia (+976)' => '+976',
        'Montserrat (+1664)' => '+1664',
        'Morocco (+212)' => '+212',
        'Mozambique (+258)' => '+258',
        'Myanmar (+95)' => '+95',
        'Namibia (+264)' => '+264',
        'Nauru (+674)' => '+674',
        'Nepal (+977)' => '+977',
        'Netherlands (+31)' => '+31',
        'New Caledonia (+687)' => '+687',
        'New Zealand (+64)' => '+64',
        'Nicaragua (+505)' => '+505',
        'Niger (+227)' => '+227',
        'Nigeria (+234)' => '+234',
        'Niue (+683)' => '+683',
        'Norfolk Islands (+672)' => '+672',
        'Northern Marianas (+670)' => '+670',
        'Norway (+47)' => '+47',
        'Oman (+968)' => '+968',
        'Pakistan (+92)' => '+92',
        'Palau (+680)' => '+680',
        'Panama (+507)' => '+507',
        'Papua New Guinea (+675)' => '+675',
        'Paraguay (+595)' => '+595',
        'Peru (+51)' => '+51',
        'Philippines (+63)' => '+63',
        'Poland (+48)' => '+48',
        'Portugal (+351)' => '+351',
        'Puerto Rico (+1787)' => '+1787',
        'Qatar (+974)' => '+974',
        'Reunion (+262)' => '+262',
        'Romania (+40)' => '+40',
        'Russia (+7)' => '+7',
        'Rwanda (+250)' => '+250',
        'San Marino (+378)' => '+378',
        'Sao Tome &amp; Principe (+239)' => '+239',
        'Saudi Arabia (+966)' => '+966',
        'Senegal (+221)' => '+221',
        'Serbia (+381)' => '+381',
        'Seychelles (+248)' => '+248',
        'Sierra Leone (+232)' => '+232',
        'Singapore (+65)' => '+65',
        'Slovak Republic (+421)' => '+421',
        'Slovenia (+386)' => '+386',
        'Solomon Islands (+677)' => '+677',
        'Somalia (+252)' => '+252',
        'South Africa (+27)' => '+27',
        'Spain (+34)' => '+34',
        'Sri Lanka (+94)' => '+94',
        'St. Helena (+290)' => '+290',
        'St. Kitts (+1869)' => '+1869',
        'St. Lucia (+1758)' => '+1758',
        'Suriname (+597)' => '+597',
        'Sudan (+249)' => '+249',
        'Swaziland (+268)' => '+268',
        'Sweden (+46)' => '+46',
        'Switzerland (+41)' => '+41',
        'Syria (+963)' => '+963',
        'Taiwan (+886)' => '+886',
        'Tanzania (+255)' => '+255',
        'Tajikistan (+992)' => '+992',
        'Thailand (+66)' => '+66',
        'Togo (+228)' => '+228',
        'Tonga (+676)' => '+676',
        'Trinidad &amp; Tobago (+1868)' => '+1868',
        'Tunisia (+216)' => '+216',
        'Turkey (+90)' => '+90',
        'Turkmenistan (+993)' => '+993',
        'Turks &amp; Caicos Islands (+1649)' => '+1649',
        'Tuvalu (+688)' => '+688',
        'Uganda (+256)' => '+256',
        'Ukraine (+380)' => '+380',
        'United Arab Emirates (+971)' => '+971',
        'Uruguay (+598)' => '+598',
        'Uzbekistan (+998)' => '+998',
        'Vanuatu (+678)' => '+678',
        'Vatican City (+379)' => '+379',
        'Venezuela (+58)' => '+58',
        'Vietnam (+84)' => '+84',
        'Virgin Islands - British (+1)' => '+1',
        'Virgin Islands - US (+1)' => '+1',
        'Wallis &amp; Futuna (+681)' => '+681',
        'Yemen (North)(+969)' => '+969',
        'Yemen (South)(+967)' => '+967',
        'Zambia (+260)' => '+260',
        'Zimbabwe (+263)' => '+263',
    ];
}

function getCountriesLists($code = null)
{
    $countries = [
        'AF' => 'Afghanistan',
        'AX' => 'Aland Islands',
        'AL' => 'Albania',
        'DZ' => 'Algeria',
        'AS' => 'American Samoa',
        'AD' => 'Andorra',
        'AO' => 'Angola',
        'AI' => 'Anguilla',
        'AQ' => 'Antarctica',
        'AG' => 'Antigua and Barbuda',
        'AR' => 'Argentina',
        'AM' => 'Armenia',
        'AW' => 'Aruba',
        'AU' => 'Australia',
        'AT' => 'Austria',
        'AZ' => 'Azerbaijan',
        'BS' => 'Bahamas',
        'BH' => 'Bahrain',
        'BD' => 'Bangladesh',
        'BB' => 'Barbados',
        'BY' => 'Belarus',
        'BE' => 'Belgium',
        'BZ' => 'Belize',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BT' => 'Bhutan',
        'BO' => 'Bolivia',
        'BQ' => 'Bonaire, Sint Eustatius and Saba',
        'BA' => 'Bosnia and Herzegovina',
        'BW' => 'Botswana',
        'BV' => 'Bouvet Island',
        'BR' => 'Brazil',
        'IO' => 'British Indian Ocean Territory',
        'BN' => 'Brunei Darussalam',
        'BG' => 'Bulgaria',
        'BF' => 'Burkina Faso',
        'BI' => 'Burundi',
        'KH' => 'Cambodia',
        'CM' => 'Cameroon',
        'CA' => 'Canada',
        'CV' => 'Cape Verde',
        'KY' => 'Cayman Islands',
        'CF' => 'Central African Republic',
        'TD' => 'Chad',
        'CL' => 'Chile',
        'CN' => 'China',
        'CX' => 'Christmas Island',
        'CC' => 'Cocos (Keeling) Islands',
        'CO' => 'Colombia',
        'KM' => 'Comoros',
        'CG' => 'Congo',
        'CD' => 'Congo, Democratic Republic',
        'CK' => 'Cook Islands',
        'CR' => 'Costa Rica',
        'CI' => 'Cote d Ivoire',
        'HR' => 'Croatia',
        'CU' => 'Cuba',
        'CW' => 'Curacao',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DK' => 'Denmark',
        'DJ' => 'Djibouti',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'EC' => 'Ecuador',
        'EG' => 'Egypt',
        'SV' => 'El Salvador',
        'GQ' => 'Equatorial Guinea',
        'ER' => 'Eritrea',
        'EE' => 'Estonia',
        'ET' => 'Ethiopia',
        'FK' => 'Falkland Islands',
        'FO' => 'Faroe Islands',
        'FJ' => 'Fiji',
        'FI' => 'Finland',
        'FR' => 'France',
        'GF' => 'French Guiana',
        'PF' => 'French Polynesia',
        'TF' => 'French Southern Territories',
        'GA' => 'Gabon',
        'GM' => 'Gambia',
        'GE' => 'Georgia',
        'DE' => 'Germany',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GR' => 'Greece',
        'GL' => 'Greenland',
        'GD' => 'Grenada',
        'GP' => 'Guadeloupe',
        'GU' => 'Guam',
        'GT' => 'Guatemala',
        'GG' => 'Guernsey',
        'GN' => 'Guinea',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HT' => 'Haiti',
        'HM' => 'Heard Island and McDonald Islands',
        'VA' => 'Holy See (Vatican City State)',
        'HN' => 'Honduras',
        'HK' => 'Hong Kong',
        'HU' => 'Hungary',
        'IS' => 'Iceland',
        'IN' => 'India',
        'ID' => 'Indonesia',
        'IR' => 'Iran, Islamic Republic of',
        'IQ' => 'Iraq',
        'IE' => 'Ireland',
        'IM' => 'Isle of Man',
        'IL' => 'Israel',
        'IT' => 'Italy',
        'JM' => 'Jamaica',
        'JP' => 'Japan',
        'JE' => 'Jersey',
        'JO' => 'Jordan',
        'KZ' => 'Kazakhstan',
        'KE' => 'Kenya',
        'KI' => 'Kiribati',
        'KP' => 'Korea, Democratic Peoples Republic of',
        'KR' => 'Korea, Republic of',
        'KW' => 'Kuwait',
        'KG' => 'Kyrgyzstan',
        'LA' => 'Lao Peoples Democratic Republic',
        'LV' => 'Latvia',
        'LB' => 'Lebanon',
        'LS' => 'Lesotho',
        'LR' => 'Liberia',
        'LY' => 'Libya',
        'LI' => 'Liechtenstein',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'MO' => 'Macao',
        'MK' => 'Macedonia, the former Yugoslav Republic of',
        'MG' => 'Madagascar',
        'MW' => 'Malawi',
        'MY' => 'Malaysia',
        'MV' => 'Maldives',
        'ML' => 'Mali',
        'MT' => 'Malta',
        'MH' => 'Marshall Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MU' => 'Mauritius',
        'YT' => 'Mayotte',
        'MX' => 'Mexico',
        'FM' => 'Micronesia, Federated States of',
        'MD' => 'Moldova, Republic of',
        'MC' => 'Monaco',
        'MN' => 'Mongolia',
        'ME' => 'Montenegro',
        'MS' => 'Montserrat',
        'MA' => 'Morocco',
        'MZ' => 'Mozambique',
        'MM' => 'Myanmar',
        'NA' => 'Namibia',
        'NR' => 'Nauru',
        'NP' => 'Nepal',
        'NL' => 'Netherlands',
        'NC' => 'New Caledonia',
        'NZ' => 'New Zealand',
        'NI' => 'Nicaragua',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NU' => 'Niue',
        'NF' => 'Norfolk Island',
        'MP' => 'Northern Mariana Islands',
        'NO' => 'Norway',
        'OM' => 'Oman',
        'PK' => 'Pakistan',
        'PW' => 'Palau',
        'PS' => 'Palestinian Territory, Occupied',
        'PA' => 'Panama',
        'PG' => 'Papua New Guinea',
        'PY' => 'Paraguay',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PN' => 'Pitcairn',
        'PL' => 'Poland',
        'PT' => 'Portugal',
        'PR' => 'Puerto Rico',
        'QA' => 'Qatar',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'BL' => 'Saint Barthelemy',
        'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
        'KN' => 'Saint Kitts and Nevis',
        'LC' => 'Saint Lucia',
        'MF' => 'Saint Martin (French part)',
        'PM' => 'Saint Pierre and Miquelon',
        'VC' => 'Saint Vincent and the Grenadines',
        'WS' => 'Samoa',
        'SM' => 'San Marino',
        'ST' => 'Sao Tome and Principe',
        'SA' => 'Saudi Arabia',
        'SN' => 'Senegal',
        'RS' => 'Serbia',
        'SC' => 'Seychelles',
        'SL' => 'Sierra Leone',
        'SG' => 'Singapore',
        'SX' => 'Sint Maarten (Dutch part)',
        'SK' => 'Slovakia',
        'SI' => 'Slovenia',
        'SB' => 'Solomon Islands',
        'SO' => 'Somalia',
        'ZA' => 'South Africa',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'SS' => 'South Sudan',
        'ES' => 'Spain',
        'LK' => 'Sri Lanka',
        'SD' => 'Sudan',
        'SR' => 'Suriname',
        'SJ' => 'Svalbard and Jan Mayen',
        'SZ' => 'Swaziland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'SY' => 'Syrian Arab Republic',
        'TW' => 'Taiwan, Province of China',
        'TJ' => 'Tajikistan',
        'TZ' => 'Tanzania, United Republic of',
        'TH' => 'Thailand',
        'TL' => 'Timor-Leste',
        'TG' => 'Togo',
        'TK' => 'Tokelau',
        'TO' => 'Tonga',
        'TT' => 'Trinidad and Tobago',
        'TN' => 'Tunisia',
        'TR' => 'Turkey',
        'TM' => 'Turkmenistan',
        'TC' => 'Turks and Caicos Islands',
        'TV' => 'Tuvalu',
        'UG' => 'Uganda',
        'UA' => 'Ukraine',
        'AE' => 'United Arab Emirates',
        'GB' => 'United Kingdom',
        'US' => 'United States',
        'UM' => 'United States Minor Outlying Islands',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VU' => 'Vanuatu',
        'VE' => 'Venezuela, Bolivarian Republic of',
        'VN' => 'Viet Nam',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S.',
        'WF' => 'Wallis and Futuna',
        'EH' => 'Western Sahara',
        'YE' => 'Yemen',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    ];

    if (!empty($code) and !empty($countries[$code])) {
        return $countries[$code];
    }

    return $countries;
}

// Truncate a string only at a whitespace
function truncate($text, $length, $withTail = true)
{
    $length = abs((int)$length);
    if (strlen($text) > $length) {
        $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", ($withTail ? '\\1 ...' : '\\1'), $text);
    }

    return ($text);
}


/**
 * @param null $page => Setting::$pagesSeoMetas
 * @return array [title, description]
 */
function getSeoMetas($page = null)
{
    return App\Models\Setting::getSeoMetas($page);
}

/**
 * @return array [title, image, link]
 */
function getSocials()
{
    return App\Models\Setting::getSocials();
}

/**
 * @return array [title, items => [title, link]]
 */
function getFooterColumns()
{
    return App\Models\Setting::getFooterColumns();
}


/*
 * @return array [site_name, site_email, site_phone, site_language, register_method, user_languages, rtl_languages, fav_icon, locale, logo, footer_logo, rtl_layout, home hero1 is active, home hero2 is active, content_translate, default_time_zone, date_format, time_format]
 */
function getGeneralSettings($key = null)
{
    return App\Models\Setting::getGeneralSettings($key);
}

/**
 * @param null $key
 * $key => "agora_resolution" | "agora_max_bitrate" | "agora_min_bitrate" | "agora_frame_rate" | "agora_live_streaming" | "agora_chat" | "agora_cloud_rec" | "agora_in_free_courses"
 * "new_interactive_file" | "timezone_in_register" | "timezone_in_create_webinar"
 * "sequence_content_status" | "webinar_assignment_status" | "webinar_private_content_status" | "disable_view_content_after_user_register"
 * "direct_classes_payment_button_status" | "mobile_app_status" | "cookie_settings_status" | "show_other_register_method" | "show_certificate_additional_in_register"
 * @return
 * */
function getFeaturesSettings($key = null)
{
    return App\Models\Setting::getFeaturesSettings($key);
}


function getSMSChannelsSettings($key = null)
{
    return App\Models\Setting::getSMSChannelsSettings($key);
}

/**
 * @param null $key
 * $key => cookie_settings_modal_message | cookie_settings_modal_items
 * @return
 * */
function getCookieSettings($key = null)
{
    return App\Models\Setting::getCookieSettings($key);
}


/**
 * @param $key
 * @return array|[commission, tax, minimum_payout, currency, currency_position, price_display]
 */
function getFinancialSettings($key = null)
{
    return App\Models\Setting::getFinancialSettings($key);
}

function getFinancialCurrencySettings($key = null)
{
    return App\Models\Setting::getFinancialCurrencySettings($key);
}

function getCommissionSettings($key = null)
{
    return App\Models\Setting::getCommissionSettings($key);
}


/**
 * @param string $section => 2 for hero section 2
 * @return array|[title, description, hero_background]
 */
function getHomeHeroSettings($section = '1')
{
    return App\Models\Setting::getHomeHeroSettings($section);
}

/**
 * @return array|[title, description, background]
 */
function getHomeVideoOrImageBoxSettings()
{
    return App\Models\Setting::getHomeVideoOrImageBoxSettings();
}


/**
 * @param null $page => admin_login, admin_dashboard, login, register, remember_pass, search, categories,
 * become_instructor, certificate_validation, blog, instructors
 * ,dashboard, panel_sidebar, user_avatar, user_cover, instructor_finder_wizard, products_lists
 * @return string|array => [all pages]
 */
function getPageBackgroundSettings($page = null)
{
    return App\Models\Setting::getPageBackgroundSettings($page);
}


/**
 * @param null $key => css, js
 * @return string|array => {css, js}
 */
function getCustomCssAndJs($key = null)
{
    return App\Models\Setting::getCustomCssAndJs($key);
}

/**
 * @return array
 */
function getOfflineBankSettings($key = null)
{
    return App\Models\Setting::getOfflineBankSettings($key);
}

/**
 * @return array [status, users_affiliate_status, affiliate_user_commission, affiliate_user_amount, referred_user_amount, referral_description]
 */
function getReferralSettings()
{
    $settings = App\Models\Setting::getReferralSettings();

    if (empty($settings['status'])) {
        $settings['status'] = false;
    } else {
        $settings['status'] = true;
    }

    if (empty($settings['users_affiliate_status'])) {
        $settings['users_affiliate_status'] = false;
    } else {
        $settings['users_affiliate_status'] = true;
    }

    if (empty($settings['affiliate_user_commission'])) {
        $settings['affiliate_user_commission'] = 0;
    }

    if (empty($settings['affiliate_user_amount'])) {
        $settings['affiliate_user_amount'] = 0;
    }

    if (empty($settings['referred_user_amount'])) {
        $settings['referred_user_amount'] = 0;
    }

    if (empty($settings['referral_description'])) {
        $settings['referral_description'] = '';
    }

    return $settings;
}

/**
 * @return array
 */
function getOfflineBanksTitle()
{
    $titles = [];

    $banks = getOfflineBankSettings();

    if (!empty($banks) and count($banks)) {
        foreach ($banks as $bank) {
            $titles[] = $bank['title'] ?? "";
        }
    }

    return $titles;
}

/**
 * @return array
 */
function getReportReasons()
{
    return App\Models\Setting::getReportReasons();
}

/**
 * @param $template {String|nullable}
 * @return array
 */
function getNotificationTemplates($template = null)
{
    return App\Models\Setting::getNotificationTemplates($template);
}

/**
 * @param $key
 * @return array
 */
function getContactPageSettings($key = null)
{
    return App\Models\Setting::getContactPageSettings($key);
}

/**
 * @param $key
 * @return array
 */
function get404ErrorPageSettings($key = null)
{
    return App\Models\Setting::get404ErrorPageSettings($key);
}

/**
 * @param $key
 * @return array
 */
function getHomeSectionsSettings($key = null)
{
    return App\Models\Setting::getHomeSectionsSettings($key);
}

/**
 * @param $key
 * @return array
 */
function getNavbarLinks()
{
    $links = App\Models\Setting::getNavbarLinksSettings();

    if (!empty($links)) {
        usort($links, function ($item1, $item2) {
            return $item1['order'] <=> $item2['order'];
        });
    }

    return $links;
}

/**
 * @return array
 */
function getPanelSidebarSettings()
{
    return App\Models\Setting::getPanelSidebarSettings();
}


/**
 * @return array
 */
function getFindInstructorsSettings()
{
    return App\Models\Setting::getFindInstructorsSettings();
}

/**
 * @return array
 */
function getRewardProgramSettings()
{
    return App\Models\Setting::getRewardProgramSettings();
}

/**
 * @return array
 */
function getRewardsSettings()
{
    return App\Models\Setting::getRewardsSettings();
}

/**
 * @param $kay => [status, virtual_product_commission, physical_product_commission, store_tax,
 *                 possibility_create_virtual_product, possibility_create_physical_product,
 *                 shipping_tracking_url, activate_comments
 *              ]
 */
function getStoreSettings($key = null)
{
    return App\Models\Setting::getStoreSettings($key);
}

function getBecomeInstructorSectionSettings()
{
    return App\Models\Setting::getBecomeInstructorSectionSettings();
}

function getForumSectionSettings()
{
    return App\Models\Setting::getForumSectionSettings();
}

function getRegistrationPackagesGeneralSettings($key = null)
{
    return App\Models\Setting::getRegistrationPackagesGeneralSettings($key);
}

function getRegistrationPackagesInstructorsSettings($key = null)
{
    return App\Models\Setting::getRegistrationPackagesInstructorsSettings($key);
}

function getRegistrationPackagesOrganizationsSettings($key = null)
{
    return App\Models\Setting::getRegistrationPackagesOrganizationsSettings($key);
}

function getMobileAppSettings($key = null)
{
    return App\Models\Setting::getMobileAppSettings($key);
}

function getMaintenanceSettings($key = null)
{
    return App\Models\Setting::getMaintenanceSettings($key);
}

function getRestrictionSettings($key = null)
{
    return App\Models\Setting::getRestrictionSettings($key);
}

function getGeneralOptionsSettings($key = null)
{
    return App\Models\Setting::getGeneralOptionsSettings($key);
}

function getGiftsGeneralSettings($key = null)
{
    return App\Models\Setting::getGiftsGeneralSettings($key);
}

function getAiContentsSettingsName($key = null)
{
    return App\Models\Setting::getAiContentsSettingsName($key);
}

function getCertificateMainSettings($key = null)
{
    return App\Models\Setting::getCertificateMainSettings($key);
}

function getRemindersSettings($key = null)
{
    return App\Models\Setting::getRemindersSettings($key);
}

function getGeneralSecuritySettings($key = null)
{
    return App\Models\Setting::getGeneralSecuritySettings($key);
}

function getAbandonedCartSettings($key = null)
{
    return App\Models\Setting::getAbandonedCartSettings($key);
}


function getAdminPanelUrlPrefix()
{
    $prefix = getGeneralSecuritySettings('admin_panel_url');
    return !empty($prefix) ? $prefix : 'admin';
}

function getAdminPanelUrl($url = null, $withFirstSlash = true)
{
    return ($withFirstSlash ? '/' : '') . getAdminPanelUrlPrefix() . ($url ?? '');
}

function getAdvertisingModalSettings()
{
    $cookieKey = 'show_advertise_modal';
    $settings = App\Models\Setting::getAdvertisingModalSettings();

    $show = false;

    if (!empty($settings) and !empty($settings['status']) and $settings['status'] == 1) {
        $checkCookie = Cookie::get($cookieKey);

        if (empty($checkCookie)) {
            $show = true;

            Cookie::queue($cookieKey, 1, 30 * 24 * 60);
        }
    }

    return $show ? $settings : null;
}

function getOthersPersonalizationSettings($key = null)
{
    return \App\Models\Setting::getOthersPersonalizationSettings($key);
}

function getInstallmentsSettings($key = null)
{
    return \App\Models\Setting::getInstallmentsSettings($key);
}

function getInstallmentsTermsSettings($key = null)
{
    return \App\Models\Setting::getInstallmentsTermsSettings($key);
}

function getRegistrationBonusSettings($key = null)
{
    return \App\Models\Setting::getRegistrationBonusSettings($key);
}

function getRegistrationBonusTermsSettings($key = null)
{
    return \App\Models\Setting::getRegistrationBonusTermsSettings($key);
}

function getStatisticsSettings($key = null)
{
    return \App\Models\Setting::getStatisticsSettings($key);
}

/**
 * @return string ("primary_color"|"secondary_color") || null
 * */
function getThemeColorsSettings($admin = false)
{
    $settings = App\Models\Setting::getThemeColorsSettings();

    $result = '';

    if (!empty($settings) and count($settings)) {
        $result = ":root{" . PHP_EOL;

        if ($admin) {
            foreach (\App\Models\Setting::$rootAdminColors as $color) {
                if (!empty($settings['admin_' . $color])) {
                    $result .= "--$color:" . $settings['admin_' . $color] . ';' . PHP_EOL;
                }
            }
        } else {
            foreach (\App\Models\Setting::$rootColors as $color) {
                if (!empty($settings[$color])) {
                    $result .= "--$color:" . $settings[$color] . ';' . PHP_EOL;
                }
            }

            if (!empty($settings['front_body_background'])) {
                $result .= "--body_bg:" . $settings['front_body_background'] . ';' . PHP_EOL;
            }
        }

        $result .= "}" . PHP_EOL;
    }

    return $result;
}


/**
 * @return string ("primary_color"|"secondary_color") || null
 * */
function getThemeFontsSettings()
{
    $settings = App\Models\Setting::getThemeFontsSettings();

    $result = '';

    if (!empty($settings) and count($settings)) {

        foreach ($settings as $type => $setting) {

            if (!empty($settings[$type]['regular'])) {
                $result .= "@font-face {
                      font-family: '$type-font-family';
                      font-style: normal;
                      font-weight: 400;
                      font-display: swap;
                      src: url({$settings[$type]['regular']}) format('woff2');
                    }";
            }

            if (!empty($settings[$type]['bold'])) {
                $result .= "@font-face {
                      font-family: '$type-font-family';
                      font-style: normal;
                      font-weight: bold;
                      font-display: swap;
                      src: url({$settings[$type]['bold']}) format('woff2');
                    }";
            }

            if (!empty($settings[$type]['medium'])) {
                $result .= "@font-face {
                      font-family: '$type-font-family';
                      font-style: normal;
                      font-weight: 500;
                      font-display: swap;
                      src: url({$settings[$type]['medium']}) format('woff2');
                    }";
            }

        }
    }

    return $result;
}

/**
 * @param $page => home, search, classes, categories, login, register, contact, blog, certificate_validation, 'instructors', 'organizations'
 *
 * @return string
 * */
function getPageRobot($page)
{
    $seoSettings = getSeoMetas($page);

    return (empty($seoSettings['robot']) or $seoSettings['robot'] != 'noindex') ? 'index, follow, all' : 'NOODP, nofollow, noindex';
}

function getPageRobotNoIndex()
{
    return 'NOODP, nofollow, noindex';
}

function getDefaultLocale()
{
    $key = 'site_language';
    $name = \App\Models\Setting::$generalName;

    /// I did not use the helper method because the Setting model uses translation and may get stuck in the loop.

    $setting = cache()->remember('settings.getDefaultLocale', 24 * 60 * 60, function () use ($name) {
        $setting = \Illuminate\Support\Facades\DB::table('settings')
            ->where('page', $name)
            ->where('name', $name)
            ->join('setting_translations', 'settings.id', '=', 'setting_translations.setting_id')
            ->select('settings.*', 'setting_translations.value')
            ->first();

        $value = [];

        if (!empty($setting) and !empty($setting->value) and isset($setting->value)) {
            $value = json_decode($setting->value, true);
        }

        return $value;
    });

    $siteLanguage = 'EN';

    if (!empty($setting)) {
        if (!empty($setting[$key])) {
            $siteLanguage = $setting[$key];
        }
    }

    return $siteLanguage;
}

function deepClone($object)
{
    $cloned = clone($object);
    foreach ($cloned as $key => $val) {
        if (is_object($val) || (is_array($val))) {
            $cloned->{$key} = unserialize(serialize($val));
        }
    }

    return $cloned;
}

function sendNotification($template, $options, $user_id = null, $group_id = null, $sender = 'system', $type = 'single', $templateId = null)
{
    if ($user_id == 1) {
        $mainAdminId = \App\User::getMainAdminId();

        if (!empty($mainAdminId)) {
            $user_id = $mainAdminId;
        }
    }

    if (empty($templateId)) {
        $templateId = getNotificationTemplates($template);
    }
    $notificationTemplate = \App\Models\NotificationTemplate::where('id', $templateId)->first();

    if (!empty($notificationTemplate)) {
        $title = str_replace(array_keys($options), array_values($options), $notificationTemplate->title);
        $message = str_replace(array_keys($options), array_values($options), $notificationTemplate->template);

        $check = \App\Models\Notification::where('user_id', $user_id)
            ->where('group_id', $group_id)
            ->where('title', $title)
            ->where('message', $message)
            ->where('sender', $sender)
            ->where('type', $type)
            ->first();

        $ignoreDuplicateTemplates = ['new_badge', 'registration_package_expired'];

        if (empty($check) or !in_array($template, $ignoreDuplicateTemplates)) {
            \App\Models\Notification::create([
                'user_id' => $user_id,
                'group_id' => $group_id,
                'title' => $title,
                'message' => $message,
                'sender' => $sender,
                'type' => $type,
                'created_at' => time()
            ]);

            if (env('APP_ENV') == 'production') {
                $user = \App\User::where('id', $user_id)->first();

                if (!empty($user) and !empty($user->email)) {
                    try {
                        \Mail::to($user->email)->send(new \App\Mail\SendNotifications(['title' => $title, 'message' => $message]));
                    } catch (Exception $exception) {
                        // dd($exception)
                    }
                }

                // Send Firebase Messages
                handleSendFirebaseMessages($user_id, $group_id, $sender, $type, $title, $message);
            }
        }

        return true;
    }

    return false;
}

function sendNotificationToEmail($template, $options, $email)
{
    $templateId = getNotificationTemplates($template);
    $notificationTemplate = \App\Models\NotificationTemplate::where('id', $templateId)->first();

    if (!empty($notificationTemplate)) {
        $title = str_replace(array_keys($options), array_values($options), $notificationTemplate->title);
        $message = str_replace(array_keys($options), array_values($options), $notificationTemplate->template);


        if (env('APP_ENV') == 'production') {
            try {
                \Mail::to($email)->send(new \App\Mail\SendNotifications(['title' => $title, 'message' => $message]));
            } catch (Exception $exception) {
                // dd($exception)
            }
        }

        return true;
    }

    return false;
}

function time2string($time)
{
    $_d = 0;
    $_h = 0;
    $_m = 0;
    $_s = 1;

    if ($time > 0) {
        $d = floor($time / 86400);
        $_d = ($d < 10 ? '0' : '') . $d;

        $h = floor(($time - $d * 86400) / 3600);
        $_h = ($h < 10 ? '0' : '') . $h;

        $m = floor(($time - ($d * 86400 + $h * 3600)) / 60);
        $_m = ($m < 10 ? '0' : '') . $m;

        $s = $time - ($d * 86400 + $h * 3600 + $m * 60);
        $_s = ($s < 10 ? '0' : '') . $s;
    }

    return [
        'day' => $_d,
        'hour' => $_h,
        'minute' => $_m,
        'second' => $_s
    ];
}

$months = [
    1 => 'Jan.',
    2 => 'Feb.',
    3 => 'Mar.',
    4 => 'Apr.',
    5 => 'May',
    6 => 'Jun.',
    7 => 'Jul.',
    8 => 'Aug.',
    9 => 'Sep.',
    10 => 'Oct.',
    11 => 'Nov.',
    12 => 'Dec.'
];

function fromAndToDateFilter($from, $to, $query, $column = 'created_at', $strToTime = true)
{
    if (!empty($from) and !empty($to)) {
        $from = $strToTime ? strtotime($from) : $from;
        $to = $strToTime ? strtotime($to) : $to;

        $query->whereBetween($column, [$from, $to]);
    } else {
        if (!empty($from)) {
            $from = $strToTime ? strtotime($from) : $from;

            $query->where($column, '>=', $from);
        }

        if (!empty($to)) {
            $to = $strToTime ? strtotime($to) : $to;

            $query->where($column, '<', $to);
        }
    }

    return $query;
}

function random_str($length, $includeNumeric = true, $includeChar = true)
{
    $keyspace = ($includeNumeric ? '0123456789' : '') . ($includeChar ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : '');
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;

    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[rand(0, $max)];
    }

    return ($includeNumeric and !$includeChar) ? (int)$str : $str;
}

function checkCourseForSale($course, $user)
{
    if (!$course->canSale()) {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('cart.course_not_capacity'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    if ($course->checkUserHasBought($user)) {
        $toastData = [
            'title' => trans('cart.fail_purchase'),
            'msg' => trans('site.you_bought_webinar'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    if ($course->creator_id == $user->id or $course->teacher_id == $user->id) {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('cart.cant_purchase_your_course'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    $isRequiredPrerequisite = false;
    if (!empty($course->prerequisites)) {
        $prerequisites = $course->prerequisites;
        if (count($prerequisites)) {
            foreach ($prerequisites as $prerequisite) {
                $prerequisiteWebinar = $prerequisite->prerequisiteWebinar;

                if ($prerequisite->required and !empty($prerequisiteWebinar) and !$prerequisiteWebinar->checkUserHasBought()) {
                    $isRequiredPrerequisite = true;
                }
            }
        }
    }

    if ($isRequiredPrerequisite) {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('cart.this_course_has_required_prerequisite'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    return 'ok';
}

function checkProductForSale($product, $user)
{
    if ($product->getAvailability() < 1) {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('update.product_not_availability'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    if ($product->creator_id == $user->id) {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('update.cant_purchase_your_product'),
            'status' => 'error'
        ];
        return back()->with(['toast' => $toastData]);
    }

    return 'ok';
}

function isAdminUrl($url = null)
{
    if (empty($url)) {
        $url = request()->getPathInfo();
    }
    return (1 === strpos($url, 'admin'));
}

function getTranslateAttributeValue($model, $key, $loca = null)
{
    $isAdminUrl = isAdminUrl();

    $locale = app()->getLocale();
    $contentLocale = $isAdminUrl ? getContentLocale() : null; // for admin edit contents

    $isEditModel = ($isAdminUrl and !empty($contentLocale) and is_array($contentLocale) and $contentLocale['table'] == $model->getTable() and $contentLocale['item_id'] == $model->id);

    if ($isAdminUrl and
        !empty($contentLocale) and
        is_array($contentLocale) and
        (
            ($contentLocale['table'] == $model->getTable() and $contentLocale['item_id'] == $model->id) or
            (
                (!empty($model->parent_id) and $contentLocale['item_id'] == $model->parent_id) or // for category edit page
                (!empty($model->filter_id) and $contentLocale['item_id'] == $model->filter_id) // for filter edit page
            )
        )
    ) {
        $locale = $contentLocale['locale']; // for admin edit contents
    }

    try {
        $locale = !empty($loca) ? $loca : $locale;

        if ($model->getTable() === 'settings' and in_array($model->name, \App\Models\Setting::getSettingsWithDefaultLocal())) {
            $locale = \App\Models\Setting::$defaultSettingsLocale;
        }

        $model->locale = $locale;

        return $model->translate(mb_strtolower($locale))->{$key};
    } catch (\Exception $e) {
        // this conditions get client side

        if (empty($contentLocale) and empty($loca)) { //  first get translate by site default language
            $defaultLocale = getDefaultLocale();

            return getTranslateAttributeValue($model, $key, $defaultLocale);
        } elseif ((!empty($loca) or !$isEditModel) and $loca != 'en' and !empty($model->translations) and count($model->translations)) { // if not translate by site default language get translate by English language
            return getTranslateAttributeValue($model, $key, 'en');
        } else if ((!empty($loca) or !$isEditModel) and !empty($model->translations) and count($model->translations)) { // if not default and English get translate by first locale
            $translations = $model->translations->first();

            return getTranslateAttributeValue($model, $key, $translations->locale);
        }

        return '';
    }
}

function getContentLocale()
{
    return session()->get('edit_content_locale', null);
}

function storeContentLocale($locale, $table, $item_id)
{
    removeContentLocale();

    $data = [
        'locale' => $locale,
        'table' => $table,
        'item_id' => $item_id
    ];

    session()->put('edit_content_locale', $data);
}

function removeContentLocale()
{
    session()->remove('edit_content_locale');
}

function getAgoraResolutions(): array
{
    return [
        '160_120', '120_120', '320_180', '180_180', '240_180', '320_240', '240_240', '424_240', '640_360', '360_360',
        '640_360', '360_360', '480_360', '480_360', '640_480', '480_480', '640_480', '480_480', '848_480', '848_480',
        '640_480', '1280_720', '1280_720', '960_720', '960_720', '1920_1080', '1920_1080', '1920_1080'
    ];
}


function getUserCurrencyItem($user = null, $userCurrency = null)
{
    $multiCurrency = new MultiCurrency();
    $currencies = $multiCurrency->getCurrencies();

    if (empty($userCurrency)) {
        $userCurrency = currency($user);
    }

    foreach ($currencies as $currencyItem) {
        if ($currencyItem->currency == $userCurrency) {
            return $currencyItem;
        }
    }

    return $multiCurrency->getDefaultCurrency();
}

function curformat($amount)
{
    // (A1) SPLIT WHOLE & DECIMALS
    $amount = explode(".", $amount);
    $whole = $amount[0];
    $decimal = isset($amount[1]) ? $amount[1] : "";

    // (A2) ADD THOUSAND SEPARATORS
    if (strlen($whole) > 3) {
        $temp = "";
        $j = 0;
        for ($i = strlen($whole) - 1; $i >= 0; $i--) {
            $temp = $whole[$i] . $temp;
            $j++;
            if ($j % 3 == 0 && $i != 0) {
                $temp = "," . $temp;
            }
        }
        $whole = $temp;
    }

    // (A3) RESULT
    return "\$$whole.$decimal";
}

function handlePriceFormat($price, $decimals = 0, $decimal_separator = '.', $thousands_separator = '')
{
    if ($price and $price > 0) {
        $format = number_format($price, $decimals, $decimal_separator, $thousands_separator);

        $str = "{$decimal_separator}";
        $i = 0;
        while ($i < $decimals) {
            $i += 1;
            $str .= "0";
        }

        return str_replace($str, "", $format);
    }

    return $price;
}

function handlePrice($price, $showCurrency = true, $format = true, $coursePagePrice = false, $user = null, $showTaxInPrice = false, $taxType = 'general')
{
    if (empty($price)) {
        return 0;
    }

    $userCurrencyItem = getUserCurrencyItem($user);
    $priceDisplay = getFinancialSettings('price_display') ?? 'only_price';

    $decimal = $userCurrencyItem->currency_decimal ?? 1;
    $decimalSeparator = $userCurrencyItem->currency_separator == "dot" ? '.' : ',';
    $thousandsSeparator = $userCurrencyItem->currency_separator == "dot" ? "," : ".";

    $price = convertPriceToUserCurrency($price, $userCurrencyItem);

    if ($priceDisplay != 'only_price') {
        $tax = getFinancialSettings('tax') ?? 0;

        if ($taxType == 'store') {
            $storeTax = getStoreSettings('store_tax');

            if (isset($storeTax) and is_numeric($storeTax)) {
                $tax = $storeTax;
            }
        }

        $tax = convertPriceToUserCurrency($tax, $userCurrencyItem);

        if ($tax > 0) {
            $taxPrice = $price * $tax / 100;

            if ($priceDisplay == 'total_price') {

                if ($showTaxInPrice) {
                    $price = $price + $taxPrice;
                }

                if ($format) {
                    $price = handlePriceFormat($price, $decimal, $decimalSeparator, $thousandsSeparator);
                }
            } elseif ($priceDisplay == 'price_and_tax') {
                if ($coursePagePrice) {
                    return [
                        'price' => $price,
                        'tax' => $taxPrice
                    ];
                }

                if ($format) {
                    $price = handlePriceFormat($price, $decimal, $decimalSeparator, $thousandsSeparator);
                    $taxPrice = handlePriceFormat($taxPrice, $decimal, $decimalSeparator, $thousandsSeparator);
                }

                if ($showCurrency) {
                    $price = addCurrencyToPrice($price, $userCurrencyItem);
                    $taxPrice = addCurrencyToPrice($taxPrice, $userCurrencyItem);
                }

                $price = $price . ($showTaxInPrice ? ('+' . $taxPrice . ' ' . trans('cart.tax')) : '');
            }
        }
    } elseif ($format) {
        $price = handlePriceFormat($price, $decimal, $decimalSeparator, $thousandsSeparator);
    }

    if ($coursePagePrice) {
        return [
            'price' => $price,
            'tax' => 0
        ];
    }

    if ($showCurrency and $priceDisplay != 'price_and_tax') {
        $price = addCurrencyToPrice($price, $userCurrencyItem);
    }

    return $price;
}

function convertPriceToUserCurrency($price, $userCurrencyItem = null)
{
    if (empty($userCurrencyItem)) {
        $userCurrencyItem = getUserCurrencyItem();
    }

    $exchangeRate = (!empty($userCurrencyItem) and $userCurrencyItem->exchange_rate) ? $userCurrencyItem->exchange_rate : 0;

    if ($exchangeRate > 0) {
        return $price * $exchangeRate;
    }

    return $price;
}

function convertPriceToDefaultCurrency($price, $userCurrencyItem = null)
{
    if (empty($userCurrencyItem)) {
        $userCurrencyItem = getUserCurrencyItem();
    }

    $exchangeRate = (!empty($userCurrencyItem) and $userCurrencyItem->exchange_rate) ? $userCurrencyItem->exchange_rate : 0;

    if ($exchangeRate > 0) {
        return $price / $exchangeRate;
    }

    return $price;
}

function addCurrencyToPrice($price, $userCurrencyItem = null)
{
    if (empty($userCurrencyItem)) {
        $userCurrencyItem = getUserCurrencyItem();
    }


    if (!empty($price)) {
        $currency = currencySign($userCurrencyItem->currency);
        $currencyPosition = $userCurrencyItem->currency_position;

        switch ($currencyPosition) {
            case 'left':
                $price = $currency . $price;
                break;

            case 'left_with_space':
                $price = $currency . ' ' . $price;
                break;

            case 'right':
                $price = $price . $currency;
                break;

            case 'right_with_space':
                $price = $price . ' ' . $currency;
                break;

            default:
                $price = $currency . $price;
        }
    }

    return $price;
}

/**
 * This text is for the course details page only and should not be used elsewhere. Use the "handlePrice" method for other places.
 * */
function handleCoursePagePrice($price)
{
    $result = handlePrice($price, true, true, true, null, true);

    $price = addCurrencyToPrice($result['price']);

    $tax = !empty($result['tax']) ? addCurrencyToPrice($result['tax']) : 0;

    return [
        'price' => $price,
        'tax' => $tax,
    ];
}


function checkShowCookieSecurityDialog()
{
    $show = false;

    if (getFeaturesSettings('cookie_settings_status')) {

        if (auth()->check()) {
            $checkDB = \App\Models\UserCookieSecurity::where('user_id', auth()->id())
                ->first();

            $show = empty($checkDB);
        } else {
            $checkCookie = Cookie::get('cookie-security');

            $show = empty($checkCookie);
        }
    }

    return $show;
}

function getNavbarButton($roleId = null, $forGuest = false)
{
    return \App\Models\NavbarButton::where('role_id', $roleId)
        ->where('for_guest', $forGuest)
        ->first();
}


function getLeafletApiPath()
{
    return "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
    //return 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';
}


function convertToMB($size, $unit = 'B')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $index = array_search($unit, $units);
    $bytes = $size * pow(1024, $index);

    $mb_size = $bytes / pow(1024, 2);

    return round($mb_size, 2);
}


function checkMobileNumber($phoneNumber)
{
    if (preg_match('/^\+\d{12}$/', $phoneNumber)) {
        return true;
    }

    return true;
}

function canDeleteContentDirectly()
{
    $result = false;

    $allowInstructorDeleteContent = !!(!empty(getGeneralOptionsSettings('allow_instructor_delete_content')));

    if ($allowInstructorDeleteContent) {
        $result = (!empty(getGeneralOptionsSettings('content_delete_method')) and getGeneralOptionsSettings('content_delete_method') == "delete_directly");
    }

    return $result;
}

function getRazorpayApiKey(): array
{
    $api_key = "";
    $api_secret = "";

    $paymentChannel = \App\Models\PaymentChannel::query()->where('class_name', 'Razorpay')->first();

    if (!empty($paymentChannel) and !empty($paymentChannel->credentials)) {
        if (!empty($paymentChannel->credentials['api_key'])) {
            $api_key = $paymentChannel->credentials['api_key'];
        }

        if (!empty($paymentChannel->credentials['api_secret'])) {
            $api_secret = $paymentChannel->credentials['api_secret'];
        }
    }

    return [
        'api_key' => $api_key,
        'api_secret' => $api_secret,
    ];
}


function customSortArrayNumAndTextIndex($array) {
    $numericKeys = [];
    $textualKeys = [];

    foreach ($array as $key => $value) {
        if (is_numeric($key)) {
            $numericKeys[$key] = $value;
        } else {
            $textualKeys[$key] = $value;
        }
    }

    ksort($numericKeys);

    return array_merge($numericKeys, $textualKeys);
}
