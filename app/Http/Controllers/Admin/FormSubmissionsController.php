<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormRoleUserGroup;
use App\Models\FormSubmission;
use App\Models\FormSubmissionItem;
use App\Models\Group;
use App\Models\Role;
use App\Models\Translation\FormTranslation;
use Illuminate\Http\Request;

class FormSubmissionsController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize("admin_forms_submissions");

        $query = FormSubmission::query();

        $query = $this->handleFilters($request, $query);

        $submissions = $query
            ->with([
                'form',
                'user',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $forms = Form::query()->get();

        $data = [
            'pageTitle' => trans('update.submissions'),
            'submissions' => $submissions,
            'forms' => $forms,
        ];

        return view('admin.forms.submissions.index', $data);
    }

    private function handleFilters(Request $request, $query)
    {
        $formId = $request->get('form');
        $userIds = $request->get('user_ids');
        $from = $request->get('from');
        $to = $request->get('to');


        // $from and $to
        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($formId)) {
            $query->where('form_id', $formId);
        }

        if (!empty($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        return $query;
    }

    public function update(Request $request, $formId, $id)
    {
        $this->authorize("admin_forms_submissions");

        $form = Form::query()->where('id', $formId)
            ->with([
                'fields' => function ($query) {
                    $query->orderBy('order', 'asc');
                    $query->with([
                        'options' => function ($query) {
                            $query->orderBy('order', 'asc');
                        }
                    ]);
                }
            ])
            ->first();

        if (!empty($form)) {
            $submission = FormSubmission::query()->where("form_id", $form->id)
                ->where('id', $id)
                ->first();

            if (!empty($submission)) {
                $errors = $this->checkRequiredFields($request, $form);

                if (!empty($errors) and count($errors)) {
                    return back()->withErrors($errors)->withInput($request->all());
                }

                $fieldsData = $request->get('fields');

                foreach ($fieldsData as $fieldId => $value) {
                    FormSubmissionItem::query()->updateOrCreate([
                        "submission_id" => $submission->id,
                        "form_field_id" => $fieldId,
                    ], [
                        "value" => (is_array($value)) ? json_encode($value) : $value,
                    ]);
                }

                $toastData = [
                    'title' => trans('public.request_success'),
                    'msg' => trans('update.the_form_information_has_been_saved_successfully'),
                    'status' => 'success'
                ];

                return back()->with(['toast' => $toastData]);
            }
        }

        abort(404);
    }

    private function checkRequiredFields(Request $request, $form)
    {
        $errors = [];
        $fieldsData = $request->get('fields');


        foreach ($form->fields as $field) {
            if ($field->required and empty($fieldsData[$field->id])) {
                $errors[$field->id] = trans('validation.required', ['attribute' => $field->title]);
            }
        }

        return $errors;
    }

    public function show($id)
    {
        $this->authorize("admin_forms_submissions");

        $submission = FormSubmission::query()->where('id', $id)
            ->with([
                'form' => function ($query) {
                    $query->with([
                        'fields' => function ($query) {
                            $query->orderBy('order', 'asc');
                            $query->with([
                                'options' => function ($query) {
                                    $query->orderBy('order', 'asc');
                                }
                            ]);
                        }
                    ]);
                },
                'items'
            ])
            ->first();

        if (!empty($submission)) {
            $form = $submission->form;

            $data = [
                'pageTitle' => trans('update.submission_details'),
                'submission' => $submission,
                'form' => $form,
            ];

            return view('admin.forms.submissions.details', $data);
        }

        abort(404);
    }


    public function delete($id)
    {
        $this->authorize("admin_forms_submissions");

        $submission = FormSubmission::query()->findOrFail($id);

        $submission->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.form_submissions_were_successfully_deleted'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/forms/submissions"))->with(['toast' => $toastData]);
    }
}
