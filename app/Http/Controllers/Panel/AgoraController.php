<?php

namespace App\Http\Controllers\Panel;

use App\Agora\RtcTokenBuilder;
use App\Agora\RtmTokenBuilder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgoraController extends Controller
{
    public $appId;
    private $appCertificate;

    public function __construct()
    {
        $settings = getFeaturesSettings();

        $this->appId = !empty($settings['agora_app_id']) ? $settings['agora_app_id'] : '';
        $this->appCertificate = !empty($settings['agora_app_certificate']) ? $settings['agora_app_certificate'] : '';
    }

    /*public function index(Request $request)
    {
        $user = auth()->user();

        $channelName = 'channelName';
        $accountName = $user->full_name;
        $streamRole = $user->id == 903 ? 'host' : 'audience'; // host | audience

        $rtcToken = $this->getRTCToken($channelName);
        $rtmToken = $this->getRTMToken($accountName);

        $data = [
            'isHost' => $user->id == 903,
            'appId' => $this->appId,
            'accountName' => $accountName,
            'channelName' => $channelName,
            'rtcToken' => $rtcToken,
            'rtmToken' => $rtmToken,
            'streamRole' => $streamRole,
        ];

        return view('web.default.course.agora.index', $data);
    }*/

    public function getRTCToken(string $channelName, bool $isHost): string
    {
        $role = $isHost ? RtcTokenBuilder::RolePublisher : RtcTokenBuilder::RoleAttendee;

        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        return RtcTokenBuilder::buildTokenWithUserAccount($this->appId, $this->appCertificate, $channelName, null, $role, $privilegeExpiredTs);
    }

    public function getRTMToken($channelName): string
    {
        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        return RtmTokenBuilder::buildToken($this->appId, $this->appCertificate, $channelName, null, $privilegeExpiredTs);
    }
}
