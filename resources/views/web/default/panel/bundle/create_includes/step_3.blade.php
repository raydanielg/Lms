@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush

<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group mt-30 d-flex align-items-center justify-content-between mb-5">
            <label class="cursor-pointer input-label" for="subscribeSwitch">{{ trans('update.include_subscribe') }}</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" name="subscribe" class="custom-control-input" id="subscribeSwitch" {{ !empty($bundle) && $bundle->subscribe ? 'checked' : (old('subscribe') ? 'checked' : '')  }}>
                <label class="custom-control-label" for="subscribeSwitch"></label>
            </div>
        </div>

        <div>
            <p class="font-12 text-gray">- {{ trans('forms.subscribe_hint') }}</p>
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('update.access_days') }} ({{ trans('public.optional') }})</label>
            <input type="number" name="access_days" value="{{ !empty($bundle) ? $bundle->access_days : old('access_days') }}" class="form-control @error('access_days')  is-invalid @enderror"/>
            @error('access_days')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
            <p class="font-12 text-gray mt-10">- {{ trans('update.access_days_input_hint') }}</p>
        </div>

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.price') }} ({{ $currency }})</label>
            <input type="number" name="price" value="{{ (!empty($bundle) and !empty($bundle->price)) ? convertPriceToUserCurrency($bundle->price) : old('price') }}" class="form-control @error('price')  is-invalid @enderror" placeholder="{{ trans('public.0_for_free') }}"/>
            @error('price')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
</div>

<section class="mt-30">
    <div class="">
        <h2 class="section-title after-line">{{ trans('webinars.sale_plans') }} ({{ trans('public.optional') }})</h2>


        <div class="mt-15">
            <p class="font-12 text-gray">- {{ trans('webinars.sale_plans_hint_1') }}</p>
            <p class="font-12 text-gray">- {{ trans('webinars.sale_plans_hint_2') }}</p>
            <p class="font-12 text-gray">- {{ trans('webinars.sale_plans_hint_3') }}</p>
        </div>
    </div>

    <button id="webinarAddTicket" data-webinar-id="{{ $bundle->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('public.add_plan') }}</button>

    <div class="row mt-10">
        <div class="col-12">

            <div class="accordion-content-wrapper mt-15" id="ticketsAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($bundle->tickets) and count($bundle->tickets))
                    <ul class="draggable-lists" data-order-table="tickets">
                        @foreach($bundle->tickets as $ticketInfo)
                            @include('web.default.panel.bundle.create_includes.accordions.ticket',['bundle' => $bundle,'ticket' => $ticketInfo])
                        @endforeach
                    </ul>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'ticket.png',
                        'title' => trans('public.ticket_no_result'),
                        'hint' => trans('public.ticket_no_result_hint'),
                    ])
                @endif
            </div>
        </div>
    </div>
</section>

<div id="newTicketForm" class="d-none">
    @include('web.default.panel.bundle.create_includes.accordions.ticket',['bundle' => $bundle])
</div>

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
@endpush
