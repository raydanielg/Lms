<?php

namespace App\Http\Controllers\Admin\traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait DeviceLimitSettings
{

    public function resetUsersLoginCount(Request $request)
    {
        $this->authorize('admin_settings');

        DB::table('users')->update([
            'logged_count' => 0
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.all_users_logged_count_reseted_successful'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
