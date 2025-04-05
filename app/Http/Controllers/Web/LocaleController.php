<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocaleController extends Controller
{
    public function setLocale(Request $request)
    {
        $this->validate($request, [
            'locale' => 'required'
        ]);

        $locale = $request->get('locale');
        $locale = localeToCountryCode(mb_strtoupper($locale), true);

        $generalSettings = getGeneralSettings();
        $userLanguages = $generalSettings['user_languages'] ?? [];

        if (in_array($locale, $userLanguages)) {
            if (auth()->check()) {
                $user = auth()->user();
                $user->update([
                    'language' => $locale
                ]);
            } else {
                Cookie::queue('user_locale', $locale, 30 * 24 * 60);
            }
        }

        $previousUrl = $request->get('previous_url');

        if (!empty($previousUrl)) {
            return redirect($previousUrl);
        }

        return redirect()->back();
    }
}
