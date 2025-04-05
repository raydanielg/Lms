<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Panel\AgoraController;
use App\Http\Resources\SessionResource;
use App\Models\AgoraHistory;
use App\Models\Api\WebinarChapter;
use App\Models\File;
use App\Models\Sale;
use App\Models\Api\Session;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function show($id)
    {
        $session = Session::where('id', $id)
            ->where('status', WebinarChapter::$chapterActive)->first();
        abort_unless($session, 404);
        if ($error = $session->canViewError()) {
            //       return $this->failure($error, 403, 403);
        }
        $resource = new SessionResource($session);
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $resource);
    }

    public function BigBlueButton(Request $request,$session_id)
    {

        $user = apiAuth();
        Auth::login($user);

        return redirect(url('panel/sessions/' . $session_id . '/joinToBigBlueButton'));

    }

    public function agora(Request $request,$session_id)
    {

        $user = apiAuth();
        Auth::login($user);

        return redirect(url('panel/sessions/' . $session_id . '/joinToAgora'));
    }
}
