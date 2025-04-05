<div class="row">
    <div class="col-12 ">

        <button type="button" class="js-add-form-field btn btn-success btn-sm">{{ trans('update.add_field') }}</button>

        <ul id="formFieldsCard" class="draggable-content-lists draggable-form-field-lists"
            data-drag-class="draggable-form-field-lists"
            data-path="{{ getAdminPanelUrl("/forms/{$form->id}/fields/orders") }}"
            data-move-class="move-icon"
        >

            @if(!empty($form->fields))
                @foreach($form->fields as $field)
                    @include('admin.forms.create.includes.field_accordion',['formField' => $field])
                @endforeach
            @endif

        </ul>

    </div>
</div>


<div id="newFormField" class="d-none">
    @include('admin.forms.create.includes.field_accordion')
</div>
