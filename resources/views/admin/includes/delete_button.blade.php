<button class="@if(empty($hideDefaultClass) or !$hideDefaultClass) {{ !empty($noBtnTransparent) ? '' : 'btn-transparent' }} text-primary @endif {{ $btnClass ?? '' }}"
        data-confirm="{{ $deleteConfirmMsg ?? trans('admin/main.delete_confirm_msg') }}"
        data-confirm-href="{{ $url }}"
        data-confirm-text-yes="{{ trans('admin/main.yes') }}"
        data-confirm-text-cancel="{{ trans('admin/main.cancel') }}"
        @if(empty($btnText))
        data-toggle="tooltip" data-placement="top" title="{{ !empty($tooltip) ? $tooltip : trans('admin/main.delete') }}"
    @endif
>
    @if(!empty($btnText))
        {!! $btnText !!}
    @else
        <i class="fa {{ !empty($btnIcon) ? $btnIcon : 'fa-times' }}" aria-hidden="true"></i>
    @endif
</button>
