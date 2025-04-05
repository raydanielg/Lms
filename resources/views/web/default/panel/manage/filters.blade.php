<div class="panel-section-card py-20 px-25 mt-20">
    <form action="/panel/manage/{{ $user_type }}" method="get" class="row">
        <div class="col-12 col-lg-4">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="input-label">{{ trans('public.from') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="dateInputGroupPrepend">
                                    <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                </span>
                            </div>
                            <input type="text" name="from" autocomplete="off" value="{{ request()->get('from') }}" class="form-control {{ !empty(request()->get('from')) ? 'datepicker' : 'datefilter' }}" aria-describedby="dateInputGroupPrepend"/>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="input-label">{{ trans('public.to') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="dateInputGroupPrepend">
                                    <i data-feather="calendar" width="18" height="18" class="text-white"></i>
                                </span>
                            </div>
                            <input type="text" name="to" autocomplete="off" value="{{ request()->get('to') }}" class="form-control {{ !empty(request()->get('to')) ? 'datepicker' : 'datefilter' }}" aria-describedby="dateInputGroupPrepend"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="row">
                <div class="col-12 col-lg-5">
                    <div class="form-group">
                        <label class="input-label">{{ trans('auth.name') }}</label>
                        <input type="text" name="name" value="{{ request()->get('name',null) }}" class="form-control"/>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="form-group">
                        <label class="input-label">{{ trans('auth.email') }}</label>
                        <input type="text" name="email" value="{{ request()->get('email',null) }}" class="form-control"/>
                    </div>
                </div>

                <div class="col-12 col-lg-3">
                    <div class="form-group">
                        <label class="input-label d-block">{{ trans('public.type') }}</label>
                        <select name="type" class="form-control">
                            <option >{{ trans('public.all') }}</option>
                            <option value="active" @if(request()->get('type',null) == 'active') selected @endif>{{ trans('public.active') }}</option>
                            <option value="inactive" @if(request()->get('type',null) == 'inactive') selected @endif>{{ trans('public.inactive') }}</option>
                            <option value="verified" @if(request()->get('type',null) == 'verified') selected @endif>{{ trans('public.verified') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-2 d-flex align-items-center justify-content-end">
            <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">{{ trans('public.show_results') }}</button>
        </div>
    </form>
</div>
