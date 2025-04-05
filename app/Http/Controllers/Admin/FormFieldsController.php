<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\Translation\FormFieldOptionTranslation;
use App\Models\Translation\FormFieldTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormFieldsController extends Controller
{
    public function store(Request $request, $formId)
    {
        $this->authorize("admin_forms_edit");

        $form = Form::query()->findOrFail($formId);
        $data = $request->get('ajax')['new'];

        $validator = Validator::make($data, [
            'type' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $field = FormField::query()->create([
            'form_id' => $form->id,
            'type' => $data['type'],
            'required' => (!empty($data['required']) and $data['required'] == "on"),
        ]);

        FormFieldTranslation::query()->updateOrCreate([
            'form_field_id' => $field->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
        ]);

        /* Options */
        $this->handleOptions($request, $field, $data);

        return response()->json([
            'code' => 200,
        ]);
    }

    public function edit(Request $request, $formId, $id)
    {
        $this->authorize("admin_forms_edit");

        $field = FormField::query()->where('id', $id)
            ->with([
                'options' => function ($query) {
                    $query->orderBy('order', 'asc');
                }
            ])->first();

        $locale = mb_strtolower($request->get('locale', app()->getLocale()));

        return response()->json([
            'code' => 200,
            'title' => $field->title,
            'field' => $field,
            'locale' => $locale,
        ]);
    }

    public function update(Request $request, $formId, $id)
    {
        $this->authorize("admin_forms_edit");

        $form = Form::query()->findOrFail($formId);
        $data = $request->get('ajax')[$id];

        $validator = Validator::make($data, [
            'type' => 'required',
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $field = FormField::query()->where('form_id', $form->id)
            ->where('id', $id)
            ->first();

        if (!empty($field)) {
            $field->update([
                'form_id' => $form->id,
                'type' => $data['type'],
                'required' => (!empty($data['required']) and $data['required'] == "on"),
            ]);

            FormFieldTranslation::query()->updateOrCreate([
                'form_field_id' => $field->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
            ]);

            /* Options */
            $this->handleOptions($request, $field, $data);

            return response()->json([
                'code' => 200,
            ]);
        }

        return response()->json([], 422);
    }

    public function delete(Request $request, $formId, $id)
    {
        $this->authorize("admin_forms_edit");

        $form = Form::query()->findOrFail($formId);
        $field = FormField::query()->where('form_id', $form->id)
            ->where('id', $id)
            ->first();

        if (!empty($field)) {
            $field->delete();

            return response()->json([
                'code' => 200,
                'title' => trans('public.request_success'),
                'text' => trans('update.form_field_were_successfully_deleted'),
            ]);
        }

        abort(404);
    }

    private function handleOptions(Request $request, $field, $data)
    {
        if (!empty($request->get('ajax')['options']) and in_array($field->type, ['dropdown', 'checkbox', 'radio'])) {
            $options = $request->get('ajax')['options'];

            foreach ($options as $optionId => $option) {
                if (!empty($option['title'])) {
                    $fieldOption = FormFieldOption::query()->where('id', $optionId)
                        ->where('form_field_id', $field->id)
                        ->first();

                    if (empty($fieldOption)) {
                        $fieldOption = FormFieldOption::query()->create([
                            'form_field_id' => $field->id,
                        ]);
                    }

                    FormFieldOptionTranslation::query()->updateOrCreate([
                        'form_field_option_id' => $fieldOption->id,
                        'locale' => mb_strtolower($data['locale']),
                    ], [
                        'title' => $option['title'],
                    ]);
                }
            }
        }
    }

    public function orders(Request $request, $formId)
    {
        $this->authorize("admin_forms_edit");
        $form = Form::query()->findOrFail($formId);

        $items = $request->get('items');

        if (!empty($items)) {
            $itemIds = explode(',', $items);

            if (!is_array($itemIds) and !empty($itemIds)) {
                $itemIds = [$itemIds];
            }

            if (!empty($itemIds) and is_array($itemIds) and count($itemIds)) {

                foreach ($itemIds as $order => $id) {
                    FormField::query()->where('form_id', $form->id)
                        ->where('id', $id)
                        ->update(['order' => ($order + 1)]);
                }

                return response()->json([
                    'title' => trans('public.request_success'),
                    'msg' => trans('update.items_sorted_successful')
                ]);
            }
        }

        return response()->json([], 422);
    }

    public function orderOptions(Request $request, $formId, $fieldId)
    {
        $this->authorize("admin_forms_edit");

        $items = $request->get('items');

        if (!empty($items)) {
            $itemIds = explode(',', $items);

            if (!is_array($itemIds) and !empty($itemIds)) {
                $itemIds = [$itemIds];
            }

            if (!empty($itemIds) and is_array($itemIds) and count($itemIds)) {

                foreach ($itemIds as $order => $id) {
                    FormFieldOption::query()->where('form_field_id', $fieldId)
                        ->where('id', $id)
                        ->update(['order' => ($order + 1)]);
                }

                return response()->json([
                    'title' => trans('public.request_success'),
                    'msg' => trans('update.items_sorted_successful')
                ]);
            }
        }

        return response()->json([], 422);
    }

    public function deleteOption($formId, $fieldId, $optionId)
    {
        $this->authorize("admin_forms_edit");

        $option = FormFieldOption::query()->where('form_field_id', $fieldId)
            ->where('id', $optionId)
            ->first();

        if (!empty($option)) {
            $option->delete();

            return response()->json([
                'code' => 200,
                'title' => trans('public.request_success'),
                'text' => trans('update.form_field_option_were_successfully_deleted'),
            ]);
        }

        abort(404);
    }
}
