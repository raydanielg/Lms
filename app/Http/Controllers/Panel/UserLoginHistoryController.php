<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\UserLoginHistory;
use Illuminate\Http\Request;

class UserLoginHistoryController extends Controller
{

    public function endSession($sessionId)
    {
        $user = auth()->user();

        $session = UserLoginHistory::query()->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->first();

        if (!empty($session)) {
            $session->update([
                'session_end_at' => time(),
                'end_session_type' => 'by_user'
            ]);

            $sessionManager = app('session');
            $sessionManager->getHandler()->destroy($session->session_id);
        }


        return response()->json([
            'code' => 200,
            'title' => trans('public.request_success'),
            'text' => trans('update.login_session_successful_deleted'),
        ]);
    }
}
