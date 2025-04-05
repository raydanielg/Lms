<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\ForumFeaturedTopic;
use App\Models\ForumRecommendedTopic;
use App\Models\ForumTopic;
use App\Models\ForumTopicAttachment;
use App\Models\ForumTopicBookmark;
use App\Models\ForumTopicLike;
use App\Models\ForumTopicPost;
use App\Models\ForumTopicReport;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function __construct()
    {
        $forumsStatus = getFeaturesSettings('forums_status');

        if (empty($forumsStatus) or $forumsStatus == '0') {
            abort(403);
        }
    }

    public function index()
    {
        $forums = Forum::orderBy('order', 'asc')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->with([
                'subForums' => function ($query) {
                    $query->where('status', 'active');
                    $query->withCount([
                        'topics',
                    ]);
                },
            ])
            ->withCount([
                'topics',
            ])
            ->get();

        foreach ($forums as $forum) {
            if (!empty($forum->subForums) and count($forum->subForums)) {
                foreach ($forum->subForums as $item) {
                    $item = $this->handleForumExtraData($item);
                }
            } else {
                $forum = $this->handleForumExtraData($forum);
            }
        }


        $seoSettings = getSeoMetas('forum');
        $pageTitle = $seoSettings['title'] ?? '';
        $pageDescription = $seoSettings['description'] ?? '';
        $pageRobot = getPageRobot('forum');

        $forumsCount = Forum::where('status', 'active')
            ->whereDoesntHave('subForums')
            ->count();

        $topicsCount = ForumTopic::query()->count();
        $postsCount = ForumTopicPost::query()->count();
        $membersCount = ForumTopicPost::select(DB::raw('count(distinct user_id) as count'))->first()->count;

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
            'forums' => $forums,
            'forumsCount' => $forumsCount,
            'topicsCount' => $topicsCount,
            'postsCount' => $postsCount,
            'membersCount' => $membersCount,
            'featuredTopics' => $this->getFeaturedTopics(),
            'recommendedTopics' => $this->getRecommendedTopics(),
        ];

        return view('web.default.forum.index', $data);
    }

    private function getFeaturedTopics()
    {
        $featuredTopics = ForumFeaturedTopic::orderBy('created_at', 'desc')
            ->with([
                'topic' => function ($query) {
                    $query->with([
                        'creator' => function ($query) {
                            $query->select('id', 'full_name', 'avatar', 'avatar_settings');
                        },
                        'posts'
                    ]);
                    $query->withCount([
                        'posts'
                    ]);
                }
            ])->get();

        foreach ($featuredTopics as $featuredTopic) {
            $usersAvatars = [];

            if ($featuredTopic->topic->posts_count > 0) {
                foreach ($featuredTopic->topic->posts as $post) {
                    if (!empty($post->user) and count($usersAvatars) < 2 and empty($usersAvatars[$post->user->id])) {
                        $usersAvatars[$post->user->id] = $post->user;
                    }
                }
            }

            $featuredTopic->usersAvatars = $usersAvatars;
        }

        return $featuredTopics;
    }

    private function getRecommendedTopics()
    {
        return ForumRecommendedTopic::orderBy('created_at', 'desc')
            ->with([
                'topics'
            ])
            ->get();
    }

    private function handleForumExtraData(&$forum)
    {
        $topicsIds = ForumTopic::where('forum_id', $forum->id)->pluck('id')->toArray();

        $forum->posts_count = ForumTopicPost::whereIn('topic_id', $topicsIds)->count();

        $forum->lastTopic = ForumTopic::where('forum_id', $forum->id)->orderBy('created_at', 'desc')->first();

        return $forum;
    }

    public function search(Request $request)
    {
        $search = $request->get('search');

        $query = ForumTopic::query();

        $resultCount = 0;
        $topics = $this->handleTopics($request, $query, $resultCount);

        $data = [
            'pageTitle' => trans('update.search_results_for', ['temp' => $search]),
            'pageDescription' => '',
            'pageRobot' => '',
            'topics' => $topics,
            'topUsers' => $this->getTopUsers(),
            'popularTopics' => $this->getPopularTopics(),
            'resultCount' => $resultCount
        ];

        return view('web.default.forum.topics_search', $data);
    }

    public function topics(Request $request, $slug)
    {
        $forum = Forum::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!empty($forum)) {
            $query = ForumTopic::where('forum_topics.forum_id', $forum->id);

            $resultCount = 0;
            $topics = $this->handleTopics($request, $query, $resultCount);

            $data = [
                'pageTitle' => $forum->title,
                'pageDescription' => $forum->description,
                'pageRobot' => '',
                'forum' => $forum,
                'topics' => $topics,
                'topUsers' => $this->getTopUsers(),
                'popularTopics' => $this->getPopularTopics(),
                'resultCount' => $resultCount
            ];

            return view('web.default.forum.topics', $data);
        }

        abort(404);
    }

    private function handleTopics(Request $request, $query, &$resultCount)
    {
        $search = $request->get('search');
        $sort = $request->get('sort');

        if (!empty($search)) {
            $topicsIds = ForumTopicPost::where('description', 'like', "%$search%")
                ->pluck('topic_id')
                ->toArray();

            $query->where(function ($query) use ($topicsIds, $search) {
                $query->whereIn('forum_topics.id', $topicsIds)
                    ->orWhere('forum_topics.title', 'like', "%$search%")
                    ->orWhere('forum_topics.description', 'like', "%$search%");
            });
        }

        $query->orderBy('forum_topics.pin', 'desc');

        if (!empty($sort) and $sort != 'newest') {
            if ($sort == 'popular_topics') {
                $query->join('forum_topic_posts', 'forum_topic_posts.topic_id', 'forum_topics.id')
                    ->select('forum_topics.*', DB::raw("count(forum_topic_posts.topic_id) as topic_posts_count"))
                    ->orderBy('topic_posts_count', 'desc');
            } elseif ($sort == 'not_answered') {
                $query->whereDoesntHave('posts');
                $query->orderBy('forum_topics.created_at', 'desc');
            }
        } else {
            $query->orderBy('forum_topics.created_at', 'desc');
        }

        $resultCount = deepClone($query)->count();

        $topics = $query->with([
            'creator' => function ($query) {
                $query->select('id', 'full_name', 'avatar', 'avatar_settings');
            }
        ])
            ->withCount([
                'posts'
            ])
            ->paginate(15);

        foreach ($topics as $topic) {
            $topic->lastPost = $topic->posts()->orderBy('created_at', 'desc')->first();
        }

        return $topics;
    }

    private function getTopUsers()
    {
        return User::leftJoin('forum_topics', 'forum_topics.creator_id', 'users.id')
            ->leftJoin('forum_topic_posts', 'forum_topic_posts.user_id', 'users.id')
            ->select('users.id', 'users.full_name', 'users.avatar', DB::raw("count(forum_topics.creator_id) as topics, count(forum_topic_posts.user_id) as posts"), DB::raw("(count(forum_topics.creator_id) + count(forum_topic_posts.user_id)) as all_posts"))
            ->whereHas('forumTopics')
            ->groupBy('forum_topics.creator_id')
            ->groupBy('forum_topic_posts.user_id')
            ->orderBy('all_posts', 'desc')
            ->limit(4)
            ->get();
    }

    private function getPopularTopics()
    {
        return ForumTopic::query()
            ->join('forum_topic_posts', 'forum_topic_posts.topic_id', 'forum_topics.id')
            ->select('forum_topics.*', DB::raw("count(forum_topic_posts.topic_id) as posts_count"))
            ->whereHas('creator')
            ->with([
                'creator' => function ($query) {
                    $query->select('id', 'full_name', 'avatar', 'avatar_settings');
                }
            ])
            ->orderBy('posts_count', 'desc')
            ->groupBy('forum_topics.id')
            ->limit(4)
            ->get();
    }

    public function createTopic(Request $request)
    {
        $user = auth()->user();

        if (empty($user)) {
            return redirect('/login');
        }

        $forums = Forum::orderBy('order', 'asc')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->with([
                'subForums' => function ($query) {
                    $query->where('status', 'active');
                }
            ])->get();

        $data = [
            'pageTitle' => trans('update.create_new_topic'),
            'pageDescription' => '',
            'pageRobot' => '',
            'forums' => $forums
        ];

        return view('web.default.forum.create_topic', $data);
    }

    public function storeTopic(Request $request)
    {
        $user = auth()->user();

        if (empty($user)) {
            abort(403);
        }

        $this->validate($request, [
            'title' => 'required|max:255',
            'forum_id' => 'required|exists:forums,id',
            'description' => 'required',
        ]);

        $data = $request->all();

        $forum = Forum::where('id', $data['forum_id'])
            ->where('status', 'active')
            ->where('close', false)
            ->first();

        if (!empty($forum) and $forum->checkUserCanCreateTopic($user)) {

            $topic = ForumTopic::create([
                'slug' => ForumTopic::makeSlug($data['title']),
                'creator_id' => $user->id,
                'forum_id' => $data['forum_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'close' => false,
                'created_at' => time(),
            ]);

            $this->handleTopicAttachments($topic, $data);

            $buyStoreReward = RewardAccounting::calculateScore(Reward::MAKE_TOPIC);
            RewardAccounting::makeRewardAccounting($topic->creator_id, $buyStoreReward, Reward::MAKE_TOPIC, $topic->id);


            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[topic_title]' => $topic->title,
                '[forum_title]' => $forum->title,
            ];
            sendNotification("new_forum_topic", $notifyOptions, 1);

            $toastData = [
                'title' => trans('public.request_success'),
                'msg' => trans('update.new_topic_successfully_created'),
                'status' => 'success'
            ];

            $url = '/forums/' . $topic->forum->slug . '/topics';
            return redirect($url)->with(['toast' => $toastData]);

        }

        abort(403);
    }

    private function handleTopicAttachments($topic, $data)
    {
        $user = auth()->user();

        ForumTopicAttachment::where('creator_id', $user->id)
            ->where('topic_id', $topic->id)
            ->delete();

        if (!empty($data['attachments']) and count($data['attachments'])) {

            foreach ($data['attachments'] as $attach) {
                if (!empty($attach)) {
                    ForumTopicAttachment::create([
                        'creator_id' => $user->id,
                        'topic_id' => $topic->id,
                        'path' => $attach,
                    ]);
                }
            }
        }
    }

    public function topicLikeToggle($forumSlug, $topicSlug)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->first();

            if (!empty($topic)) {
                $like = ForumTopicLike::where('user_id', $user->id)
                    ->where('topic_id', $topic->id)
                    ->first();

                $likeStatus = true;
                if (!empty($like)) {
                    $like->delete();
                    $likeStatus = false;
                } else {
                    ForumTopicLike::create([
                        'user_id' => $user->id,
                        'topic_id' => $topic->id,
                    ]);
                }

                return response()->json([
                    'code' => 200,
                    'likes' => $topic->likes->count(),
                    'status' => $likeStatus
                ]);
            }
        }

        abort(403);
    }

    public function topicBookmarkToggle($forumSlug, $topicSlug)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->first();

            if (!empty($topic)) {
                $add = true;
                $bookmark = ForumTopicBookmark::where('user_id', $user->id)
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!empty($bookmark)) {
                    $add = false;

                    $bookmark->delete();
                } else {
                    ForumTopicBookmark::create([
                        'user_id' => $user->id,
                        'topic_id' => $topic->id,
                        'created_at' => time(),
                    ]);
                }

                return response()->json([
                    'code' => 200,
                    'add' => $add
                ]);
            }
        }

        abort(403);
    }

    public function topicEdit($forumSlug, $topicSlug)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->where('creator_id', $user->id)
                ->first();

            if (!empty($topic)) {
                $forums = Forum::orderBy('order', 'asc')
                    ->whereNull('parent_id')
                    ->where('status', 'active')
                    ->with([
                        'subForums' => function ($query) {
                            $query->where('status', 'active');
                        }
                    ])->get();

                $data = [
                    'pageTitle' => 'edit topic',
                    'pageDescription' => '',
                    'pageRobot' => '',
                    'forums' => $forums,
                    'topic' => $topic,
                ];

                return view('web.default.forum.create_topic', $data);
            }
        }

        abort(403);
    }

    public function topicUpdate(Request $request, $forumSlug, $topicSlug)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->where('creator_id', $user->id)
                ->first();

            if (!empty($topic)) {
                $this->validate($request, [
                    'title' => 'required|max:255',
                    'forum_id' => 'required|exists:forums,id',
                    'description' => 'required',
                ]);

                $data = $request->all();

                $topic->update([
                    'forum_id' => $data['forum_id'],
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'close' => false,
                ]);

                $this->handleTopicAttachments($topic, $data);

                $toastData = [
                    'title' => trans('public.request_success'),
                    'msg' => trans('update.new_topic_successfully_created'),
                    'status' => 'success'
                ];

                $url = '/forums/' . $topic->forum->slug . '/topics';
                return redirect($url)->with(['toast' => $toastData]);
            }
        }

        abort(403);
    }

    public function topicDownloadAttachment($forumSlug, $topicSlug, $attachmentId)
    {
        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($forum)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->first();

            if (!empty($topic)) {
                $attachment = ForumTopicAttachment::where('id', $attachmentId)
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!empty($attachment)) {
                    $filePath = public_path($attachment->path);

                    if (file_exists($filePath)) {
                        $fileInfo = pathinfo($filePath);
                        $type = (!empty($fileInfo) and !empty($fileInfo['extension'])) ? $fileInfo['extension'] : '';

                        $fileName = str_replace(' ', '-', "attachment-{$attachment->id}");
                        $fileName = str_replace('.', '-', $fileName);
                        $fileName .= '.' . $type;

                        $headers = array(
                            'Content-Type: application/' . $type,
                        );

                        return response()->download($filePath, $fileName, $headers);
                    }
                }
            }
        }

        abort(403);
    }

    public function posts($forumSlug, $topicSlug)
    {
        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($forum) and $forum->checkUserCanCreateTopic()) {

            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->with([
                    'forum',
                    'attachments',
                    'posts' => function ($query) {
                        $query->orderBy('pin', 'desc');
                        $query->orderBy('created_at', 'asc');
                        $query->with([
                            'parent'
                        ]);
                    }
                ])
                ->first();

            if (!empty($topic)) {
                $user = auth()->user();

                $likedPostsIds = [];
                if (!empty($user)) {
                    $likedPostsIds = ForumTopicLike::where('user_id', $user->id)->pluck('topic_post_id')->toArray();

                    $topicLiked = ForumTopicLike::where('user_id', $user->id)
                        ->where('topic_id', $topic->id)
                        ->first();

                    $bookmarked = ForumTopicBookmark::where('user_id', $user->id)
                        ->where('topic_id', $topic->id)
                        ->first();

                    $topic->liked = !empty($topicLiked);
                    $topic->bookmarked = !empty($bookmarked);
                }

                $data = [
                    'pageTitle' => $topic->title,
                    'pageDescription' => $topic->description,
                    'pageRobot' => '',
                    'forum' => $forum,
                    'topic' => $topic,
                    'likedPostsIds' => $likedPostsIds,
                ];

                return view('web.default.forum.posts', $data);
            }
        }

        abort(404);
    }

    public function storePost(Request $request, $forumSlug, $topicSlug)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->first();

            if (!empty($topic)) {
                $forum = $topic->forum;

                if (!$topic->close and !$forum->isClosed()) {
                    $data = $request->all();

                    $validator = Validator::make($data, [
                        'description' => 'required|min:3'
                    ]);

                    if ($validator->fails()) {
                        return response([
                            'code' => 422,
                            'errors' => $validator->errors(),
                        ], 422);
                    }

                    $replyPostId = (!empty($data['reply_post_id']) and $data['reply_post_id'] != '') ? $data['reply_post_id'] : null;

                    $post = ForumTopicPost::create([
                        'user_id' => $user->id,
                        'topic_id' => $topic->id,
                        'parent_id' => $replyPostId,
                        'description' => $data['description'],
                        'attach' => $data['attach'],
                        'created_at' => time(),
                    ]);

                    $buyStoreReward = RewardAccounting::calculateScore(Reward::SEND_TOPIC_POST);
                    RewardAccounting::makeRewardAccounting($post->user_id, $buyStoreReward, Reward::SEND_TOPIC_POST, $post->id);

                    $notifyOptions = [
                        '[topic_title]' => $topic->title,
                        '[u.name]' => $user->full_name
                    ];
                    sendNotification('send_post_in_topic', $notifyOptions, $topic->creator_id);

                    return response()->json([
                        'code' => 200
                    ]);
                }
            }
        }

        abort(403);
    }


    public function storeTopicReport(Request $request, $forumSlug, $topicSlug)
    {
        $user = auth()->user();


        if (!empty($user)) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'item_id' => 'required',
                'item_type' => 'required',
                'message' => 'required|min:3',
            ]);

            if ($validator->fails()) {
                return response([
                    'code' => 422,
                    'errors' => $validator->errors(),
                ], 422);
            }

            ForumTopicReport::create([
                'user_id' => $user->id,
                'topic_id' => ($data['item_type'] == 'topic') ? $data['item_id'] : null,
                'topic_post_id' => ($data['item_type'] == 'topic_post') ? $data['item_id'] : null,
                'message' => $data['message'],
                'created_at' => time(),
            ]);

            $notifyOptions = [
                '[u.name]' => $user->full_name,
                '[content_type]' => trans('public.' . $data['item_type'])
            ];
            sendNotification("new_report_item_for_admin", $notifyOptions, 1);


            return response()->json([
                'code' => 200
            ]);
        }

        abort(403);
    }

    public function postLikeToggle($forumSlug, $topicSlug, $postId)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->first();

            if (!empty($topic)) {
                $post = ForumTopicPost::where('id', $postId)
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!empty($post)) {
                    $like = ForumTopicLike::where('user_id', $user->id)
                        ->where('topic_post_id', $postId)
                        ->first();

                    $likeStatus = true;
                    if (!empty($like)) {
                        $like->delete();
                        $likeStatus = false;
                    } else {
                        ForumTopicLike::create([
                            'user_id' => $user->id,
                            'topic_post_id' => $postId,
                        ]);
                    }

                    return response()->json([
                        'code' => 200,
                        'likes' => $post->likes->count(),
                        'status' => $likeStatus
                    ]);
                }
            }
        }


        abort(403);
    }


    public function postUnPin($forumSlug, $topicSlug, $postId)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->where('creator_id', $user->id)
                ->first();

            if (!empty($topic)) {
                $post = ForumTopicPost::where('id', $postId)
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!empty($post)) {
                    $post->update([
                        'pin' => false
                    ]);

                    return response()->json([
                        'code' => 200,
                    ]);
                }
            }
        }

        abort(403);
    }

    public function postPin($forumSlug, $topicSlug, $postId)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->where('creator_id', $user->id)
                ->first();

            if (!empty($topic)) {
                $post = ForumTopicPost::where('id', $postId)
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!empty($post)) {
                    $post->update([
                        'pin' => true
                    ]);

                    return response()->json([
                        'code' => 200,
                    ]);
                }
            }
        }

        abort(403);
    }

    public function postEdit($forumSlug, $topicSlug, $postId)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->first();

            if (!empty($topic)) {
                $post = ForumTopicPost::where('id', $postId)
                    ->where('user_id', $user->id)
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!empty($post)) {

                    return response()->json([
                        'code' => 200,
                        'post' => $post
                    ]);
                }
            }
        }

        abort(403);
    }

    public function postUpdate(Request $request, $forumSlug, $topicSlug, $postId)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->first();

            if (!empty($topic)) {

                $post = ForumTopicPost::where('id', $postId)
                    ->where('user_id', $user->id)
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!empty($post)) {
                    $data = $request->all();

                    $validator = Validator::make($data, [
                        'description' => 'required|min:3'
                    ]);

                    if ($validator->fails()) {
                        return response([
                            'code' => 422,
                            'errors' => $validator->errors(),
                        ], 422);
                    }

                    $post->update([
                        'description' => $data['description'],
                        'attach' => $data['attach'],
                    ]);

                    return response()->json([
                        'code' => 200
                    ]);
                }
            }
        }

        abort(403);
    }

    public function postDownloadAttachment($forumSlug, $topicSlug, $postId)
    {
        $user = auth()->user();

        $forum = Forum::where('slug', $forumSlug)
            ->where('status', 'active')
            ->first();

        if (!empty($user) and !empty($forum) and $forum->checkUserCanCreateTopic($user)) {
            $topic = ForumTopic::where('slug', $topicSlug)
                ->where('forum_id', $forum->id)
                ->first();

            if (!empty($topic)) {
                $post = ForumTopicPost::where('id', $postId)
                    ->where('topic_id', $topic->id)
                    ->first();

                if (!empty($post)) {
                    $filePath = public_path($post->attach);

                    if (file_exists($filePath)) {
                        $fileInfo = pathinfo($filePath);
                        $type = (!empty($fileInfo) and !empty($fileInfo['extension'])) ? $fileInfo['extension'] : '';

                        $fileName = str_replace(' ', '-', "attachment-{$post->id}");
                        $fileName = str_replace('.', '-', $fileName);
                        $fileName .= '.' . $type;

                        $headers = array(
                            'Content-Type: application/' . $type,
                        );

                        return response()->download($filePath, $fileName, $headers);
                    }
                }
            }
        }

        abort(403);
    }
}
