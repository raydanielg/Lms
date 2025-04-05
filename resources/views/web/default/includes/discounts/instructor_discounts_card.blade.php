<div class="instructor-discount-card d-flex align-items-center justify-content-between px-15 rounded-sm bg-gray100 {{ !empty($instructorDiscountClassName) ? $instructorDiscountClassName : '' }}">
    <div class="d-flex align-items-center">
        <div class="instructor-discount-card__code d-flex-center py-15">
            <span class="font-20 font-weight-bold text-secondary mr-5">{{ $discount->code }}</span>
            <div class="form-group mb-0">
                <button type="button" class="js-copy btn-transparent" data-input="dis_code" data-toggle="tooltip" data-placement="top" title="{{ trans('public.copy') }}" data-copy-text="{{ trans('public.copy') }}" data-done-text="{{ trans('public.done') }}">
                    <i data-feather="copy" width="18" height="18" class="text-gray"></i>
                </button>

                <input type="hidden" name="dis_code" value="{{ $discount->code }}" class="form-control"/>
            </div>
        </div>

        <div class="pl-15 py-15">
            <h6 class="font-14 text-secondary font-weight-bold">{{ $discount->title }}</h6>
            <p class="font-12 mt-5 font-weight-500 text-gray">{{ $discount->subtitle }}</p>
        </div>
    </div>

    <img src="/assets/default/img/discount/ticket-discount.svg" alt="ticket-discount" class="img-fluid">
</div>

