<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumTopicBookmark;
use App\Models\ForumTopicPost;
use Illuminate\Http\Request;

class ForumsController extends Controller
{
    public function topics(Request $request)
    {
        $this->authorize("panel_forums_my_topics");

        if (getFeaturesSettings('forums_status')) {
            $user = auth()->user();

            $forums = Forum::orderBy('order', 'asc')
                ->whereNull('parent_id')
                ->where('status', 'active')
                ->with([
                    'subForums' => function ($query) {
                        $query->where('status', 'active');
                    }
                ])->get();

            $query = ForumTopic::where('creator_id', $user->id);

            $publishedTopics = deepClone($query)->count();
            $lockedTopics = deepClone($query)->where('close', true)->count();

            $topicsIds = deepClone($query)->pluck('id')->toArray();
            $topicMessages = ForumTopicPost::whereIn('topic_id', $topicsIds)->count();

            $query = $this->handleFilters($request, $query);

            $topics = $query->orderBy('created_at', 'desc')
                ->with([
                    'forum'
                ])
                ->withCount([
                    'posts'
                ])
                ->paginate(10);

            $data = [
                'pageTitle' => trans('update.topics'),
                'forums' => $forums,
                'topics' => $topics,
                'publishedTopics' => $publishedTopics,
                'lockedTopics' => $lockedTopics,
                'topicMessages' => $topicMessages,
            ];

            return view('web.default.panel.forum.topics', $data);
        }

        abort(403);
    }

    private function handleFilters(Request $request, $query, $type = null)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $forumId = $request->get('forum_id');
        $status = $request->get('status');

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($forumId) and $forumId != 'all') {
            if ($type == 'posts') {
                $query->whereHas('topic', function ($query) use ($forumId) {
                    $query->where('forum_id', $forumId);
                });
            } else {
                $query->where('forum_id', $forumId);
            }
        }

        if ($status and $status !== 'all') {
            if ($type == 'posts') {
                $query->whereHas('topic', function ($query) use ($status) {
                    if ($status == 'closed') {
                        $query->where('close', true);
                    } else {
                        $query->where('close', false);
                    }
                });
            } else {
                if ($status == 'closed') {
                    $query->where('close', true);
                } else {
                    $query->where('close', false);
                }
            }
        }


        return $query;
    }

    public function posts(Request $request)
    {
        $this->authorize("panel_forums_my_posts");

        if (getFeaturesSettings('forums_status')) {
            $user = auth()->user();

            $forums = Forum::orderBy('order', 'asc')
                ->whereNull('parent_id')
                ->where('status', 'active')
                ->with([
                    'subForums' => function ($query) {
                        $query->where('status', 'active');
                    }
                ])->get();


            $query = ForumTopicPost::where('user_id', $user->id);

            $query = $this->handleFilters($request, $query, 'posts');

            $posts = $query->orderBy('created_at', 'desc')
                ->with([
                    'topic' => function ($query) {
                        $query->with([
                            'creator' => function ($query) {
                                $query->select('id', 'full_name', 'avatar', 'avatar_settings');
                            },
                            'forum' => function ($query) {
                                $query->select('id', 'slug');
                            }
                        ]);
                    },
                    'user' => function ($query) {
                        $query->select('id', 'full_name', 'avatar', 'avatar_settings');
                    },
                ])
                ->paginate(10);

            foreach ($posts as $post) {
                $post->replies_count = ForumTopicPost::where('parent_id', $post->id)->count();
            }

            $data = [
                'pageTitle' => trans('site.posts'),
                'forums' => $forums,
                'posts' => $posts,
            ];

            return view('web.default.panel.forum.posts', $data);
        }

        abort(403);
    }

    public function bookmarks()
    {
        $this->authorize("panel_forums_bookmarks");

        if (getFeaturesSettings('forums_status')) {
            $user = auth()->user();

            $topicsIds = ForumTopicBookmark::where('user_id', $user->id)->pluck('topic_id')->toArray();

            $topics = ForumTopic::whereIn('id', $topicsIds)
                ->orderBy('created_at', 'desc')
                ->with([
                    'forum'
                ])
                ->withCount([
                    'posts'
                ])
                ->paginate(10);

            $data = [
                'pageTitle' => trans('update.topics'),
                'topics' => $topics,
            ];

            return view('web.default.panel.forum.bookmarks', $data);
        }

        abort(403);
    }

    public function removeBookmarks($topicId)
    {
        if (getFeaturesSettings('forums_status')) {
            $user = auth()->user();

            $bookmark = ForumTopicBookmark::where('user_id', $user->id)
                ->where('topic_id', $topicId)
                ->first();

            if (!empty($bookmark)) {
                $bookmark->delete();
            }

            return response([
                'code' => 200
            ]);
        }

        abort(403);
    }
}
