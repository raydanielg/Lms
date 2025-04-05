<div class="row mt-15">
    <div class="col-12 col-md-6">
        <div class="form-group ">
            <label class="input-label">{{ trans('update.upfront') }}</label>
            <input type="number" name="upfront" value="{{ !empty($installment) ? $installment->upfront : old('upfront') }}" class="form-control @error('upfront')  is-invalid @enderror"/>
            @error('upfront')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="form-group ">
            <label class="input-label">{{ trans('update.upfront_type') }}</label>
            <select name="upfront_type" class="form-control">
                <option value="fixed_amount" {{ (!empty($installment) and $installment->upfront_type == 'fixed_amount') ? 'selected' : '' }}>{{ trans('update.fixed_amount') }}</option>
                <option value="percent" {{ (!empty($installment) and $installment->upfront_type == 'percent') ? 'selected' : '' }}>{{ trans('update.percent') }}</option>
            </select>
            @error('upfront_type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
    </div>
</div>

<div class="row mt-20">
    <div class="col-12">

        {{-- Installment Steps --}}
        <div id="installmentStepsCard" class="mt-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="font-16 text-dark">{{ trans('update.payment_steps') }}</h5>

                        <button type="button" class="js-add-btn btn btn-success ml-3">
                            <i class="fa fa-plus"></i>
                            {{ trans('update.add_step') }}
                        </button>
                    </div>
                </div>
            </div>

            @if(!empty($installment) and !empty($installment->steps))
                @php
                    $installmentSteps = explode(',', $installment->options);
                @endphp
                @foreach($installment->steps as $stepRow)
                    @include('admin.financial.installments.create.includes.installment_step_inputs',['step' => $stepRow])
                @endforeach
            @endif

        </div>

    </div>
</div>

