<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AgoraHistoryExport;
use App\Exports\WaitlistItemsExport;
use App\Exports\WaitlistsExport;
use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WaitlistController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_waitlists_lists');

        $waitlists = Webinar::query()
            ->where('enable_waitlist', true)
            ->paginate(10);

        foreach ($waitlists as $waitlist) {
            $query = Waitlist::query()->where('webinar_id', $waitlist->id);

            $waitlist->members = deepClone($query)->count();
            $waitlist->registered_members = deepClone($query)->whereNotNull('user_id')->count();

            $lastSubmission = deepClone($query)->orderBy('created_at', 'desc')->first();

            if (!empty($lastSubmission)) {
                $waitlist->last_submission = $lastSubmission->created_at;
            }
        }

        $data = [
            'pageTitle' => trans('update.waitlists'),
            'waitlists' => $waitlists
        ];

        return view('admin.waitlists.index', $data);
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_waitlists_exports');

        $waitlists = Webinar::query()
            ->where('enable_waitlist', true)
            ->get();

        foreach ($waitlists as $waitlist) {
            $query = Waitlist::query()->where('webinar_id', $waitlist->id);

            $waitlist->members = deepClone($query)->count();
            $waitlist->registered_members = deepClone($query)->whereNotNull('user_id')->count();

            $lastSubmission = deepClone($query)->orderBy('created_at', 'desc')->first();

            if (!empty($lastSubmission)) {
                $waitlist->last_submission = $lastSubmission->created_at;
            }
        }

        $export = new WaitlistsExport($waitlists);

        return Excel::download($export, 'waitlists.xlsx');
    }

    public function viewList(Request $request, $webinarId, $justReturnQuery = false)
    {
        $this->authorize('admin_waitlists_users');

        $webinar = Webinar::query()->findOrFail($webinarId);

        $query = Waitlist::query()->where('webinar_id', $webinarId);

        $from = $request->get('from');
        $to = $request->get('to');
        $search = $request->get('search');
        $registrationStatus = $request->get('registration_status');

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('full_name', 'like', "%$search%");
                $query->orWhereHas('user', function ($query) use ($search) {
                    $query->where('full_name', 'like', "%$search%");
                });
            });
        }

        if (!empty($registrationStatus)) {
            if ($registrationStatus == 'registered') {
                $query->whereNotNull('user_id');
            } else if ($registrationStatus == 'unregistered') {
                $query->whereNull('user_id');
            }
        }

        if ($justReturnQuery) {
            return $query;
        }

        $waitlists = $query
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.waitlists') . ' - ' . $webinar->title,
            'waitlists' => $waitlists,
            'waitlistId' => $webinarId,
            'webinarTitle' => $webinar->title,
        ];

        return view('admin.waitlists.users_list', $data);
    }

    public function clearList(Request $request, $webinarId)
    {
        $this->authorize('admin_waitlists_clear_list');

        Waitlist::query()->where('webinar_id', $webinarId)
            ->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.waitlist_cleared_successful'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function disableWaitlist(Request $request, $webinarId)
    {
        $this->authorize('admin_waitlists_disable');

        $webinar = Webinar::query()->findOrFail($webinarId);

        $webinar->update([
            'enable_waitlist' => false
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.waitlist_disabled_successful'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function exportUsersList(Request $request, $webinarId)
    {
        $this->authorize('admin_waitlists_exports');

        $waitlists = $this->viewList($request, $webinarId, true)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $export = new WaitlistItemsExport($waitlists);

        return Excel::download($export, 'waitlist_items.xlsx');
    }


    public function deleteWaitlistItems($waitlistId)
    {
        $this->authorize('admin_waitlists_users');

        Waitlist::query()
            ->where('id', $waitlistId)
            ->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.waitlist_item_deleted_successful'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
