<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Noticeboard;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;

class NoticeboardController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filters(Noticeboard::query(), $request);

        $noticeboards = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $organizations = User::select('id', 'full_name', 'created_at')
            ->where('role_name', Role::$organization)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'pageTitle' => trans('panel.noticeboards'),
            'noticeboards' => $noticeboards,
            'organizations' => $organizations,
        ];

        return view('admin.noticeboards.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $sender = $request->get('sender', null);
        $type = $request->get('type', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {
            $query->where('title', 'like', "%$search%");
        }

        if (!empty($sender)) {
            switch ($sender) {
                case 'admin':
                    $query->whereNull('organ_id');
                    break;
                case 'organizations':
                    $query->whereNotNull('organ_id');
                    break;
            }
        }

        if (!empty($type)) {
            $query->where('type', $type);
        }

        return $query;
    }

    public function create()
    {
        $this->authorize('admin_noticeboards_send');

        $data = [
            'pageTitle' => trans('admin/main.new_notice_title')
        ];

        return view('admin.noticeboards.send', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_noticeboards_send');

        $this->validate($request, [
            'title' => 'required',
            'type' => 'required',
            'message' => 'required',
        ]);

        $data = $request->all();

        Noticeboard::create([
            'organ_id' => null,
            'type' => $data['type'],
            'sender' => 'Staff',
            'title' => $data['title'],
            'message' => $data['message'],
            'created_at' => time()
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('admin/main.send_noticeboard_success'),
            'status' => 'success'
        ];
        return redirect(getAdminPanelUrl().'/noticeboards')->with(['toast' => $toastData]);
    }

    public function edit($id)
    {
        $this->authorize('admin_noticeboards_edit');

        $noticeboard = Noticeboard::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/main.edit_noticeboard'),
            'noticeboard' => $noticeboard
        ];

        return view('admin.noticeboards.send', $data);
    }

    public function update(Request $request,$id)
    {
        $this->authorize('admin_noticeboards_edit');

        $this->validate($request, [
            'title' => 'required',
            'type' => 'required',
            'message' => 'required',
        ]);

        $data = $request->all();
        $noticeboard = Noticeboard::findOrFail($id);

        $noticeboard->update([
            'organ_id' => null,
            'type' => $data['type'],
            'sender' => 'Staff',
            'title' => $data['title'],
            'message' => $data['message'],
            'created_at' => time()
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('admin/main.edit_noticeboard_success'),
            'status' => 'success'
        ];
        return redirect(getAdminPanelUrl().'/noticeboards')->with(['toast' => $toastData]);
    }

    public function delete($id)
    {
        $this->authorize('admin_noticeboards_delete');

        $notification = Noticeboard::findOrFail($id);

        $notification->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('admin/main.delete_noticeboard_success'),
            'status' => 'success'
        ];
        return redirect(getAdminPanelUrl().'/noticeboards')->with(['toast' => $toastData]);
    }
}
