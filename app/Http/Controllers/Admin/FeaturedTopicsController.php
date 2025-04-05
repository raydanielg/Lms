<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumFeaturedTopic;
use Illuminate\Http\Request;

class FeaturedTopicsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_featured_topics_list');

        $featuredTopics = ForumFeaturedTopic::orderBy('created_at', 'desc')
            ->with([
                'topic'
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.featured_topics'),
            'featuredTopics' => $featuredTopics
        ];

        return view('admin.forums.featured_topics.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_featured_topics_create');

        $data = [
            'pageTitle' => trans('update.new_featured_topic'),
        ];

        return view('admin.forums.featured_topics.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_featured_topics_create');

        $this->validate($request, [
            'topic_id' => 'required|exists:forum_topics,id',
            'icon' => 'required'
        ]);

        $data = $request->all();

        ForumFeaturedTopic::create([
            'topic_id' => $data['topic_id'],
            'icon' => $data['icon'],
            'created_at' => time()
        ]);

        return redirect(getAdminPanelUrl().'/featured-topics');
    }

    public function edit($id)
    {
        $this->authorize('admin_featured_topics_edit');

        $feature = ForumFeaturedTopic::where('id', $id)
            ->with([
                'topic'
            ])
            ->first();

        if (!empty($feature)) {
            $data = [
                'pageTitle' => trans('update.edit_featured_topic'),
                'feature' => $feature
            ];

            return view('admin.forums.featured_topics.create', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_featured_topics_edit');

        $this->validate($request, [
            'topic_id' => 'required|exists:forum_topics,id',
            'icon' => 'required'
        ]);

        $feature = ForumFeaturedTopic::findOrFail($id);

        $data = $request->all();

        $feature->update([
            'topic_id' => $data['topic_id'],
            'icon' => $data['icon'],
        ]);

        return redirect(getAdminPanelUrl().'/featured-topics');
    }

    public function destroy($id)
    {
        $this->authorize('admin_featured_topics_delete');

        $feature = ForumFeaturedTopic::findOrFail($id);

        $feature->delete();

        return back();
    }
}
