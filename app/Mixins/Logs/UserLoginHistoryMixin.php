<?php

namespace App\Mixins\Logs;

use App\Models\UserLoginHistory;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class UserLoginHistoryMixin
{
    protected $browser;
    protected $deviceType;
    protected $os;

    public function __construct()
    {
        $agent = new Agent();

        $this->browser = $agent->browser();
        $this->deviceType = $agent->deviceType();

        $platform = $agent->platform();
        $version = $agent->version($platform);

        $this->os = "$platform-$version";
    }


    public function storeUserLoginHistory($user)
    {
        $ipAddress = $_SERVER['REMOTE_ADDR']; // "46.143.59.39"
        $country = null;
        $city = null;
        $location = null;

        $locationData = $this->getUserLocation($ipAddress);
        if (!empty($locationData) and !empty($locationData['status']) and $locationData['status'] == "success") {
            $country = $locationData['country'] ?? null;
            $city = $locationData['city'] ?? null;
            $location = (!empty($locationData['lat']) and !empty($locationData['lon'])) ? "{$locationData['lat']},{$locationData['lon']}" : null;
        } else {
            $ipAddress = null;
        }

        $userSession = session()->getId();

        UserLoginHistory::query()->create([
            'user_id' => $user->id,
            'browser' => $this->browser,
            'device' => $this->deviceType,
            'os' => $this->os,
            'ip' => $ipAddress,
            'country' => $country,
            'city' => $city,
            'location' => !empty($location) ? DB::raw("point({$location})") : null,
            'session_id' => $userSession,
            'session_start_at' => time(),
            'session_end_at' => null,
            'created_at' => time(),
        ]);


    }

    public function storeUserLogoutHistory($userId)
    {
        $session = UserLoginHistory::query()
            ->where('user_id', $userId)
            ->where('browser', $this->browser)
            ->where('device', $this->deviceType)
            ->where('os', $this->os)
            ->whereNull('session_end_at')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!empty($session)) {
            $session->update([
                'session_end_at' => time(),
                'end_session_type' => 'default'
            ]);

            $sessionManager = app('session');
            $sessionManager->getHandler()->destroy($session->session_id);
        }
    }

    private function getUserLocation($ipAddress)
    {
        $response = Http::get("http://ip-api.com/json/{$ipAddress}");
        $locationData = $response->json();

        return $locationData;
    }

}
