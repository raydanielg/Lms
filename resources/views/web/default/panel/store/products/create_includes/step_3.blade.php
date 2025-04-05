@push('styles_top')
    <link href="/assets/default/vendors/sortable/jquery-ui.min.css"/>
@endpush

<div class="row">

    @if($product->isVirtual())
        <div class="col-12 mb-40 mt-20">
            <div class="">
                <h2 class="section-title after-line">{{ trans('public.files') }}</h2>
            </div>
            <div class="mt-15">
                <p class="font-14 text-gray">- {{ trans('update.product_files_hint_1') }}</p>
            </div>
            <button id="productAddFile" data-product-id="{{ $product->id }}" type="button" class="btn btn-primary btn-sm mt-15">{{ trans('public.add_new_files') }}</button>


            <div class="accordion-content-wrapper mt-15" id="filesAccordion" role="tablist" aria-multiselectable="true">
                @if(!empty($product->files) and count($product->files))
                    <ul class="draggable-lists" data-order-path="/panel/store/products/files/order-items">
                        @foreach($product->files as $fileInfo)
                            @include('web.default.panel.store.products.create_includes.accordions.file',['file' => $fileInfo])
                        @endforeach
                    </ul>
                @else
                    @include(getTemplate() . '.includes.no-result',[
                        'file_name' => 'files.png',
                        'title' => trans('public.files_no_result'),
                        'hint' => trans('public.files_no_result_hint'),
                    ])
                @endif
            </div>

            <div id="newFileForm" class="d-none">
                @include('web.default.panel.store.products.create_includes.accordions.file')
            </div>

        </div>
    @endif


    <div class="col-12 col-md-6 mt-15">

        <div class="form-group mt-15">
            <label class="input-label">{{ trans('public.thumbnail_image') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text panel-file-manager" data-input="thumbnail" data-preview="holder">
                        <i data-feather="upload" width="18" height="18" class="text-white"></i>
                    </button>
                </div>
                <input type="text" name="thumbnail" id="thumbnail" value="{{ !empty($product) ? $product->thumbnail : old('thumbnail') }}" class="form-control @error('thumbnail')  is-invalid @enderror" placeholder="{{ trans('update.thumbnail_images_size') }}"/>
                @error('thumbnail')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>

        <div id="productImagesInputs" class="form-group mt-15">
            <label class="input-label mb-0">{{ trans('update.images') }}</label>

            <div class="main-row input-group product-images-input-group mt-10">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text panel-file-manager" data-input="images_record" data-preview="holder">
                        <i data-feather="upload" width="18" height="18" class="text-white"></i>
                    </button>
                </div>
                <input type="text" name="images[]" id="images_record" value="" class="form-control" placeholder="{{ trans('update.product_images_size') }}"/>

                <button type="button" class="btn btn-primary btn-sm add-btn">
                    <i data-feather="plus" width="18" height="18" class="text-white"></i>
                </button>
            </div>

            @if(!empty($product->images) and count($product->images))
                @foreach($product->images as $productImage)
                    <div class="input-group product-images-input-group mt-10">
                        <div class="input-group-prepend">
                            <button type="button" class="input-group-text panel-file-manager" data-input="images_{{ $productImage->id }}" data-preview="holder">
                                <i data-feather="upload" width="18" height="18" class="text-white"></i>
                            </button>
                        </div>
                        <input type="text" name="images[]" id="images_{{ $productImage->id }}" value="{{ $productImage->path }}" class="form-control" placeholder="{{ trans('update.product_images_size') }}"/>

                        <button type="button" class="btn btn-sm btn-danger remove-btn">
                            <i data-feather="x" width="18" height="18" class="text-white"></i>
                        </button>
                    </div>
                @endforeach
            @endif

            @error('images')
            <div class="invalid-feedback d-block">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group mt-25">
            <label class="input-label">{{ trans('public.demo_video') }} ({{ trans('public.optional') }})</label>

            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text text-white panel-file-manager" data-input="demo_video" data-preview="holder">
                        <i data-feather="upload" width="18" height="18" class="text-white"></i>
                    </button>
                </div>
                <input type="text" name="video_demo" id="demo_video" value="{{ !empty($product) ? $product->video_demo : old('video_demo') }}" class="form-control @error('video_demo')  is-invalid @enderror"/>
                @error('video_demo')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
    </div>

</div>

@push('scripts_bottom')
    <script src="/assets/default/vendors/sortable/jquery-ui.min.js"></script>
@endpush
