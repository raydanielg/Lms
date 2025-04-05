@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('update.forms') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ getAdminPanelUrl("/forms/{$form->id}/submissions/{$submission->id}/update") }}" method="post" class="mt-30">
                                {{ csrf_field() }}

                                @foreach($form->fields as $field)
                                    @php
                                        $item = $submission->items->where('form_field_id', $field->id)->first();
                                        $value = null;

                                        if (!empty($item)) {
                                            $value = $item->value;
                                        }
                                    @endphp

                                    @if($field->type == "input")
                                        <div class="form-group">
                                            <label class="input-label" for="code">{{ $field->title }}:</label>
                                            <input type="text" name="fields[{{ $field->id }}]" class="form-control @error($field->id) is-invalid @enderror" value="{{ $value }}" >
                                            @error($field->id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    @elseif($field->type == "number")
                                        <div class="form-group">
                                            <label class="input-label" for="code">{{ $field->title }}:</label>
                                            <input type="number" name="fields[{{ $field->id }}]" class="form-control @error($field->id) is-invalid @enderror" value="{{ $value }}" >
                                            @error($field->id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    @elseif($field->type == "textarea")
                                        <div class="form-group">
                                            <label class="input-label" for="code">{{ $field->title }}:</label>
                                            <textarea rows="4" name="fields[{{ $field->id }}]" class="form-control @error($field->id) is-invalid @enderror" >{{ $value }}</textarea>
                                            @error($field->id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    @elseif($field->type == "upload")
                                        <div class="form-group">
                                            <label class="input-label">{{ $field->title }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text panel-file-manager" disabled data-input="upload_{{ $field->id }}" data-preview="holder">
                                                        <i class="fa fa-upload"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="fields[{{ $field->id }}]" id="upload_{{ $field->id }}" class="form-control @error($field->id) is-invalid @enderror" value="{{ $value }}" />
                                                <div class="input-group-append">
                                                    <button type="button" class="input-group-text admin-file-view" data-input="upload_{{ $field->id }}">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            @error($field->id)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    @elseif($field->type == "date_picker")
                                        <div class="form-group">
                                            <label class="input-label">{{ $field->title }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                <span class="input-group-text" id="dateRangeLabel{{ $field->id }}">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                                </div>

                                                <input type="text" name="fields[{{ $field->id }}]" class="form-control text-center datetimepicker @error($field->id) is-invalid @enderror"
                                                       aria-describedby="dateRangeLabel{{ $field->id }}" autocomplete="off" value="{{ $value }}" />
                                            </div>

                                            @error($field->id)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    @elseif($field->type == "toggle")
                                        <div class="mb-20">
                                            <div class="d-flex align-items-center ">
                                                <label class="mb-0 mr-10 cursor-pointer" for="toggle{{ $field->id }}">{{ $field->title }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="fields[{{ $field->id }}]" class="custom-control-input" id="toggle{{ $field->id }}" {{ ($value == "on" ? 'checked' : '') }} >
                                                    <label class="custom-control-label" for="toggle{{ $field->id }}"></label>
                                                </div>
                                            </div>
                                            @error($field->id)
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    @elseif($field->type == "dropdown")
                                        @if(!empty($field->options) and count($field->options))
                                            <div class="form-group">
                                                <label class="input-label">{{ $field->title }}:</label>
                                                <select name="fields[{{ $field->id }}]" class="form-control @error($field->id) is-invalid @enderror" >
                                                    <option value="" class="">{{ trans('update.select_a_option') }}</option>
                                                    @foreach($field->options as $option)
                                                        <option value="{{ $option->id }}" {{ ($value == $option->id) ? 'selected' : '' }}>{{ $option->title }}</option>
                                                    @endforeach
                                                </select>
                                                @error($field->id)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif

                                    @elseif($field->type == "checkbox")
                                        @php
                                            if (!empty($value)) {
                                                $value = json_decode($value, true);
                                            }
                                        @endphp

                                        @if(!empty($field->options) and count($field->options))
                                            <div class="form-group">
                                                <label class="input-label">{{ $field->title }}:</label>

                                                @foreach($field->options as $option)
                                                    <div class="custom-control custom-checkbox mt-10">
                                                        <input type="checkbox" name="fields[{{ $field->id }}][]" value="{{ $option->id }}" class="custom-control-input" id="checkbox{{ $option->id }}" {{ (is_array($value) and in_array($option->id, $value)) ? 'checked' : '' }} >
                                                        <label class="custom-control-label font-14" for="checkbox{{ $option->id }}">{{ $option->title }}</label>
                                                    </div>
                                                @endforeach

                                                @error($field->id)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif

                                    @elseif($field->type == "radio")
                                        @if(!empty($field->options) and count($field->options))
                                            <div class="form-group">
                                                <label class="input-label">{{ $field->title }}:</label>

                                                @foreach($field->options as $option)
                                                    <div class="custom-control custom-radio mt-10">
                                                        <input type="radio" name="fields[{{ $field->id }}]" value="{{ $option->id }}" class="custom-control-input" id="radio{{ $option->id }}" {{ ($value == $option->id) ? 'checked' : '' }} >
                                                        <label class="custom-control-label font-14" for="radio{{ $option->id }}">{{ $option->title }}</label>
                                                    </div>
                                                @endforeach

                                                @error($field->id)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif

                                    @endif
                                @endforeach


                                <div class="d-flex align-items-center justify-content-end mt-30">
                                    <button type="button" class="js-clear-form btn btn-outline-primary mr-2">{{ trans('update.clear_form') }}</button>

                                    <button type="submit" class="btn btn-primary">{{ trans('update.submit_form') }}</button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/form_submissions_details.min.js"></script>
@endpush
