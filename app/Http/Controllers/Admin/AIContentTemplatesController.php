<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiContentTemplate;
use App\Models\Translation\AiContentTemplateTranslation;
use Illuminate\Http\Request;

class AIContentTemplatesController extends Controller
{

    public function index()
    {
        $this->authorize("admin_ai_contents_templates_lists");

        $templates = AiContentTemplate::query()->orderBy('created_at', 'desc')
            ->withCount([
                'contents'
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.service_templates'),
            'templates' => $templates,
        ];

        return view('admin.ai_contents.templates.lists.index', $data);
    }


    public function create()
    {
        $this->authorize("admin_ai_contents_templates_create");


        $data = [
            'pageTitle' => trans('update.new_template'),
        ];

        return view('admin.ai_contents.templates.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("admin_ai_contents_templates_create");

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'type' => 'required|in:text,image',
            'prompt' => 'required_if:type,text',
            'image_size' => 'required_if:type,image',
        ]);

        $data = $request->all();

        $template = AiContentTemplate::query()->create([
            'type' => $data['type'],
            'enable_length' => (!empty($data['enable_length']) and $data['enable_length'] == "1"),
            'length' => (!empty($data['enable_length']) and !empty($data['length'])) ? $data['length'] : null,
            'image_size' => (!empty($data['image_size']) and $data['type'] == "image") ? $data['image_size'] : null,
            'enable' => (!empty($data['status']) and $data['status'] == "active"),
            'created_at' => time(),
        ]);

        AiContentTemplateTranslation::query()->updateOrCreate([
            'ai_content_template_id' => $template->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'prompt' => $data['prompt'] ?? null,
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.new_ai_content_template_were_successfully_created'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/ai-contents/templates/{$template->id}/edit"))->with(['toast' => $toastData]);
    }


    public function edit(Request $request, $id)
    {
        $this->authorize("admin_ai_contents_templates_edit");
        $template = AiContentTemplate::query()->findOrFail($id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $template->getTable(), $template->id);

        $data = [
            'pageTitle' => trans('update.edit_template') . ' ' . $template->title,
            'template' => $template,
            'locale' => $locale,
        ];

        return view('admin.ai_contents.templates.create.index', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("admin_ai_contents_templates_edit");
        $template = AiContentTemplate::query()->findOrFail($id);

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'type' => 'required|in:text,image',
            'prompt' => 'required_if:type,text',
            'image_size' => 'required_if:type,image',
        ]);

        $data = $request->all();

        $template->update([
            'type' => $data['type'],
            'enable_length' => (!empty($data['enable_length']) and $data['enable_length'] == "1"),
            'length' => (!empty($data['enable_length']) and !empty($data['length'])) ? $data['length'] : null,
            'image_size' => (!empty($data['image_size']) and $data['type'] == "image") ? $data['image_size'] : null,
            'enable' => (!empty($data['status']) and $data['status'] == "active"),
        ]);

        AiContentTemplateTranslation::query()->updateOrCreate([
            'ai_content_template_id' => $template->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'prompt' => $data['prompt'] ?? null,
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.new_ai_content_template_were_successfully_updated'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/ai-contents/templates/{$template->id}/edit"))->with(['toast' => $toastData]);
    }

    public function delete($id)
    {
        $this->authorize("admin_ai_contents_templates_delete");

        $template = AiContentTemplate::query()->findOrFail($id);
        $template->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.new_ai_content_template_were_successfully_deleted'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/ai-contents/templates"))->with(['toast' => $toastData]);
    }

    public function statusToggle($id)
    {
        $this->authorize("admin_ai_contents_templates_delete");

        $template = AiContentTemplate::query()->findOrFail($id);
        $template->update([
            'enable' => !$template->enable
        ]);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.new_ai_content_template_status_were_successfully_change'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/ai-contents/templates"))->with(['toast' => $toastData]);
    }
}
