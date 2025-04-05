@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.report_reasons') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.report_reasons') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ getAdminPanelUrl() }}/reports/reasons" method="post">
                                {{ csrf_field() }}

                                <div class="row">
                                    <div class="col-12 col-md-6">

                                        @if(!empty(getGeneralSettings('content_translate')))
                                            <div class="form-group">
                                                <label class="input-label">{{ trans('auth.language') }}</label>
                                                <select name="locale" class="form-control js-edit-content-locale">
                                                    @foreach($userLanguages as $lang => $language)
                                                        <option value="{{ $lang }}" @if(mb_strtolower(request()->get('locale', app()->getLocale())) == mb_strtolower($lang)) selected @endif>{{ $language }}</option>
                                                    @endforeach
                                                </select>
                                                @error('locale')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        @else
                                            <input type="hidden" name="locale" value="{{ getDefaultLocale() }}">
                                        @endif


                                        <div id="addAccountTypes" class="ml-0">
                                            <strong class="d-block mb-4">{{ trans('admin/main.add_report_reasons') }}</strong>

                                            <div class="form-group main-row">
                                                <div class="d-flex align-items-stretch">
                                                    <input type="text" name="value[]"
                                                           class="form-control w-auto flex-grow-1"
                                                           placeholder="{{ trans('admin/main.choose_title') }}"/>

                                                    <button type="button" class="btn btn-success add-btn fas fa-plus ml-2"></button>
                                                </div>
                                                <div class="text-muted text-small mt-1">{{ trans('admin/main.report_reasons_hint') }}</div>
                                            </div>

                                            @if(!empty($value) and count($value))
                                                @foreach($value as $item)
                                                    <div class="form-group">
                                                        <div class="d-flex align-items-stretch">
                                                            <input type="text" name="value[]"
                                                                   class="form-control w-auto flex-grow-1"
                                                                   value="{{ $item }}"
                                                                   placeholder="{{ trans('admin/main.choose_title') }}"/>

                                                            <button type="button" class="btn fas fa-times remove-btn btn-danger ml-2"></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">{{ trans('admin/main.save_change') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/reports.min.js"></script>
@endpush
