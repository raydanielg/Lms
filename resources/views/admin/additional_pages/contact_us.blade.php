@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.contact_us') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.contact_us') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ getAdminPanelUrl() }}/additional_page/contact_us" method="post">
                                {{ csrf_field() }}

                                <div class="row">

                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.contact_us_background') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text admin-file-manager" data-input="background_record" data-preview="holder">
                                                        <i class="fa fa-chevron-up"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="value[background]" id="background_record" value="{{ (!empty($value) and !empty($value['background'])) ? $value['background'] : '' }}" class="form-control"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.map_position') }}</label>
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" name="value[latitude]" value="{{ (!empty($value) and !empty($value['latitude'])) ? $value['latitude'] : '' }}" class="form-control" placeholder="{{ trans('admin/main.map_position_latitude') }}"/>
                                                </div>
                                                <div class="col">
                                                    <input type="text" name="value[longitude]" value="{{ (!empty($value) and !empty($value['longitude'])) ? $value['longitude'] : '' }}" class="form-control" placeholder="{{ trans('admin/main.map_position_longitude') }}"/>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.map_zoom') }}</label>
                                            <input type="text" name="value[map_zoom]" value="{{ (!empty($value) and !empty($value['map_zoom'])) ? $value['map_zoom'] : '' }}" class="form-control" placeholder="{{ trans('admin/main.map_zoom') }}"/>
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.contact_us_phones') }}</label>
                                            <input type="text" name="value[phones]" value="{{ (!empty($value) and !empty($value['phones'])) ? $value['phones'] : '' }}" class="form-control" placeholder="{{ trans('admin/main.contact_us_phones_placeholder') }}"/>
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.contact_us_emails') }}</label>
                                            <input type="text" name="value[emails]" value="{{ (!empty($value) and !empty($value['emails'])) ? $value['emails'] : '' }}" class="form-control" placeholder="{{ trans('admin/main.contact_us_emails_placeholder') }}"/>
                                        </div>

                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.contact_us_address') }}</label>
                                            <textarea name="value[address]" rows="5" class="form-control" placeholder="{{ trans('admin/main.contact_us_address') }}">{{ (!empty($value) and !empty($value['address'])) ? $value['address'] : '' }}</textarea>
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
    <script>
        var removeLang = '{{ trans('admin/main.remove') }}';
    </script>
    <script src="/assets/default/js/admin/contact_us.min.js"></script>
@endpush
