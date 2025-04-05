@php
    $elId = "langFileSwitch_{$fileName}";

    if (!empty($folderName)) {
        $elId .= "_{$folderName}";
    }

@endphp

<div class="form-group d-flex align-items-center mb-1">

    <div class="custom-control custom-checkbox mr-1">
        <input type="checkbox" name="{{ $inputName }}[]" value="{{ $fileName }}" class="custom-control-input" id="{{ $elId }}">
        <label class="custom-control-label" for="{{ $elId }}"></label>
    </div>

    <label class="mb-0 cursor-pointer" for="{{ $elId }}">{{ $fileName }}</label>
</div>
