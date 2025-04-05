<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumTopicPost;
use App\Models\Group;
use App\Models\Role;
use App\Models\Translation\CategoryTranslation;
use App\Models\Translation\ForumTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        removeContentLocale();

        $this->authorize('admin_forum_list');

        $subForums = $request->get('subForums');

        $forums = Forum::where(function ($query) use ($subForums) {
            if (!empty($subForums)) {
                $query->where('parent_id', $subForums);
            } else {
                $query->whereNull('parent_id');
            }
        })
            ->with([
                'subForums' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->orderBy('id', 'desc')
            ->paginate(10);

        foreach ($forums as $forum) {
            $forumIds = Forum::where('parent_id', $forum->id)->pluck('id')->toArray();
            $forumIds[] = $forum->id;

            $topicsIds = ForumTopic::whereIn('forum_id', $forumIds)->pluck('id')->toArray();

            $forum->topics_count = count($topicsIds);
            $forum->posts_count = ForumTopicPost::whereIn('topic_id', $topicsIds)->count();
        }

        $totalForums = Forum::query()->whereDoesntHave('subForums')->count();
        $totalTopics = ForumTopic::query()->count();
        $postsCount = ForumTopicPost::query()->count();
        $membersCount = ForumTopicPost::select(DB::raw('count(distinct user_id) as count'))->first()->count;

        $data = [
            'pageTitle' => trans('update.forums'),
            'forums' => $forums,
            'totalForums' => $totalForums,
            'totalTopics' => $totalTopics,
            'postsCount' => $postsCount,
            'membersCount' => $membersCount,
        ];

        return view('admin.forums.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_forum_create');

        $userGroups = Group::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::all();

        $data = [
            'pageTitle' => trans('update.new_forum'),
            'userGroups' => $userGroups,
            'roles' => $roles
        ];

        return view('admin.forums.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_forum_create');

        $this->validate($request, [
            'title' => 'required|min:3|max:255',
            'description' => 'required',
            'icon' => 'required',
            'role_id' => 'nullable|exists:roles,id',
            'group_id' => 'nullable|exists:groups,id',
            'status' => 'in:active,disabled',
        ]);
        $data = $request->all();

        $forum = Forum::create([
            'slug' => Forum::makeSlug($data['title']),
            'icon' => $data['icon'],
            'group_id' => $data['group_id'] ?? null,
            'role_id' => $data['role_id'] ?? null,
            'status' => $data['status'],
            'close' => (!empty($data['close']) and $data['close'] == 1),
        ]);

        ForumTranslation::updateOrCreate([
            'forum_id' => $forum->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        $hasSubForum = (!empty($request->get('has_sub')) and $request->get('has_sub') == 'on');
        $this->setSubForum($forum, $request->get('sub_forums'), $hasSubForum, $data['locale']);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/forums');
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_forum_edit');

        $forum = Forum::findOrFail($id);
        $subForums = Forum::where('parent_id', $forum->id)
            ->orderBy('order', 'asc')
            ->get();

        $userGroups = Group::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::all();

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $forum->getTable(), $forum->id);

        $data = [
            'pageTitle' => trans('admin/main.edit'),
            'forum' => $forum,
            'subForums' => $subForums,
            'userGroups' => $userGroups,
            'roles' => $roles
        ];

        return view('admin.forums.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_forum_edit');

        $this->validate($request, [
            'title' => 'required|min:3|max:255',
            'description' => 'required',
            'icon' => 'required',
            'group_id' => 'nullable|exists:groups,id',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'in:active,disabled',
        ]);

        $data = $request->all();

        $forum = Forum::findOrFail($id);
        $forum->update([
            'icon' => $data['icon'],
            'group_id' => $data['group_id'] ?? null,
            'role_id' => $data['role_id'] ?? null,
            'status' => $data['status'],
            'close' => (!empty($data['close']) and $data['close'] == 1),
        ]);

        ForumTranslation::updateOrCreate([
            'forum_id' => $forum->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        $hasSubForums = (!empty($request->get('has_sub')) and $request->get('has_sub') == 'on');
        $this->setSubForum($forum, $request->get('sub_forums'), $hasSubForums, $data['locale']);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/forums');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_forum_delete');

        $forum = Forum::where('id', $id)->first();

        if (!empty($forum)) {
            Forum::where('parent_id', $forum->id)
                ->delete();

            $forum->delete();
        }

        return redirect(getAdminPanelUrl().'/forums');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        $option = $request->get('option', null);

        $query = Forum::select('id')
            ->whereTranslationLike('title', "%$term%");

        /*if (!empty($option)) {

        }*/

        $forums = $query->get();

        return response()->json($forums, 200);
    }

    public function searchTopics(Request $request)
    {
        $term = $request->get('term');

        $option = $request->get('option', null);

        $query = ForumTopic::select('id', 'title')
            ->where('title', 'like', "%$term%");

        $topics = $query->get();

        return response()->json($topics, 200);
    }

    public function setSubForum(Forum $forum, $subForums, $hasSubForums, $locale)
    {
        $order = 1;
        $oldIds = [];

        if ($hasSubForums and !empty($subForums) and count($subForums)) {

            foreach ($subForums as $key => $subForum) {
                $check = Forum::where('id', $key)->first();

                if (is_numeric($key)) {
                    $oldIds[] = $key;
                }

                if (!empty($subForum['title'])) {
                    if (!empty($check)) {
                        $check->update([
                            'order' => $order,
                            'icon' => $subForum['icon'],
                            'group_id' => $subForum['group_id'] ?? null,
                            'role_id' => $subForum['role_id'] ?? null,
                            'status' => $subForum['status'],
                            'close' => $forum->close || ((!empty($subForum['close']) and $subForum['close'] == 1)),
                        ]);

                        ForumTranslation::updateOrCreate([
                            'forum_id' => $check->id,
                            'locale' => mb_strtolower($locale),
                        ], [
                            'title' => $subForum['title'],
                            'description' => $subForum['description'],
                        ]);
                    } else {
                        $new = Forum::create([
                            'slug' => Forum::makeSlug($subForum['title']),
                            'parent_id' => $forum->id,
                            'order' => $order,
                            'icon' => $subForum['icon'],
                            'group_id' => $subForum['group_id'] ?? null,
                            'role_id' => $subForum['role_id'] ?? null,
                            'status' => $subForum['status'],
                            'close' => $forum->close || ((!empty($subForum['close']) and $subForum['close'] == 1)),
                        ]);

                        ForumTranslation::updateOrCreate([
                            'forum_id' => $new->id,
                            'locale' => mb_strtolower($locale),
                        ], [
                            'title' => $subForum['title'],
                            'description' => $subForum['description'],
                        ]);

                        $oldIds[] = $new->id;
                    }

                    $order += 1;
                }
            }
        }

        Forum::where('parent_id', $forum->id)
            ->whereNotIn('id', $oldIds)
            ->delete();

        return true;
    }
}
