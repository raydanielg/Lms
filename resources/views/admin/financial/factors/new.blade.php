@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.new_factor') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.new_factor') }}</div>
            </div>
        </div>


        <div class="section-body">

            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h2 class="section-title">{{ trans('admin/main.create') }}</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-7">
                    <div class="card">
                        <div class="card-body">

                            <form action="{{ getAdminPanelUrl() }}/documents/new" method="post">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label class="control-label">{{ trans('admin/main.title') }}</label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror">

                                    @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>


                                <div class="form-group">
                                    <label class="control-label">{{ trans('admin/main.amount') }}({{ $currency }})</label>
                                    <input type="text" name="amount" class="form-control @error('amount') is-invalid @enderror" placeholder="100">

                                    @error('amount')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>


                                <div class="form-group">
                                    <label class="input-label d-block">{{ trans('admin/main.user') }}</label>
                                    <select name="user" class="form-control search-user-select2" data-placeholder="{{ trans('public.search_user') }}">

                                    </select>

                                    @error('user')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{ trans('public.description') }}</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6"></textarea>
                                    @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-success">{{ trans('admin/main.submit') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

