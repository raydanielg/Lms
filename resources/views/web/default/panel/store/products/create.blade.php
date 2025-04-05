@extends('web.default.panel.layouts.panel_layout')

@push('styles_top')

@endpush

@section('content')
    <div class="">

        <form method="post" action="/panel/store/products/{{ !empty($product) ? $product->id .'/update' : 'store' }}" id="productForm" class="webinar-form">
            @include('web.default.panel.store.products.create_includes.progress')

            {{ csrf_field() }}
            <input type="hidden" name="current_step" value="{{ !empty($currentStep) ? $currentStep : 1 }}">
            <input type="hidden" name="draft" value="no" id="forDraft"/>
            <input type="hidden" name="get_next" value="no" id="getNext"/>
            <input type="hidden" name="get_step" value="0" id="getStep"/>


            @if($currentStep == 1)
                @include('web.default.panel.store.products.create_includes.step_1')
            @elseif(!empty($product))
                @include('web.default.panel.store.products.create_includes.step_'.$currentStep)
            @endif

        </form>


        <div class="create-webinar-footer d-flex flex-column flex-md-row align-items-center justify-content-between mt-20 pt-15 border-top">
            <div class="d-flex align-items-center">

                @if(!empty($product))
                    <a href="/panel/store/products/{{ $product->id }}/step/{{ ($currentStep - 1) }}" class="btn btn-sm btn-primary {{ $currentStep < 2 ? 'disabled' : '' }}">{{ trans('webinars.previous') }}</a>
                @else
                    <a href="" class="btn btn-sm btn-primary disabled">{{ trans('webinars.previous') }}</a>
                @endif

                <button type="button" id="getNextStep" class="btn btn-sm btn-primary ml-15" @if($currentStep >= 5) disabled @endif>{{ trans('webinars.next') }}</button>
            </div>

            <div class="mt-20 mt-md-0">
                <button type="button" id="sendForReview" class="btn btn-sm btn-primary">{{ trans('public.send_for_review') }}</button>

                <button type="button" id="saveAsDraft" class=" btn btn-sm btn-primary">{{ trans('public.save_as_draft') }}</button>

                @if(!empty($product) and $product->creator_id == $authUser->id)
                    @include('web.default.panel.includes.content_delete_btn', [
                        'deleteContentUrl' => "/panel/store/products/{$product->id}/delete?redirect_to=/panel/store/products",
                        'deleteContentClassName' => 'webinar-actions btn btn-sm btn-danger mt-20 mt-md-0',
                        'deleteContentItem' => $product,
                    ])
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script>
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var requestFailedLang = '{{ trans('public.request_failed') }}';
        var maxFourImageCanSelect = '{{ trans('update.max_four_image_can_select') }}';
    </script>

    <script src="/assets/default/js/panel/new_product.min.js"></script>
@endpush
