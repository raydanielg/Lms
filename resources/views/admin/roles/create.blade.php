@extends('admin.layouts.app')

@push('libraries_top')


@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{!empty($role) ?trans('/admin/main.edit'): trans('admin/main.new') }} {{ trans('admin/main.role') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}/roles">{{ trans('admin/main.roles') }}</a>
                </div>
                <div class="breadcrumb-item">{{!empty($role) ?trans('/admin/main.edit'): trans('admin/main.new') }}</div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="section-body">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ getAdminPanelUrl() }}/roles/{{ !empty($role) ? $role->id.'/update' : 'store' }}" method="Post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-6">

                                        @if(empty($role))
                                            <div class="form-group @error('name') is-invalid @enderror">
                                                <label>{{ trans('admin/main.name') }}</label>
                                                <input type="text" name="name" class="form-control"
                                                       value="{{ !empty($role) ? $role->name : old('name') }}"
                                                       placeholder=""/>

                                                <p class="mt-1 text-muted">{{ trans('update.role_name_hint') }}</p>
                                            </div>

                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        @endif

                                        <div class="form-group @error('caption') is-invalid @enderror">
                                            <label>{{ trans('admin/main.caption') }}</label>
                                            <input type="text" name="caption" class="form-control" value="{{ !empty($role) ? $role->caption : old('caption') }}"
                                                   placeholder="{{ trans('admin/main.create_field_name_placeholder') }}"/>

                                            @error('caption')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        @if(empty($role) or !$role->isDefaultRole())
                                            <div class="form-group mb-1">
                                                <label class="custom-switch pl-0">
                                                    <input id="isAdmin" type="checkbox" name="is_admin" class="custom-switch-input section-parent" {{ (!empty($role) && $role->is_admin) ? 'checked' : '' }}>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description">{{ trans('admin/main.is_admin') }}</span>
                                                </label>
                                            </div>
                                            <div class="text-muted text-small mt-1">{{ trans('admin/main.new_role_admin_access_hint') }}</div>
                                        @endif

                                    </div>
                                </div>


                                    <div class="form-group" id="sections">

                                        <h2 class="section-title">{{ trans('admin/main.permission') }}</h2>
                                        <p class="section-lead">
                                            {{ trans('admin/main.permission_description') }}
                                        </p>

                                        <div class="row">
                                            @foreach($sections as $section)
                                                <div class="section-card is_{{ $section->type }} col-12 col-md-6 col-lg-4 {{ (!empty($role) and $role->is_admin and $section->type == 'panel') ? 'd-none' : '' }} {{ (!empty($role) and !$role->is_admin and $section->type == 'admin') ? 'd-none' : '' }} {{ (empty($role) and $section->type == 'admin') ? 'd-none' : '' }}">
                                                    <div class="card card-primary section-box">
                                                        <div class="card-header">
                                                            <input type="checkbox" name="permissions[]" id="permissions_{{ $section->id }}" value="{{ $section->id }}"
                                                                   {{isset($permissions[$section->id]) ? 'checked' : ''}} class="form-check-input mt-0 section-parent">
                                                            <label class="form-check-label font-16 font-weight-bold cursor-pointer" for="permissions_{{ $section->id }}">
                                                                {{ $section->caption }}
                                                            </label>
                                                        </div>

                                                        @if(!empty($section->children))
                                                            <div class="card-body">

                                                                @foreach($section->children as $key => $child)
                                                                    <div class="form-check mt-1">
                                                                        <input type="checkbox" name="permissions[]" id="permissions_{{ $child->id }}" value="{{ $child->id }}"
                                                                               {{ isset($permissions[$child->id]) ? 'checked' : '' }} class="form-check-input section-child">
                                                                        <label class="form-check-label cursor-pointer mt-0" for="permissions_{{ $child->id }}">
                                                                            {{ $child->caption }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                <div class=" mt-4">
                                    <button class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/roles.min.js"></script>
@endpush
