<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplatesController extends Controller
{
    public function index()
    {
        $this->authorize('admin_notifications_list');

        $templates = NotificationTemplate::orderBy('id','desc')->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/users.templates'),
            'templates' => $templates
        ];

        return view('admin.notifications.templates', $data);
    }

    public function create()
    {
        $this->authorize('admin_notifications_template_create');

        $data = [
            'pageTitle' => trans('admin/pages/users.new_template'),
        ];

        return view('admin.notifications.new_template', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_notifications_template_create');

        $this->validate($request, [
            'title' => 'required',
            'template' => 'required',
        ]);

        $data = $request->all();

        NotificationTemplate::create([
            'title' => $data['title'],
            'template' => $data['template'],
        ]);

        return redirect(getAdminPanelUrl().'/notifications/templates');
    }

    public function edit($id)
    {
        $this->authorize('admin_notifications_template_edit');

        $template = NotificationTemplate::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/users.edit_template'),
            'template' => $template
        ];

        return view('admin.notifications.new_template', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_notifications_template_edit');

        $this->validate($request, [
            'title' => 'required',
            'template' => 'required',
        ]);

        $data = $request->all();
        $template = NotificationTemplate::findOrFail($id);

        $template->update([
            'title' => $data['title'],
            'template' => $data['template'],
        ]);

        return redirect(getAdminPanelUrl().'/notifications/templates');
    }

    public function delete($id)
    {
        $this->authorize('admin_notifications_template_delete');

        $template = NotificationTemplate::findOrFail($id);

        $template->delete();

        return redirect(getAdminPanelUrl().'/notifications/templates');
    }
}
