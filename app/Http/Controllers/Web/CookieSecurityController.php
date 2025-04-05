<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\UserCookieSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CookieSecurityController extends Controller
{
    public $cookieKey = 'cookie-security';

    public function setAll()
    {
        $this->handleStore(UserCookieSecurity::$ALL, null);

        return response()->json([
            'code' => 200,
            'msg' => trans('update.cookie_security_successfully_submitted')
        ]);
    }

    public function setCustomize(Request $request)
    {
        $settings = $request->get('settings');

        $this->handleStore(UserCookieSecurity::$CUSTOMIZE, json_encode($settings));

        return response()->json([
            'code' => 200,
            'msg' => trans('update.cookie_security_successfully_submitted')
        ]);
    }

    private function handleStore($type, $settings)
    {
        $user = auth()->user();

        $data = [
            'type' => $type,
            'settings' => $settings,
            'created_at' => time()
        ];

        if (!empty($user)) {
            UserCookieSecurity::updateOrCreate([
                'user_id' => $user->id,
            ],
                $data
            );
        } else {
            Cookie::queue($this->cookieKey, json_encode($data), 30 * 24 * 60);
        }
    }
}
