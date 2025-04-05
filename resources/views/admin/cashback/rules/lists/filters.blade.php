{{-- Filters --}}
<section class="card">
    <div class="card-body">
        <form method="get" class="mb-0">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="input-label">{{trans('admin/main.search')}}</label>
                        <input name="title" type="text" class="form-control" value="{{ request()->get('title') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="input-label">{{trans('admin/main.start_date')}}</label>
                        <div class="input-group">
                            <input type="date" id="from" class="text-center form-control" name="from" value="{{ request()->get('from') }}" placeholder="Start Date">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="input-label">{{trans('admin/main.end_date')}}</label>
                        <div class="input-group">
                            <input type="date" id="to" class="text-center form-control" name="to" value="{{ request()->get('to') }}" placeholder="End Date">
                        </div>
                    </div>
                </div>


                @php
                    $filters = ['amount_asc', 'amount_desc', 'paid_amount_asc', 'paid_amount_desc', 'date_asc', 'date_desc'];
                @endphp
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="input-label">{{trans('admin/main.filters')}}</label>
                        <select name="sort" data-plugin-selectTwo class="form-control populate">
                            <option value="">{{trans('admin/main.all')}}</option>

                            @foreach($filters as $filter)
                                <option value="{{ $filter }}" @if(request()->get('sort') == $filter) selected @endif>{{trans('update.'.$filter)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label class="input-label">{{trans('admin/main.type')}}</label>
                        <select name="target_type" class="form-control populate">
                            <option value="">{{trans('admin/main.all')}}</option>

                            @foreach(\App\Models\CashbackRule::$targetTypes as $type)
                                <option value="{{ $type }}" @if(request()->get('target_type') == $type) selected @endif>{{ trans('update.target_types_'.$type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="input-label">{{trans('admin/main.status')}}</label>
                        <select name="status" class="form-control populate">
                            <option value="">{{trans('admin/main.all')}}</option>

                            <option value="active" {{ (request()->get('status') == 'active') ? 'selected' : '' }}>{{ trans('admin/main.active') }}</option>
                            <option value="inactive" {{ (request()->get('status') == 'inactive') ? 'selected' : '' }}>{{ trans('admin/main.inactive') }}</option>
                        </select>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group mt-1">
                        <label class="input-label mb-4"> </label>
                        <input type="submit" class="text-center btn btn-primary w-100" value="{{trans('admin/main.show_results')}}">
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
