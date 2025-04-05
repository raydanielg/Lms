<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumTopicReport;
use Illuminate\Http\Request;

class ForumTopicReportsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_forum_topic_post_reports');

        $reports = ForumTopicReport::with([
            'user' => function ($query) {
                $query->select('id', 'full_name');
            },
            'topic',
            'topicPost'
        ])->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.topic_and_post_reports'),
            'reports' => $reports
        ];

        return view('admin.forums.topics.reports', $data);
    }

    public function delete($id)
    {
        $this->authorize('admin_forum_topic_post_reports');

        $report = ForumTopicReport::findOrFail($id);

        $report->delete();

        return back();
    }
}
