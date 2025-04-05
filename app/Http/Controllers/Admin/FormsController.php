<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormRoleUserGroup;
use App\Models\FormSubmission;
use App\Models\Group;
use App\Models\Role;
use App\Models\Translation\FormTranslation;
use Illuminate\Http\Request;

class FormsController extends Controller
{
    public function index()
    {
        $this->authorize("admin_forms_lists");

        $forms = Form::query()
            ->withCount([
                'fields',
                'submissions'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($forms as $form) {
            $form->users_count = FormSubmission::query()
                ->where('form_id', $form->id)
                ->distinct('user_id')
                ->count('user_id');
        }

        $data = [
            'pageTitle' => trans('update.forms'),
            'forms' => $forms,
        ];

        return view('admin.forms.lists.index', $data);
    }

    public function create()
    {
        $this->authorize("admin_forms_create");

        $userGroups = Group::query()->where('status', 'active')->get();
        $roles = Role::query()->get();

        $data = [
            'pageTitle' => trans('update.new_form'),
            'userGroups' => $userGroups,
            'roles' => $roles,
        ];

        return view('admin.forms.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("admin_forms_create");

        $this->validate($request, [
            'url' => 'required|string|max:255|unique:forms',
            'title' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $startDate = !empty($data['start_date']) ? convertTimeToUTCzone($data['start_date'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_date']) ? convertTimeToUTCzone($data['end_date'], getTimezone())->getTimestamp() : null;

        $form = Form::query()->create([
            'url' => $data['url'],
            'cover' => $data['cover'] ?? null,
            'image' => $data['image'] ?? null,
            'enable_login' => (!empty($data['enable_login']) and $data['enable_login'] == "on"),
            'enable_resubmission' => (!empty($data['enable_resubmission']) and $data['enable_resubmission'] == "on"),
            'enable_welcome_message' => (!empty($data['enable_welcome_message']) and $data['enable_welcome_message'] == "on"),
            'enable_tank_you_message' => (!empty($data['enable_tank_you_message']) and $data['enable_tank_you_message'] == "on"),
            'welcome_message_image' => $data['welcome_message_image'] ?? null,
            'tank_you_message_image' => $data['tank_you_message_image'] ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'enable' => false,
            'created_at' => time(),
        ]);

        $this->storeExtraData($form, $data);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.new_form_were_successfully_created'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/forms/{$form->id}/edit"))->with(['toast' => $toastData]);
    }


    private function storeExtraData($form, $data)
    {
        FormTranslation::query()->updateOrCreate([
            'form_id' => $form->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'heading_title' => $data['heading_title'] ?? null,
            'description' => $data['description'] ?? null,
            'welcome_message_title' => $data['welcome_message_title'] ?? null,
            'welcome_message_description' => $data['welcome_message_description'] ?? null,
            'tank_you_message_title' => $data['tank_you_message_title'] ?? null,
            'tank_you_message_description' => $data['tank_you_message_description'] ?? null,
        ]);


        /* Roles Users Groups */
        FormRoleUserGroup::query()->where('form_id', $form->id)->delete();

        $items = [
            'role_ids' => 'role_id',
            'users_ids' => 'users_id',
            'group_ids' => 'group_id',
        ];

        foreach ($items as $item => $column) {
            if (!empty($data[$item])) {
                $insert = [];

                foreach ($data[$item] as $id) {
                    if (!empty($id)) {
                        $insert[] = [
                            'form_id' => $form->id,
                            "{$column}" => $id,
                        ];
                    }
                }

                if (!empty($insert)) {
                    FormRoleUserGroup::query()->insert($insert);
                }
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $this->authorize("admin_forms_edit");

        $form = Form::query()->where('id', $id)
            ->with([
                'fields' => function ($query) {
                    $query->orderBy('order', 'asc');
                    $query->with([
                        'options' => function ($query) {
                            $query->orderBy('order', 'asc');
                        }
                    ]);
                }
            ])->first();

        if (!empty($form)) {
            $locale = $request->get('locale', mb_strtolower(app()->getLocale()));
            storeContentLocale($locale, $form->getTable(), $form->id);

            $userGroups = Group::query()->where('status', 'active')->get();
            $roles = Role::query()->get();

            $data = [
                'pageTitle' => trans('update.edit_form'),
                'userGroups' => $userGroups,
                'roles' => $roles,
                'form' => $form,
            ];

            return view('admin.forms.create.index', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("admin_forms_edit");

        $form = Form::query()->findOrFail($id);

        $this->validate($request, [
            'url' => 'required|string|max:255|unique:forms,url,' . $form->id,
            'title' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $startDate = !empty($data['start_date']) ? convertTimeToUTCzone($data['start_date'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_date']) ? convertTimeToUTCzone($data['end_date'], getTimezone())->getTimestamp() : null;

        $form->update([
            'url' => $data['url'],
            'cover' => $data['cover'] ?? null,
            'image' => $data['image'] ?? null,
            'enable_login' => (!empty($data['enable_login']) and $data['enable_login'] == "on"),
            'enable_resubmission' => (!empty($data['enable_resubmission']) and $data['enable_resubmission'] == "on"),
            'enable_welcome_message' => (!empty($data['enable_welcome_message']) and $data['enable_welcome_message'] == "on"),
            'enable_tank_you_message' => (!empty($data['enable_tank_you_message']) and $data['enable_tank_you_message'] == "on"),
            'welcome_message_image' => $data['welcome_message_image'] ?? null,
            'tank_you_message_image' => $data['tank_you_message_image'] ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'enable' => (!empty($data['enable']) and $data['enable'] == "on"),
        ]);

        $this->storeExtraData($form, $data);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.form_were_successfully_updated'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/forms/{$form->id}/edit"))->with(['toast' => $toastData]);
    }

    public function delete($id)
    {
        $this->authorize("admin_forms_delete");

        $form = Form::query()->findOrFail($id);
        $form->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.form_were_successfully_deleted'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/forms"))->with(['toast' => $toastData]);
    }

}
