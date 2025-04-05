<section>
    <h2 class="section-title after-line mt-2 mb-4">{{ trans('public.extra_information') }}</h2>

    <div class="row">
    <div class="col-12 col-md-6 mt-15">

        <div class="form-group">
            <label class="input-label">{{ trans('public.price') }} ({{ $currency }})</label>
            <input type="number" name="price" value="{{ !empty($product) ? $product->price : old('price') }}" class="form-control @error('price')  is-invalid @enderror" placeholder="{{ trans('public.0_for_free') }}"/>
            @error('price')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        @if($product->isPhysical())
            <div class="form-group">
                <label class="input-label">{{ trans('update.delivery_fee') }}</label>
                <input type="number" name="delivery_fee" value="{{ !empty($product) ? $product->delivery_fee : old('delivery_fee') }}" class="form-control @error('delivery_fee')  is-invalid @enderror" placeholder="{{ trans('update.0_for_free_delivery') }}"/>
                @error('delivery_fee')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label">{{ trans('update.delivery_estimated_time') }} ({{ trans('public.day') }})</label>
                <input type="number" name="delivery_estimated_time" value="{{ !empty($product) ? $product->delivery_estimated_time : old('delivery_estimated_time') }}" class="form-control @error('delivery_estimated_time')  is-invalid @enderror" placeholder=""/>
                @error('delivery_estimated_time')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        @endif

        <div class="form-group js-inventory-inputs {{ (!empty($product) and $product->unlimited_inventory) ? 'd-none' : '' }}">
            <label class="input-label">{{ trans('update.inventory') }}</label>
            <input type="number" name="inventory" value="{{ (!empty($product) and $product->getAvailability() != 99999) ? $product->getAvailability() : old('inventory') }}" class="form-control @error('inventory')  is-invalid @enderror" placeholder=""/>
            @error('inventory')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group js-inventory-inputs {{ (!empty($product) and $product->unlimited_inventory) ? 'd-none' : '' }}">
            <label class="input-label">{{ trans('update.inventory_warning') }}</label>
            <input type="number" name="inventory_warning" value="{{ !empty($product) ? $product->inventory_warning : old('inventory_warning') }}" class="form-control @error('inventory_warning')  is-invalid @enderror" placeholder=""/>
            <div class="text-muted text-small mt-1">{{ trans('update.inventory_warning_hint') }}</div>
            @error('inventory_warning')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        {{-- Product Badges --}}
        @if(!empty($product))
            @include('admin.product_badges.content_include', ['itemTarget' => $product])
        @endif

        <div class="form-group mb-1 d-flex align-items-center">
            <label class="cursor-pointer mb-0 input-label mr-2" for="unlimitedInventorySwitch">{{ trans('update.unlimited_inventory') }}</label>
            <div class="custom-control custom-switch d-inline-block">
                <input type="checkbox" name="unlimited_inventory" class="custom-control-input" id="unlimitedInventorySwitch" {{ (!empty($product) and $product->unlimited_inventory) ? 'checked' :  '' }}>
                <label class="custom-control-label" for="unlimitedInventorySwitch"></label>
            </div>
        </div>

        <p class="text-gray font-12">{{ trans('update.create_product_unlimited_inventory_hint') }}</p>

    </div>
</div>

</section>
