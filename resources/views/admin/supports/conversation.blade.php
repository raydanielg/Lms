@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <div class="tickets-list">
                <a class="ticket-item">
                    <div class="ticket-title">
                        <h4 class="text-primary">{{ $support->title }}</h4>
                    </div>
                    <div class="ticket-info">
                        <div class="font-weight-bold">{{ $support->user->full_name }}</div>
                        <div class="bullet"></div>
                        <div class="font-weight-bold">
                            @if($support->status == 'open')
                                <span class="text-success">{{ trans('admin/main.open') }}</span>
                            @elseif($support->status == 'close')
                                <span class="text-danger">{{ trans('admin/main.close') }}</span>
                            @elseif($support->status == 'replied')
                                <span class="text-warning">{{ trans('admin/main.pending_reply') }}</span>
                            @else
                                <span class="text-primary">{{ trans('admin/main.replied') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            </div>

            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a></div>
                <div class="breadcrumb-item">{{ trans('admin/main.conversation') }}</div>
            </div>
        </div>


        <div class="section-body">

            <div class="row">
                <div class="col-12 ">
                    <div class="card chat-box" id="mychatbox2">

                        <div class="card-body chat-content">

                            @foreach($support->conversations as $conversations)
                                <div class="chat-item chat-{{ !empty($conversations->sender_id) ? 'right' : 'left' }}">
                                    <img src="{{ !empty($conversations->sender_id) ? $conversations->sender->getAvatar() : $conversations->supporter->getAvatar() }}">

                                    <div class="chat-details">

                                        <div class="chat-time">{{ !empty($conversations->sender_id) ? $conversations->sender->full_name : $conversations->supporter->full_name }}</div>

                                        <div class="chat-text white-space-pre-wrap">{{ $conversations->message }}</div>
                                        <div class="chat-time">
                                            <span class="mr-2">{{ dateTimeFormat($conversations->created_at,'Y M j | H:i') }}</span>

                                            @if(!empty($conversations->attach))
                                                <a href="{{ url($conversations->attach) }}" target="_blank" class="text-success"><i class="fa fa-paperclip"></i> {{ trans('admin/main.open_attach') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 ">
                    <div class="card">

                        <div class="card-body">
                            <form action="{{ getAdminPanelUrl() }}/supports/{{ $support->id }}/conversation" method="post">
                                {{ csrf_field() }}

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('site.message') }}</label>
                                    <textarea name="message" rows="6" class=" form-control @error('message')  is-invalid @enderror">{!! old('message')  !!}</textarea>
                                    @error('message')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <label class="input-label">{{ trans('admin/main.attach') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="input-group-text admin-file-manager" data-input="attach" data-preview="holder">
                                                        Browse
                                                    </button>
                                                </div>
                                                <input type="text" name="attach" id="attach" value="{{ old('image_cover') }}" class="form-control"/>
                                                <div class="input-group-append">
                                                    <button type="button" class="input-group-text admin-file-view" data-input="attach">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4 text-right mt-4">
                                        <button type="submit" class="btn btn-primary">{{ trans('site.send_message') }}</button>

                                        @if($support->status != 'close')
                                            <a href="{{ getAdminPanelUrl() }}/supports/{{ $support->id }}/close" class="btn btn-danger ml-1">{{ trans('admin/main.close_conversation') }}</a>
                                        @endif
                                    </div>
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
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>

@endpush
