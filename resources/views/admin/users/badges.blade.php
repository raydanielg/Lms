@extends('admin.layouts.app')


@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.badges') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}/users">{{ trans('admin/main.users') }}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.badges') }}</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">{{ !empty($badge) ? trans('/admin/main.edit') : trans('admin/main.create') }}</h2>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            @if(empty($badge))

                                <ul class="nav nav-pills" id="myTab3" role="tablist">
                                    @foreach(\App\Models\Badge::$badgeTypes as $type)
                                        <li class="nav-item">
                                            <a class="nav-link {{ $loop->iteration == 1 ? 'active' : '' }}" id="{{ $type }}-tab" data-toggle="tab" href="#{{ $type }}" role="tab" aria-controls="{{ $type }}" aria-selected="true">{{ trans('admin/main.'.$type) }}</a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content" id="myTabContent2">
                                    @foreach(\App\Models\Badge::$badgeTypes as $type)
                                        <div class="tab-pane mt-3 fade {{ $loop->iteration == 1 ? 'show active' : '' }}" id="{{ $type }}" role="tabpanel" aria-labelledby="{{ $type }}-tab">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <form action="{{ getAdminPanelUrl() }}/users/badges/store" method="post">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="type" value="{{ $type }}">

                                                        @if(!empty(getGeneralSettings('content_translate')))
                                                            <div class="form-group">
                                                                <label class="input-label">{{ trans('auth.language') }}</label>
                                                                <select name="locale" class="form-control">
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

                                                        <div class="form-group">
                                                            <label>{{ trans('admin/main.title') }}</label>
                                                            <input type="text" name="title" value="{{ old('title') }}" class="form-control  @error('title') is-invalid @enderror"/>
                                                            @error('title')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="input-label">{{ trans('admin/main.image') }}</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <button type="button" class="input-group-text admin-file-manager" data-input="image_{{ $type }}" data-preview="holder">
                                                                        <i class="fa fa-chevron-up"></i>
                                                                    </button>
                                                                </div>
                                                                <input type="text" name="image" id="image_{{ $type }}" value="{{ old('image') }}" class="form-control @error('image')  is-invalid @enderror"/>
                                                                @error('image')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class=" control-label">{{ trans('admin/main.condition') }}</label>

                                                            <div class="input-group">
                                                            <span class="input-group-prepend">
                                                                <span class="input-group-text">{{ trans('admin/main.from') }}</span>
                                                            </span>
                                                                <input type="number" name="condition[from]" class="form-control @error('condition.from')  is-invalid @enderror">

                                                                <span class="input-group-append">
                                                                <span class="input-group-text">{{ trans('admin/main.to') }}</span>
                                                            </span>
                                                                <input type="number" name="condition[to]" class="form-control @error('condition.from')  is-invalid @enderror">


                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">{{ trans('admin/main.condition_'.$type) }}</div>
                                                                </div>

                                                                @error('condition.from')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror

                                                                @error('condition.to')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ trans('update.score') }}</label>
                                                            <input type="number" name="score" value="{{ old('score') }}" class="form-control  @error('score') is-invalid @enderror"/>
                                                            @error('score')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{ trans('admin/main.description') }}</label>
                                                            <textarea name="description" rows="4" class="form-control  @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
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

                                            <div class="table-responsive mt-5">
                                                <table class="table table-striped font-14">
                                                    <tr>
                                                        <th>{{ trans('admin/main.title') }}</th>
                                                        <th>{{ trans('public.image') }}</th>
                                                        <th>{{ trans('admin/main.condition') }}</th>
                                                        <th>{{ trans('update.score') }}</th>
                                                        <th class="text-left">{{ trans('public.description') }}</th>
                                                        <th>{{ trans('admin/main.created_at') }}</th>
                                                        <th>{{ trans('admin/main.actions') }}</th>
                                                    </tr>

                                                    @if(!empty($badges[$type]))
                                                        @foreach($badges[$type] as $badge)
                                                            <tr>
                                                                <td>{{ $badge->title }}</td>
                                                                <td>
                                                                    <img src="{{ $badge->image }}" width="24"/>
                                                                </td>
                                                                <td>{{ $badge->condition->from }} to {{ $badge->condition->to }}</td>
                                                                <td>{{ $badge->score }}</td>
                                                                <td class="text-left" width="25%">
                                                                    <p>{{ $badge->description  }}</p>
                                                                </td>
                                                                <td>{{ dateTimeFormat($badge->created_at,'j M Y') }}</td>
                                                                <td>
                                                                    @can('admin_users_badges_edit')
                                                                        <a href="{{ getAdminPanelUrl() }}/users/badges/{{ $badge->id }}/edit" class="btn-sm" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                            <i class="fa fa-edit"></i>
                                                                        </a>
                                                                    @endcan

                                                                    @can('admin_users_badges_delete')
                                                                        @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/users/badges/'.$badge->id.'/delete' , 'btnClass' => 'btn-sm'])
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            @else
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <form action="{{ getAdminPanelUrl() }}/users/badges/{{ $badge->id }}/update" method="post">
                                            {{ csrf_field() }}

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

                                            <div class="form-group">
                                                <label>{{ trans('admin/main.title') }}</label>
                                                <input type="text" name="title" value="{{ !empty($badge) ? $badge->title : old('title') }}" class="form-control  @error('title') is-invalid @enderror"/>
                                                @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label class="input-label">{{ trans('admin/main.image') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <button type="button" class="input-group-text admin-file-manager" data-input="imageUrl" data-preview="holder">
                                                            <i class="fa fa-chevron-up"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" name="image" id="imageUrl" value="{{ !empty($badge) ? $badge->image : old('image') }}" class="form-control @error('image')  is-invalid @enderror"/>
                                                    @error('image')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class=" control-label">{{ trans('admin/main.condition') }}</label>

                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text">{{ trans('admin/main.from') }}</span>
                                                    </span>
                                                    <input type="number" name="condition[from]" class="form-control @error('condition.from')  is-invalid @enderror" value="{{ $badge->condition->from }}">

                                                    <span class="input-group-append">
                                                        <span class="input-group-text">{{ trans('admin/main.to') }}</span>
                                                    </span>
                                                    <input type="number" name="condition[to]" class="form-control @error('condition.from')  is-invalid @enderror" value="{{ $badge->condition->to }}">


                                                    <div class="input-group-append">
                                                        <div class="input-group-text">{{ trans('admin/main.condition_'.$badge->type) }}</div>
                                                    </div>

                                                    @error('condition.from')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror

                                                    @error('condition.to')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>{{ trans('update.score') }}</label>
                                                <input type="number" name="score" value="{{ !empty($badge) ? $badge->score : old('score') }}" class="form-control  @error('score') is-invalid @enderror"/>
                                                @error('score')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label>{{ trans('admin/main.description') }}</label>
                                                <textarea name="description" rows="4" class="form-control  @error('description') is-invalid @enderror">{!! nl2br(!empty($badge) ? $badge->description : old('description')) !!}</textarea>
                                                @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>

                                            <button type="submit" class="btn btn-primary">{{ trans('admin/main.submit') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3"><h5>{{trans('admin/main.hints')}}</h5></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.badges_hint_title_1')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.badges_hint_description_1')}}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">{{trans('admin/main.badges_hint_title_2')}}</div>
                        <div class=" text-small font-600-bold">{{trans('admin/main.badges_hint_description_2')}}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
