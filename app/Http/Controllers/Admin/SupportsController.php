<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Support;
use App\Models\SupportConversation;
use App\Models\SupportDepartment;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_supports_list');

        $query = Support::select('*',DB::raw("case
            when status = 'open' then 'a'
            when status = 'replied' then 'a'
            when status = 'supporter_replied' then 'b'
            when status = 'close' then 'c'
            end as status_order
        "));

        if (!empty($request->get('type')) and $request->get('type') == 'course_conversations') {
            $query->whereNotNull('webinar_id')
                ->with([
                    'webinar' => function ($qu) {
                        $qu->with([
                            'teacher' => function ($qu) {
                                $qu->select('id', 'full_name');
                            }
                        ]);
                    }
                ]);
        } else {
            $query->whereNotNull('department_id');
        }

        $totalConversations = deepClone($query)->count();
        $openConversationsCount = deepClone($query)->where('status', '!=', 'close')->count();
        $closeConversationsCount = deepClone($query)->where('status', 'close')->count();
        $classesWithSupport = 0;
        $pendingReplySupports = 0;

        if (!empty($request->get('type')) and $request->get('type') == 'course_conversations') {
            $classesWithSupport = Webinar::where('support', true)
                ->where('status', 'active')
                ->count();
        } else {
            $pendingReplySupports = deepClone($query)->whereIn('status', ['open', 'replied'])->count();
        }

        $query = $this->handleFilters($request, $query);

        $supports = $query->orderBy('status_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->with([
                'department',
                'user' => function ($qu) {
                    $qu->select('id', 'full_name', 'role_name');
                }
            ])->paginate(10);

        $departments = SupportDepartment::all();
        $roles = Role::all();

        $data = [
            'pageTitle' => trans('admin/pages/users.supports'),
            'supports' => $supports,
            'totalConversations' => $totalConversations,
            'pendingReplySupports' => $pendingReplySupports,
            'openConversationsCount' => $openConversationsCount,
            'closeConversationsCount' => $closeConversationsCount,
            'classesWithSupport' => $classesWithSupport,
            'departments' => $departments,
            'roles' => $roles,
        ];

        if (!empty($request->get('type')) and $request->get('type') == 'course_conversations') {
            $data['pageTitle'] = trans('admin/main.classes_conversations');
            $teacher_ids = $request->get('teacher_ids');
            $student_ids = $request->get('student_ids');
            $webinar_ids = $request->get('webinar_ids');

            if (!empty($teacher_ids)) {
                $data['teachers'] = User::select('id', 'full_name')
                    ->whereIn('id', $teacher_ids)->get();
            }

            if (!empty($student_ids)) {
                $data['students'] = User::select('id', 'full_name')
                    ->whereIn('id', $student_ids)->get();
            }

            if (!empty($webinar_ids)) {
                $data['webinars'] = Webinar::select('id')
                    ->whereIn('id', $webinar_ids)->get();
            }

            return view('admin.supports.course_conversations_lists', $data);
        }

        return view('admin.supports.lists', $data);
    }

    private function handleFilters($request, $query)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $status = $request->get('status');
        $teacher_ids = $request->get('teacher_ids');
        $student_ids = $request->get('student_ids');
        $webinar_ids = $request->get('webinar_ids');
        $webinarTitle = $request->get('webinar_title');
        $title = $request->get('title');
        $date = $request->get('date');
        $department_id = $request->get('department_id');
        $role_id = $request->get('role_id');

        if (!empty($title)) {
            $query->where('title', 'like', "%$title%");
        }

        if (!empty($from) and !empty($to)) {
            $from = strtotime($from);
            $to = strtotime($to);

            $query->whereBetween('created_at', [$from, $to]);
        } else {
            if (!empty($from)) {
                $from = strtotime($from);
                $query->where('created_at', '>=', $from);
            }

            if (!empty($to)) {
                $to = strtotime($to);

                $query->where('created_at', '<', $to);
            }
        }

        if (!empty($date)) {
            $timestamp = strtotime($date);
            $beginOfDay = strtotime("today", $timestamp);
            $endOfDay = strtotime("tomorrow", $beginOfDay) - 1;

            $query->whereBetween('created_at', [$beginOfDay, $endOfDay]);
        }

        if (!empty($department_id)) {
            $query->where('department_id', $department_id);
        }

        if (!empty($role_id)) {
            $query->whereHas('user', function ($query) use ($role_id) {
                $query->where('role_id', $role_id);
            });
        }

        if (!empty($webinarTitle)) {
            $webinar_ids = Webinar::where('status', 'active')
                ->whereTranslationLike('title', "%$webinarTitle%")
                ->pluck('id')
                ->toArray();
        }

        if (!empty($webinar_ids)) {
            $query->whereIn('webinar_id', $webinar_ids);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($teacher_ids)) {

            $webinarIds = Webinar::where('status', 'active')
                ->where(function ($qu) use ($teacher_ids) {
                    $qu->whereIn('creator_id', $teacher_ids)
                        ->orWhereIn('teacher_id', $teacher_ids);
                })
                ->pluck('id')
                ->toArray();

            $query->where(function ($query) use ($webinarIds, $teacher_ids) {
                $query->whereIn('webinar_id', $webinarIds)
                    ->orWhereIn('user_id', $teacher_ids);
            });
        }


        if (!empty($student_ids)) {
            $query->whereIn('user_id', $student_ids);
        }

        return $query;
    }

    public function create(Request $request)
    {
        $this->authorize('admin_support_send');

        $user_id = $request->get('user_id');
        $toUser = null;
        if (!empty($user_id)) {
            $toUser = User::find($user_id);
        }

        $departments = SupportDepartment::all();

        $data = [
            'pageTitle' => trans('admin/main.new_support_ticket_title'),
            'departments' => $departments,
            'toUser' => $toUser
        ];

        return view('admin.supports.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_support_send');

        $this->validate($request, [
            'title' => 'required|min:2',
            'department_id' => 'required|exists:support_departments,id',
            'user_id' => 'required|exists:users,id',
            'message' => 'required',
        ]);

        $data = $request->all();

        $support = Support::create([
            'user_id' => $data['user_id'],
            'department_id' => $data['department_id'],
            'title' => $data['title'],
            'status' => 'open',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        SupportConversation::create([
            'support_id' => $support->id,
            'supporter_id' => auth()->id(),
            'message' => $data['message'],
            'attach' => $data['attach'],
            'created_at' => time(),
        ]);

        return redirect(getAdminPanelUrl().'/supports/' . $support->id . '/conversation');
    }

    public function edit($id)
    {
        $this->authorize('admin_supports_reply');

        $support = Support::where('id', $id)
            ->whereNotNull('department_id')
            ->first();

        if (empty($support)) {
            abort(404);
        }

        $departments = SupportDepartment::all();

        $data = [
            'pageTitle' => trans('admin/main.edit_support_ticket_title'),
            'departments' => $departments,
            'support' => $support,
        ];

        return view('admin.supports.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_supports_reply');

        $this->validate($request, [
            'title' => 'required|min:2',
            'department_id' => 'required|exists:support_departments,id',
            'user_id' => 'required|exists:users,id',
            'message' => 'required',
        ]);

        $data = $request->all();

        $support = Support::where('id', $id)
            ->whereNotNull('department_id')
            ->first();

        if (empty($support)) {
            abort(404);
        }

        $support->update([
            'user_id' => $data['user_id'],
            'department_id' => $data['department_id'],
            'title' => $data['title'],
            'status' => 'open',
            'updated_at' => time(),
        ]);

        return redirect(getAdminPanelUrl().'/supports');
    }

    public function delete($id)
    {
        $this->authorize('admin_supports_delete');

        $support = Support::where('id', $id)
            ->whereNotNull('department_id')
            ->first();

        if (empty($support)) {
            abort(404);
        }

        $support->delete();

        return redirect(getAdminPanelUrl().'/supports');
    }

    public function conversationClose($id)
    {
        $this->authorize('admin_supports_reply');

        $support = Support::where('id', $id)
            ->first();

        if (empty($support)) {
            abort(404);
        }

        $support->update([
            'status' => 'close',
            'updated_at' => time()
        ]);

        return redirect(getAdminPanelUrl().'/supports/' . $support->id . '/conversation');
    }

    public function conversation($id)
    {
        $this->authorize('admin_supports_reply');

        $support = Support::where('id', $id)
            ->where(function ($query) {
                $query->whereNotNull('department_id')
                    ->orWhereNotNull('webinar_id');
            })
            ->with(['department', 'conversations' => function ($query) {
                $query->with(['sender' => function ($qu) {
                    $qu->select('id', 'full_name', 'avatar');
                }, 'supporter' => function ($qu) {
                    $qu->select('id', 'full_name', 'avatar');
                }]);
                $query->orderBy('created_at', 'asc');
            }, 'user' => function ($qu) {
                $qu->select('id', 'full_name', 'role_name');
            }, 'webinar' => function ($qu) {
                $qu->with(['teacher' => function ($qu) {
                    $qu->select('id', 'full_name');
                }]);
            }])->first();

        if (empty($support)) {
            abort(404);
        }

        $data = [
            'pageTitle' => trans('admin/pages/users.support_conversation'),
            'support' => $support
        ];

        return view('admin.supports.conversation', $data);
    }

    public function storeConversation(Request $request, $id)
    {
        $this->authorize('admin_supports_reply');

        $this->validate($request, [
            'message' => 'required|string|min:2',
        ]);

        $data = $request->all();
        $support = Support::where('id', $id)
            //->whereNotNull('department_id')
            ->first();

        if (empty($support)) {
            abort(404);
        }

        $support->update([
            'status' => 'supporter_replied',
            'updated_at' => time()
        ]);

        SupportConversation::create([
            'support_id' => $support->id,
            'supporter_id' => auth()->id(),
            'message' => $data['message'],
            'attach' => $data['attach'],
            'created_at' => time(),
        ]);

        return redirect(getAdminPanelUrl().'/supports/' . $support->id . '/conversation');
    }
}
