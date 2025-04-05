<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Translation\BlogCategoryTranslation;
use Illuminate\Http\Request;

class BlogCategoriesController extends Controller
{
    public function index()
    {
        $this->authorize('admin_blog_categories');
        removeContentLocale();

        $blogCategories = BlogCategory::withCount('blog')->get();

        $data = [
            'pageTitle' => trans('admin/pages/blog.blog_categories'),
            'blogCategories' => $blogCategories
        ];

        return view('admin.blog.categories', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_blog_categories_create');

        $this->validate($request, [
            'title' => 'required|string',
        ]);

        $data = $request->all();

        $category = BlogCategory::create([
            'slug' => BlogCategory::makeSlug($data['title']),
        ]);

        BlogCategoryTranslation::query()->updateOrCreate([
            'blog_category_id' => $category->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);

        return redirect(getAdminPanelUrl() . '/blog/categories');
    }

    public function edit(Request $request, $category_id)
    {
        $this->authorize('admin_blog_categories_edit');

        $editCategory = BlogCategory::findOrFail($category_id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $editCategory->getTable(), $editCategory->id);

        $data = [
            'pageTitle' => trans('admin/pages/blog.blog_categories'),
            'editCategory' => $editCategory
        ];

        return view('admin.blog.categories', $data);
    }

    public function update(Request $request, $category_id)
    {
        $this->authorize('admin_blog_categories_edit');

        $this->validate($request, [
            'title' => 'required',
        ]);

        $category = BlogCategory::findOrFail($category_id);

        $data = $request->all();

        BlogCategoryTranslation::query()->updateOrCreate([
            'blog_category_id' => $category->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);

        return redirect(getAdminPanelUrl() . '/blog/categories');
    }

    public function delete($category_id)
    {
        $this->authorize('admin_blog_categories_delete');

        $editCategory = BlogCategory::findOrFail($category_id);

        $editCategory->delete();

        return redirect(getAdminPanelUrl() . '/blog/categories');
    }
}
