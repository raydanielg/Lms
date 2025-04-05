<div>
    <div class="custom-modal-body">
        <h2 class="section-title after-line">{{ trans('update.add_content') }}</h2>

        <div class="content-form" data-action="{{ getAdminPanelUrl("/product-badges/{$badge->id}/contents/").(!empty($content) ? $content->id.'/update' : 'store') }}">

            <div class="form-group">
                <label class="input-label d-block">{{ trans('admin/main.type') }}</label>
                <select id="contentType" name="type" class="js-ajax-type form-control">
                    <option value="">{{ trans('update.select_a_type') }}</option>

                    @foreach(['course', 'product', 'bundle', 'upcoming_course', 'blog_article'] as $type)
                        <option value="{{ $type }}" {{ (!empty($content) and $content->type == $type) ? 'selected' : '' }}>{{ trans("update.{$type}") }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback"></div>
            </div>

            <div class="js-content-fields js-field-course form-group {{ (!empty($content) and $content->type == "course") ? '' : 'd-none' }}">
                <label class="input-label">{{trans('admin/main.class')}}</label>
                <select name="course" class="js-ajax-course form-control modal-search-webinar-select2"
                        data-placeholder="Search classes">

                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="js-content-fields js-field-product form-group {{ (!empty($content) and $content->type == "product") ? '' : 'd-none' }}">
                <label class="input-label">{{trans('update.product')}}</label>
                <select name="product" class="js-ajax-product form-control modal-search-product-select2"
                        data-placeholder="{{ trans('update.search_product') }}">

                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="js-content-fields js-field-bundle form-group {{ (!empty($content) and $content->type == "bundle") ? '' : 'd-none' }}">
                <label class="input-label">{{trans('update.bundle')}}</label>
                <select name="bundle" class="js-ajax-bundle form-control modal-search-bundle-select2"
                        data-placeholder="{{ trans('update.search_bundle') }}">

                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="js-content-fields js-field-blog_article form-group {{ (!empty($content) and $content->type == "blog_article") ? '' : 'd-none' }}">
                <label class="input-label">{{ trans('admin/main.blog') }}</label>
                <select name="blog_article" class="js-ajax-blog_article form-control modal-search-blog-select2 " data-placeholder="Search blog">

                </select>
            </div>

            <div class="js-content-fields js-field-upcoming_course form-group {{ (!empty($content) and $content->type == "upcoming_course") ? '' : 'd-none' }}">
                <label class="input-label">{{ trans('update.upcoming_course') }}</label>
                <select name="upcoming_course" class="js-ajax-upcoming_course form-control modal-search-upcoming-course-select2 " data-placeholder="{{ trans("update.search_upcoming_course") }}">

                </select>
                <div class="invalid-feedback"></div>
            </div>

            <div class="d-flex align-items-center justify-content-end mt-3">
                <button type="button" class="js-save-content btn btn-sm btn-primary">{{ trans('public.save') }}</button>
                <button type="button" class="close-swl btn btn-sm btn-danger ml-2">{{ trans('public.close') }}</button>
            </div>

        </div>
    </div>
</div>
