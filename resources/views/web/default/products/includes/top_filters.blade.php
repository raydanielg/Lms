<div id="topFilters" class="shadow-lg border border-gray300 rounded-sm p-10 p-md-20">
    <div class="row align-items-center">

        <div class="col-lg-9 d-block d-md-flex align-items-center justify-content-start my-25 my-lg-0">

            <div class="d-flex align-items-center justify-content-between justify-content-md-center">
                <label class="mb-0 mr-10 cursor-pointer" for="free">{{ trans('public.free') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="free" class="custom-control-input" id="free" @if(request()->get('free', null) == 'on') checked="checked" @endif>
                    <label class="custom-control-label" for="free"></label>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between justify-content-md-center mx-0 mx-md-20 my-20 my-md-0">
                <label class="mb-0 mr-10 cursor-pointer" for="free_shipping">{{ trans('update.free_shipping') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="free_shipping" class="custom-control-input" id="free_shipping" @if(request()->get('free_shipping', null) == 'on') checked="checked" @endif>
                    <label class="custom-control-label" for="free_shipping"></label>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between justify-content-md-center">
                <label class="mb-0 mr-10 cursor-pointer" for="discount">{{ trans('public.discount') }}</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="discount" class="custom-control-input" id="discount" @if(request()->get('discount', null) == 'on') checked="checked" @endif>
                    <label class="custom-control-label" for="discount"></label>
                </div>
            </div>

        </div>

        <div class="col-lg-3 d-flex align-items-center">
            <select name="sort" class="form-control font-14">
                <option disabled selected>{{ trans('public.sort_by') }}</option>
                <option value="">{{ trans('public.all') }}</option>
                <option value="newest" @if(request()->get('sort', null) == 'newest') selected="selected" @endif>{{ trans('public.newest') }}</option>
                <option value="expensive" @if(request()->get('sort', null) == 'expensive') selected="selected" @endif>{{ trans('public.expensive') }}</option>
                <option value="inexpensive" @if(request()->get('sort', null) == 'inexpensive') selected="selected" @endif>{{ trans('public.inexpensive') }}</option>
                <option value="bestsellers" @if(request()->get('sort', null) == 'bestsellers') selected="selected" @endif>{{ trans('public.bestsellers') }}</option>
                <option value="best_rates" @if(request()->get('sort', null) == 'best_rates') selected="selected" @endif>{{ trans('public.best_rates') }}</option>
            </select>
        </div>

    </div>
</div>
