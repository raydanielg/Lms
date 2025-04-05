@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')
    <section>
        <h2 class="section-title">{{ trans('panel.support_summary') }}</h2>

        <div class="activities-container mt-25 p-20 p-lg-35">
            <div class="row">
                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/41.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $openSupportsCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.open_conversations') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/40.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $closeSupportsCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.closed_conversations') }}</span>
                    </div>
                </div>

                <div class="col-4 d-flex align-items-center justify-content-center">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="/assets/default/img/activity/39.svg" width="64" height="64" alt="">
                        <strong class="font-30 text-dark-blue font-weight-bold mt-5">{{ $supportsCount }}</strong>
                        <span class="font-16 text-gray font-weight-500">{{ trans('panel.total_conversations') }}</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="mt-25">
        <h2 class="section-title">{{ trans('panel.message_filters') }}</h2>

        <div class="panel-section-card py-20 px-25 mt-20">
            <form action="/panel/support/tickets" method="get">
                <div class="row">
                    <div class="col-12 col-lg-5">
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
                                        <input type="text" name="from" autocomplete="off" class="form-control @if(!empty(request()->get('from'))) datepicker @else datefilter @endif" aria-describedby="dateInputGroupPrepend" value="{{ request()->get('from','') }}"/>
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
                                        <input type="text" name="to" autocomplete="off" class="form-control @if(!empty(request()->get('to'))) datepicker @else datefilter @endif" aria-describedby="dateInputGroupPrepend" value="{{ request()->get('to','') }}"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="input-label d-block">{{ trans('panel.department') }}</label>

                                    <select name="department" id="departments" class="form-control">
                                        <option value="all">{{ trans('public.all') }}</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" @if(request()->get('department') == $department->id) selected @endif>{{ $department->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="input-label">{{ trans('public.status') }}</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="all">{{ trans('public.all') }}</option>
                                        <option value="open" @if(request()->get('status') == 'open') selected @endif >{{ trans('public.open') }}</option>
                                        <option value="close" @if(request()->get('status') == 'close') selected @endif >{{ trans('public.close') }}</option>
                                        <option value="replied" @if(request()->get('status') == 'replied') selected @endif >{{ trans('panel.replied') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-2 d-flex align-items-center justify-content-end">
                        <button type="submit" class="btn btn-sm font-14 btn-primary w-100 mt-2">{{ trans('public.show_results') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="mt-40">
        <h2 class="section-title">{{ trans('panel.messages_history') }}</h2>

        @if(!empty($supports) and !$supports->isEmpty())

            <div class="bg-white shadow rounded-sm py-10 py-lg-25 px-15 px-lg-30 mt-25">
                <div class="row">
                    <div id="conversationsList" class="col-12 col-lg-6 conversations-list">
                        <div class="table-responsive">
                            <table class="table table-md">
                                <tr>
                                    <th class="text-left font-14 text-gray font-weight-500">{{ trans('navbar.title') }}</th>
                                    <th class="text-center font-14 text-gray font-weight-500">{{ trans('public.updated_at') }}</th>
                                    <th class="text-center font-14 text-gray font-weight-500">{{ trans('panel.department') }}</th>
                                    <th class="text-center font-14 text-gray font-weight-500">{{ trans('public.status') }}</th>
                                </tr>
                                <tbody>

                                @foreach($supports as $support)
                                    <tr class="@if(!empty($selectSupport) and $selectSupport->id == $support->id) selected-row @endif">
                                        <td class="text-left">
                                            <a href="/panel/support/tickets/{{ $support->id }}/conversations" class="">
                                                <div class="user-inline-avatar d-flex align-items-center">
                                                    <div class="avatar bg-gray200">
                                                        <img src="/assets/default/img/support.png" class="img-cover" alt="">
                                                    </div>
                                                    <div class="ml-10">
                                                        <span class="d-block font-14 text-dark-blue font-weight-500">{{ $support->title }}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>

                                        <td class="text-center align-middle">
                                            <span class="font-weight-500 text-dark-blue font-14 text-gray d-block">{{ (!empty($support->conversations) and count($support->conversations)) ? dateTimeFormat($support->conversations->first()->created_at,'j M Y | H:i') : dateTimeFormat($support->created_at,'j M Y | H:i') }}</span>
                                        </td>

                                        <td class="text-center align-middle">
                                            <span class="font-weight-500 text-dark-blue font-14 d-block">{{ $support->department->title }}</span>
                                        </td>

                                        <td class="text-center align-middle">
                                            @if($support->status == 'close')
                                                <span class="text-danger font-14 font-weight-500">{{  trans('panel.closed') }}</span>
                                            @elseif($support->status == 'supporter_replied')
                                                <span class="text-primary font-14 font-weight-500">{{  trans('panel.replied') }}</span>
                                            @else
                                                <span class="text-warning font-14 font-weight-500">{{  trans('public.waiting') }}</span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(!empty($selectSupport))
                        <div class="col-12 col-lg-6 border-left border-gray300">
                            <div class="conversation-box p-15 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="font-weight-500 font-14 text-dark-blue d-block">{{ $selectSupport->title }}</span>
                                    <span class="font-12 text-gray d-block mt-5">{{ trans('public.created') }}: {{ dateTimeFormat($support->created_at,'j M Y | H:i') }}</span>

                                    @if(!empty($selectSupport->webinar))
                                        <span class="font-12 text-gray d-block mt-5">{{ trans('webinars.webinar') }}: {{ $selectSupport->webinar->title }}</span>
                                    @endif
                                </div>

                                @if($selectSupport->status != 'close')
                                    <a href="/panel/support/{{ $selectSupport->id }}/close" class="btn btn-primary btn-sm">{{ trans('panel.close_request') }}</a>
                                @endif
                            </div>

                            <div id="conversationsCard" class="pt-15 conversations-card">

                                @if(!empty($selectSupport->conversations) and !$selectSupport->conversations->isEmpty())

                                    @foreach($selectSupport->conversations as $conversations)
                                        <div class="rounded-sm mt-15 border panel-shadow p-15">
                                            <div class="d-flex align-items-center justify-content-between pb-20 border-bottom border-gray300">
                                                <div class="user-inline-avatar d-flex align-items-center">
                                                    <div class="avatar bg-gray200">
                                                        <img src="{{ (!empty($conversations->supporter)) ? $conversations->supporter->getAvatar() : $conversations->sender->getAvatar() }}" class="img-cover" alt="">
                                                    </div>
                                                    <div class="ml-10">
                                                        <span class="d-block text-dark-blue font-14 font-weight-500">{{ (!empty($conversations->supporter)) ? $conversations->supporter->full_name : $conversations->sender->full_name }}</span>
                                                        <span class="mt-1 font-12 text-gray d-block">{{ (!empty($conversations->supporter)) ? trans('panel.staff') : $conversations->sender->role_name }}</span>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-column align-items-end">
                                                    <span class="font-12 text-gray">{{ dateTimeFormat($conversations->created_at,'j M Y | H:i') }}</span>

                                                    @if(!empty($conversations->attach))
                                                        <a href="{{ url($conversations->attach) }}" target="_blank" class="font-12 mt-10 text-danger"><i data-feather="paperclip" height="14"></i> {{ trans('panel.attach') }}</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="white-space-pre-wrap text-gray mt-15 font-weight-500 font-14">{{ $conversations->message }}</p>
                                        </div>
                                    @endforeach

                                @endif
                            </div>

                            <div class="conversation-box mt-30 py-10 px-15">
                                <h3 class="font-14 text-dark-blue font-weight-bold">{{ trans('panel.reply_to_the_conversation') }}</h3>
                                <form action="/panel/support/{{ $selectSupport->id }}/conversations" method="post" class="mt-5">
                                    {{ csrf_field() }}

                                    <div class="form-group mt-10">
                                        <label class="input-label d-block">{{ trans('site.message') }}</label>
                                        <textarea name="message" class="form-control @error('message')  is-invalid @enderror" rows="5">{{ old('message') }}</textarea>
                                        @error('message')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="d-flex d-flex align-items-center">
                                        <div class="form-group">
                                            <label class="input-label">{{ trans('panel.attach_file') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text panel-file-manager" data-input="attach" data-preview="holder">
                                                        <i data-feather="arrow-up" width="18" height="18" class="text-white"></i>
                                                    </button>
                                                </div>
                                                <input type="text" name="attach" id="attach" value="{{ old('attach') }}" class="form-control"/>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-sm ml-40 mt-10">{{ trans('site.send_message') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="col-12 col-lg-6 border-left border-gray300">
                            @include(getTemplate() . '.includes.no-result',[
                                'file_name' => 'support.png',
                                'title' => trans('panel.select_support'),
                                'hint' => nl2br(trans('panel.select_support_hint')),
                            ])
                        </div>
                    @endif
                </div>
            </div>

        @else

            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'support.png',
                'title' => trans('panel.support_no_result'),
                'hint' => nl2br(trans('panel.support_no_result_hint')),
            ])

        @endif
    </section>


@endsection


@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/assets/default/vendors/select2/select2.min.js"></script>

    <script src="/assets/default/js/panel/conversations.min.js"></script>
@endpush
