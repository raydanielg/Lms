<?php

namespace App\Http\Controllers\Web\traits;

use App\Models\Form;
use App\Models\FormFieldOption;
use App\Models\UserFormField;
use Illuminate\Http\Request;

trait UserFormFieldsTrait
{
    public function getFormFieldsByUserType(Request $request, $type = null, $justHtml = false, $user = null, $becomeInstructor = null)
    {
        $html = "";

        if (empty($type)) {
            $type = $request->get('type');
        }

        if (!empty($type)) {
            $form = $this->getFormFieldsByType($type);

            if (!empty($form) and count($form->fields)) {

                $values = [];

                if (!empty($user)) {
                    $userFields = UserFormField::query()->where('user_id', $user->id)->get();

                    foreach ($userFields as $userField) {
                        $values[$userField->form_field_id] = $userField->value;
                    }
                }

                if (!empty($becomeInstructor)) {
                    $becomeInstructorFields = UserFormField::query()->where('become_instructor_id', $becomeInstructor->id)->get();

                    foreach ($becomeInstructorFields as $becomeInstructorField) {
                        $values[$becomeInstructorField->form_field_id] = $becomeInstructorField->value;
                    }
                }

                $html = (string)view()->make('web.default.forms.handle_field', ['fields' => $form->fields, 'values' => $values]);
            }
        }

        if ($justHtml) {
            return $html;
        }

        return response()->json([
            'html' => $html
        ]);
    }

    private function getFormFieldsByType($type)
    {
        switch ($type) {
            case "user":
                $formId = getFeaturesSettings("user_register_form");
                break;

            case "teacher":
                $formId = getFeaturesSettings("instructor_register_form");
                break;

            case "organization":
                $formId = getFeaturesSettings("organization_register_form");
                break;

            case "become_instructor":
                $formId = getFeaturesSettings("become_instructor_form");
                break;

            case "become_organization":
                $formId = getFeaturesSettings("become_organization_form");
                break;
        }

        if (!empty($formId)) {
            $form = Form::query()->where('id', $formId)
                ->where('enable', true)
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

            return $form;
        }

        return null;
    }

    private function checkFormRequiredFields(Request $request, $form)
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

    private function storeFormFields($data, $user)
    {
        $fieldsData = $data['fields'] ?? [];

        UserFormField::query()->where('user_id', $user->id)->delete();

        if (count($fieldsData)) {
            foreach ($fieldsData as $fieldId => $value) {
                UserFormField::query()->updateOrCreate([
                    "user_id" => $user->id,
                    "form_field_id" => $fieldId,
                    "value" => (is_array($value)) ? json_encode($value) : $value,
                    'created_at' => time()
                ]);
            }
        }
    }

    private function storeBecomeInstructorFormFields($data, $become)
    {
        $fieldsData = $data['fields'] ?? [];

        UserFormField::query()->where('become_instructor_id', $become->id)->delete();

        if (count($fieldsData)) {
            foreach ($fieldsData as $fieldId => $value) {
                UserFormField::query()->updateOrCreate([
                    "become_instructor_id" => $become->id,
                    "form_field_id" => $fieldId,
                    "value" => (is_array($value)) ? json_encode($value) : $value,
                    'created_at' => time()
                ]);
            }
        }
    }

    private function handleFieldsForExport($form, $user, $items)
    {
        $userFields = UserFormField::query()->where('user_id', $user->id)->get();

        foreach ($form->fields as $field) {
            $userField = $userFields->where('form_field_id', $field->id)->first();

            $value = null;
            if (!empty($userField)) {
                $value = $userField->value;

                if ($field->type == "checkbox") {
                    $value = "";

                    if (!empty($userField->value)) {
                        $optionIds = json_decode($userField->value, true);

                        if (is_array($optionIds)) {
                            $options = FormFieldOption::query()->whereIn('id', $optionIds)
                                ->where('form_field_id', $field->id)
                                ->get();

                            foreach ($options as $option) {
                                $value .= ", " . $option->title;
                            }
                        }
                    }
                } else if (in_array($field->type, ["dropdown", "radio"])) {
                    $value = "";

                    $option = FormFieldOption::query()->where('id', $userField->value)
                        ->where('form_field_id', $field->id)
                        ->first();

                    if (!empty($option)) {
                        $value = $option->title;
                    }
                }
            }

            $items[] = $value;
        }

        return $items;
    }

}
