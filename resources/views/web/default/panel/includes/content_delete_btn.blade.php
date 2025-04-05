@php
    $allowInstructorDeleteContent = !!(!empty(getGeneralOptionsSettings('allow_instructor_delete_content')));
    $contentDeleteMethod = (!empty(getGeneralOptionsSettings('content_delete_method'))) ? getGeneralOptionsSettings('content_delete_method') : 'delete_directly';
@endphp

@if($allowInstructorDeleteContent)
    @if($contentDeleteMethod == "delete_directly")
        <a href="{{ $deleteContentUrl }}"
           class="delete-action {{ !empty($deleteContentClassName) ? $deleteContentClassName : '' }}"
        >{{ trans('public.delete') }}</a>
    @else
        <a href="{{ $deleteContentUrl }}" data-item="{{ $deleteContentItem->id }}" data-item-type="{{ $deleteContentItem->getMorphClass() }}"
           class="js-content-delete-action {{ !empty($deleteContentClassName) ? $deleteContentClassName : '' }}"
        >{{ trans('public.delete') }}</a>
    @endif
@endif
