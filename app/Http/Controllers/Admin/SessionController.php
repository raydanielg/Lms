<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Translation\SessionTranslation;
use App\Models\Webinar;
use App\Models\WebinarChapterItem;
use App\Sessions\Zoom;
use App\Sessions\ZoomOAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Validator;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required|max:64',
            'date' => 'required|date',
            'duration' => 'required|numeric',
            'link' => ($data['session_api'] == 'local') ? 'required|url' : 'nullable',
            'api_secret' => (in_array($data['session_api'], ['zoom', 'agora', 'jitsi'])) ? 'nullable' : 'required',
            'moderator_secret' => ($data['session_api'] == 'big_blue_button') ? 'required' : 'nullable',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
            $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
            $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
        } else {
            $data['check_previous_parts'] = false;
            $data['access_after_day'] = null;
        }

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])->first();

            if (!empty($webinar)) {
                $teacher = $webinar->creator;

                if (!empty($data['session_api']) and $data['session_api'] == 'zoom' and empty(getFeaturesSettings('zoom_client_id'))) {
                    $error = [
                        'zoom-not-complete-alert' => []
                    ];

                    return response([
                        'code' => 422,
                        'errors' => $error,
                    ], 422);
                }


                $sessionDate = convertTimeToUTCzone($data['date'], $webinar->timezone);

                if ($sessionDate->getTimestamp() < $webinar->start_date) {
                    $error = [
                        'date' => [trans('webinars.session_date_must_larger_webinar_start_date', ['start_date' => dateTimeFormat($webinar->start_date, 'j M Y')])]
                    ];

                    return response([
                        'code' => 422,
                        'errors' => $error,
                    ], 422);
                }

                $session = Session::create([
                    'creator_id' => $teacher->id,
                    'webinar_id' => $data['webinar_id'],
                    'chapter_id' => $data['chapter_id'] ?? null,
                    'date' => $sessionDate->getTimestamp(),
                    'duration' => $data['duration'],
                    'extra_time_to_join' => $data['extra_time_to_join'] ?? null,
                    'link' => $data['link'] ?? '',
                    'session_api' => $data['session_api'],
                    'api_secret' => $data['api_secret'] ?? '',
                    'moderator_secret' => $data['moderator_secret'] ?? '',
                    'check_previous_parts' => $data['check_previous_parts'],
                    'access_after_day' => $data['access_after_day'],
                    'status' => (!empty($data['status']) and $data['status'] == 'on') ? Session::$Active : Session::$Inactive,
                    'created_at' => time()
                ]);

                if (!empty($session)) {
                    SessionTranslation::updateOrCreate([
                        'session_id' => $session->id,
                        'locale' => mb_strtolower($data['locale']),
                    ], [
                        'title' => $data['title'],
                        'description' => $data['description'],
                    ]);
                }

                if ($data['session_api'] == 'big_blue_button') {
                    $this->handleBigBlueButtonApi($session, $teacher);
                } elseif ($data['session_api'] == 'zoom') {
                    $zoomResult = $this->handleZoomApi($session, $teacher);

                    if ($zoomResult != "ok") {
                        return $zoomResult;
                    }
                } else if ($data['session_api'] == 'agora') {
                    $agoraSettings = [
                        'chat' => (!empty($data['agora_chat']) and $data['agora_chat'] == 'on'),
                        'record' => (!empty($data['agora_record']) and $data['agora_record'] == 'on'),
                    ];
                    $session->agora_settings = json_encode($agoraSettings);

                    $session->save();
                }

                if (!empty($session) and !empty($session->chapter_id)) {
                    WebinarChapterItem::makeItem($webinar->creator_id, $session->chapter_id, $session->id, WebinarChapterItem::$chapterSession);
                }

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->get('ajax')[$id];
        $session = Session::where('id', $id)
            ->first();

        $session_api = !empty($data['session_api']) ? $data['session_api'] : $session->session_api;

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required|max:64',
            'date' => ($session_api == 'local') ? 'required|date' : 'nullable',
            'duration' => ($session_api == 'local') ? 'required|numeric' : 'nullable',
            'link' => ($session_api == 'local') ? 'required|url' : 'nullable',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
            $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
            $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
        } else {
            $data['check_previous_parts'] = false;
            $data['access_after_day'] = null;
        }

        $webinar = Webinar::where('id', $data['webinar_id'])->first();

        if (!empty($webinar)) {
            if (!empty($session)) {
                $sessionDate = $session->date;

                if (!empty($data['date'])) {
                    $sessionDate = convertTimeToUTCzone($data['date'], $webinar->timezone);

                    if ($sessionDate->getTimestamp() < $webinar->start_date) {
                        $error = [
                            'date' => [trans('webinars.session_date_must_larger_webinar_start_date', ['start_date' => dateTimeFormat($webinar->start_date, 'j M Y')])]
                        ];

                        return response([
                            'code' => 422,
                            'errors' => $error,
                        ], 422);
                    }

                    $sessionDate = $sessionDate->getTimestamp();
                }

                $agoraSettings = null;
                if ($session_api == 'agora') {
                    $agoraSettings = [
                        'chat' => (!empty($data['agora_chat']) and $data['agora_chat'] == 'on'),
                        'record' => (!empty($data['agora_record']) and $data['agora_record'] == 'on'),
                    ];
                    $agoraSettings = json_encode($agoraSettings);
                }

                $changeChapter = ($data['chapter_id'] != $session->chapter_id);
                $oldChapterId = $session->chapter_id;

                $session->update([
                    'chapter_id' => $data['chapter_id'],
                    'date' => $sessionDate,
                    'duration' => $data['duration'] ?? $session->duration,
                    'extra_time_to_join' => $data['extra_time_to_join'] ?? null,
                    'link' => $data['link'] ?? $session->link,
                    'session_api' => $session_api,
                    'api_secret' => $data['api_secret'] ?? $session->api_secret,
                    'status' => (!empty($data['status']) and $data['status'] == 'on') ? Session::$Active : Session::$Inactive,
                    'agora_settings' => $agoraSettings,
                    'check_previous_parts' => $data['check_previous_parts'],
                    'access_after_day' => $data['access_after_day'],
                    'updated_at' => time()
                ]);

                SessionTranslation::updateOrCreate([
                    'session_id' => $session->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);

                if ($changeChapter) {
                    WebinarChapterItem::changeChapter($session->creator_id, $oldChapterId, $session->chapter_id, $session->id, WebinarChapterItem::$chapterSession);
                }

                removeContentLocale();

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        removeContentLocale();

        return response()->json([], 422);
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $session = Session::find($id);

        if (!empty($session)) {
            WebinarChapterItem::where('user_id', $session->creator_id)
                ->where('item_id', $session->id)
                ->where('type', WebinarChapterItem::$chapterSession)
                ->delete();

            $session->delete();
        }

        return response()->json([
            'code' => 200,
        ], 200);
    }

    private function handleZoomApi($session, $user)
    {
        try {
            if (!empty(getFeaturesSettings('zoom_client_id')) and !empty(getFeaturesSettings('zoom_client_secret'))) {
                $meeting = (new ZoomOAuth())->makeMeeting($session);

                if ($meeting) {
                    return "ok";
                } else {
                    $session->delete();
                }
            }
        } catch (\Exception $exception) {
            $session->delete();
            //dd($exception);
        }

        return response()->json([
            'code' => 422,
            'status' => 'zoom_token_invalid',
            'zoom_error_msg' => trans('update.zoom_error_msg')
        ], 422);
    }

    private function handleBigBlueButtonApi($session, $user)
    {
        $this->handleBigBlueButtonConfigs();

        $createMeeting = \Bigbluebutton::initCreateMeeting([
            'meetingID' => $session->id,
            'meetingName' => $session->title,
            'attendeePW' => $session->api_secret,
            'moderatorPW' => $session->moderator_secret,
        ]);

        $createMeeting->setDuration($session->duration);
        $response = \Bigbluebutton::create($createMeeting);

        return true;
    }

    private function handleBigBlueButtonConfigs()
    {
        $settings = getFeaturesSettings();

        \Config::set("bigbluebutton.BBB_SECURITY_SALT", !empty($settings['bigbluebutton_security_salt']) ? $settings['bigbluebutton_security_salt'] : '');
        \Config::set("bigbluebutton.BBB_SERVER_BASE_URL", !empty($settings['bigbluebutton_server_base_url']) ? $settings['bigbluebutton_server_base_url'] : '');
    }
}
