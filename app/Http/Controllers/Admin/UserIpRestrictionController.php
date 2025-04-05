<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpRestriction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserIpRestrictionController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('admin_user_ip_restriction_lists');

        $query = IpRestriction::query();

        $restrictions = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.ip_restriction'),
            'restrictions' => $restrictions
        ];

        return view('admin.users.restrictions.lists.index', $data);
    }

    public function getForm(Request $request)
    {
        $this->authorize('admin_user_ip_restriction_create');

        $fullIP = $request->get('full_ip');

        $data = [
            'defaultFullIP' => $fullIP
        ];

        $html = (string)view()->make('admin.users.restrictions.lists.restriction_modal', $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_user_ip_restriction_create');
        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => 'required',
            'full_ip' => 'required_if:type,full_ip',
            'ip_range' => 'required_if:type,ip_range',
            'country' => 'required_if:type,country',
            'reason' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $restriction = IpRestriction::query()
            ->where('type', $data['type'])
            ->where('value', $data[$data['type']])
            ->first();

        if (!empty($restriction)) {
            return response([
                'code' => 422,
                'errors' => [
                    'full_ip' => [trans('update.full_ip_unique')]
                ],
            ], 422);
        }

        IpRestriction::query()->create([
            'type' => $data['type'],
            'value' => $data[$data['type']],
            'reason' => $data['reason'],
            'created_at' => time(),
        ]);

        return response()->json([
            'code' => 200,
            'title' => trans('public.request_success'),
            'msg' => trans('update.new_restriction_created_successful')
        ]);
    }

    public function edit($id)
    {
        $this->authorize('admin_user_ip_restriction_create');

        $restriction = IpRestriction::query()->findOrFail($id);

        $data = [
            'restriction' => $restriction,
        ];

        $html = (string)view()->make('admin.users.restrictions.lists.restriction_modal', $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_user_ip_restriction_create');

        $restriction = IpRestriction::query()->findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => 'required',
            'full_ip' => 'required_if:type,full_ip',
            'ip_range' => 'required_if:type,ip_range',
            'country' => 'required_if:type,country',
            'reason' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $restriction->update([
            'type' => $data['type'],
            'value' => $data[$data['type']],
            'reason' => $data['reason'],
        ]);

        return response()->json([
            'code' => 200,
            'title' => trans('public.request_success'),
            'msg' => trans('update.restriction_updated_successful')
        ]);
    }

    public function delete(Request $request, $id)
    {
        $this->authorize('admin_user_ip_restriction_delete');

        $restriction = IpRestriction::query()->findOrFail($id);
        $restriction->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.restriction_deleted_successful'),
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }
}
