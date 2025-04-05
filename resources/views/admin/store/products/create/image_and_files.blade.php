<section>
    <h2 class="section-title after-line mt-2 mb-4">{{ trans('update.images') }}</h2>

    <div class="row">
        <div class="col-12 col-md-6 mt-15">

            <div class="form-group mt-15">
                <label class="input-label">{{ trans('public.thumbnail_image') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text admin-file-manager" data-input="thumbnail" data-preview="holder">
                            <i class="fa fa-upload"></i>
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

                <div class="main-row input-group product-images-input-group mt-2">
                    <div class="input-group-prepend">
                        <button type="button" class="input-group-text admin-file-manager" data-input="images_record" data-preview="holder">
                            <i class="fa fa-upload"></i>
                        </button>
                    </div>
                    <input type="text" name="images[]" id="images_record" value="" class="form-control" placeholder="{{ trans('update.product_images_size') }}"/>

                    <button type="button" class="btn btn-primary btn-sm add-btn">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>

                @if(!empty($product->images) and count($product->images))
                    @foreach($product->images as $productImage)
                        <div class="input-group product-images-input-group mt-2">
                            <div class="input-group-prepend">
                                <button type="button" class="input-group-text admin-file-manager" data-input="images_{{ $productImage->id }}" data-preview="holder">
                                    <i class="fa fa-upload"></i>
                                </button>
                            </div>
                            <input type="text" name="images[]" id="images_{{ $productImage->id }}" value="{{ $productImage->path }}" class="form-control" placeholder="{{ trans('update.product_images_size') }}"/>

                            <button type="button" class="btn btn-sm btn-danger remove-btn">
                                <i class="fa fa-times"></i>
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
                        <button type="button" class="input-group-text admin-file-manager" data-input="demo_video" data-preview="holder">
                            <i class="fa fa-upload"></i>
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

        @if($product->isVirtual())
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="section-title after-line">{{ trans('public.files') }}</h2>

                    <div class="px-2 mt-3">
                        <button id="productAddFile" data-product-id="{{ $product->id }}" type="button" class="btn btn-primary btn-sm">{{ trans('public.add_new_files') }}</button>
                    </div>
                </div>

                <div class="mt-1">
                    <p class="font-14 text-gray">- {{ trans('update.product_files_hint_1') }}</p>
                </div>


                <div class="mt-2">
                    @if(!empty($product->files) and count($product->files))
                        <div class="table-responsive">
                            <table class="table table-striped text-center font-14">

                                <tr>
                                    <th>{{ trans('public.title') }}</th>
                                    <th>{{ trans('admin/main.description') }}</th>
                                    <th>{{ trans('admin/main.status') }}</th>
                                    <th class="text-right">{{ trans('admin/main.actions') }}</th>
                                </tr>

                                @foreach($product->files as $file)
                                    <tr>
                                        <td>
                                            <span class="d-block">{{ $file->title }}</span>
                                        </td>
                                        <td>
                                            <input type="hidden" value="{{ nl2br($file->description) }}">
                                            <button type="button" class="js-show-description btn btn-sm btn-light">{{ trans('admin/main.show') }}</button>
                                        </td>
                                        <td>{{ trans('admin/main.'.$file->status) }}</td>

                                        <td width="160" class="text-right">
                                            <button type="button" data-file-id="{{ $file->id }}" data-product-id="{{ !empty($product) ? $product->id : '' }}" class="edit-file btn-transparent text-primary mt-1" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/store/products/files/'. $file->id .'/delete', 'btnClass' => ' mt-1'])
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @else
                        @include(getTemplate() . '.includes.no-result',[
                            'file_name' => 'files.png',
                            'title' => trans('public.files_no_result'),
                            'hint' => trans('public.files_no_result_hint'),
                        ])
                    @endif
                </div>
            </div>

        @endif
    </div>
</section>
