<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $this->authorize('admin_tags_list');

        $tags = Tag::orderBy('id','desc')
            ->paginate(10);;
        $data = [
            'pageTitle' => trans('admin/pages/tags.tags_list_page_title'),
            'tags' => $tags
        ];

        return view('admin.tags.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_tags_create');

        $data = [
            'pageTitle' => trans('admin/main.tag_new_page_title'),
        ];

        return view('admin.tags.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_tags_create');

        $this->validate($request, [
            'title' => 'required|min:3|max:128',
        ]);

        $tag = Tag::create([
            'title' => $request->input('title'),
        ]);


        return redirect(getAdminPanelUrl().'/tags');
    }

    public function edit($id)
    {
        $this->authorize('admin_tags_edit');

        $tag = Tag::findOrFail($id);
        $data = [
            'pageTitle' => trans('admin/pages/tags.edit_page_title'),
            'tag' => $tag,
        ];

        return view('admin.tags.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_tags_edit');

        $this->validate($request, [
            'title' => 'required|min:3|max:128',
        ]);
        $tag = Tag::findOrFail($id);
        $tag->update([
            'title' => $request->input('title'),
        ]);

        return redirect(getAdminPanelUrl().'/tags');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_tags_delete');

        Tag::find($id)->delete();

        return redirect(getAdminPanelUrl().'/tags');
    }
}
