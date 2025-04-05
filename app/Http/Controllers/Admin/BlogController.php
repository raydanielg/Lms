<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\traits\ProductBadgeTrait;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Translation\BlogTranslation;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ProductBadgeTrait;

    public function index(Request $request)
    {
        removeContentLocale();

        $this->authorize('admin_blog_lists');

        $query = Blog::query();

        $blog = $this->filters($query, $request)
            ->with(['category', 'author' => function ($query) {
                $query->select('id', 'full_name');
            }])
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $blogCategories = BlogCategory::all();
        $adminRoleIds = Role::where('is_admin', true)->pluck('id')->toArray();
        $authors = User::select('id', 'full_name', 'role_id')->whereIn('role_id', $adminRoleIds)->get();

        $data = [
            'pageTitle' => trans('admin/pages/blog.blog'),
            'blog' => $blog,
            'blogCategories' => $blogCategories,
            'authors' => $authors,
        ];

        return view('admin.blog.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $category_id = $request->get('category_id', null);
        $author_id = $request->get('author_id', null);
        $status = $request->get('status', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');


        if (!empty($title)) {
            $query->whereTranslationLike('title', '%' . $title . '%');
        }

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        if (!empty($author_id)) {
            $query->where('author_id', $author_id);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function create()
    {
        $this->authorize('admin_blog_create');

        $categories = BlogCategory::all();

        $data = [
            'pageTitle' => trans('admin/pages/blog.create_blog'),
            'categories' => $categories
        ];

        return view('admin.blog.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_blog_create');

        $this->validate($request, [
            'locale' => 'required',
            'title' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'image' => 'required|string',
            'description' => 'required|string',
            'content' => 'required|string',
        ]);

        $data = $request->all();

        $blog = Blog::create([
            'slug' => Blog::makeSlug($data['title']),
            'category_id' => $data['category_id'],
            'author_id' => !empty($data['author_id']) ? $data['author_id'] : auth()->id(),
            'image' => $data['image'],
            'enable_comment' => (!empty($data['enable_comment']) and $data['enable_comment'] == 'on'),
            'status' => (!empty($data['status']) and $data['status'] == 'on') ? 'publish' : 'pending',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        if ($blog) {
            BlogTranslation::updateOrCreate([
                'blog_id' => $blog->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'meta_description' => $data['meta_description'],
                'content' => $data['content'],
            ]);

            if ($blog->status == 'publish' and $blog->author_id != auth()->id()) {
                $notifyOptions = [
                    '[blog_title]' => $blog->title,
                ];
                sendNotification('publish_instructor_blog_post', $notifyOptions, $blog->author_id);
            }
        }

        return redirect(getAdminPanelUrl().'/blog');
    }

    public function edit(Request $request, $post_id)
    {
        $this->authorize('admin_blog_edit');

        $post = Blog::findOrFail($post_id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $post->getTable(), $post->id);

        $categories = BlogCategory::all();

        $data = [
            'pageTitle' => trans('admin/pages/blog.create_blog'),
            'categories' => $categories,
            'post' => $post,
        ];

        return view('admin.blog.create', $data);
    }

    public function update(Request $request, $post_id)
    {
        $this->authorize('admin_blog_edit');

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'image' => 'required|string',
            'description' => 'required|string',
            'content' => 'required|string',
        ]);

        $data = $request->all();
        $post = Blog::findOrFail($post_id);

        $post->update([
            'category_id' => $data['category_id'],
            'author_id' => !empty($data['author_id']) ? $data['author_id'] : $post->author_id,
            'image' => $data['image'],
            'enable_comment' => (!empty($data['enable_comment']) and $data['enable_comment'] == 'on'),
            'status' => (!empty($data['status']) and $data['status'] == 'on') ? 'publish' : 'pending',
            'updated_at' => time(),
        ]);


        BlogTranslation::updateOrCreate([
            'blog_id' => $post->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
            'meta_description' => $data['meta_description'],
            'content' => $data['content'],
        ]);

        // Product Badge
        $this->handleProductBadges($post, $data);

        removeContentLocale();

        if ($post->status == 'publish' and $post->author_id != auth()->id()) {

            $createPostReward = RewardAccounting::calculateScore(Reward::CREATE_BLOG_BY_INSTRUCTOR);
            RewardAccounting::makeRewardAccounting($post->author_id, $createPostReward, Reward::CREATE_BLOG_BY_INSTRUCTOR, $post->id, true);


            $notifyOptions = [
                '[blog_title]' => $post->title,
            ];
            sendNotification('publish_instructor_blog_post', $notifyOptions, $post->author_id);
        }

        return redirect(getAdminPanelUrl().'/blog');
    }

    public function delete($post_id)
    {
        $this->authorize('admin_blog_delete');

        $post = Blog::findOrFail($post_id);

        $post->delete();

        return redirect(getAdminPanelUrl().'/blog');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');
        $blog = Blog::select('id')
            ->whereTranslationLike('title', "%$term%")
            ->get();

        return response()->json($blog, 200);
    }
}
