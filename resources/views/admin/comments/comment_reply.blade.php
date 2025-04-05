@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.reply_comment') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.reply_comment') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header flex-column align-items-start">
                            <h4>{{ trans('admin/main.main_comment') }}</h4>
                            <p class="mt-2">{{ nl2br($comment->comment) }}</p>

                            <hr class="divider my-2 w-100 border border-gray">

                            @if(!empty($comment->replies) and $comment->replies->count() > 0)
                                <div class="mt-1 w-100">
                                    <h4>{{ trans('admin/main.reply_list') }}</h4>

                                    <div class="table-responsive">
                                        <table class="table table-striped font-14">
                                            <tr>
                                                <th>{{ trans('admin/main.user') }}</th>
                                                <th>{{ trans('admin/main.comment') }}</th>
                                                <th>{{ trans('public.date') }}</th>
                                                <th>{{ trans('admin/main.status') }}</th>
                                                <th>{{ trans('admin/main.type') }}</th>
                                                <th>{{ trans('admin/main.action') }}</th>
                                            </tr>
                                            @foreach($comment->replies as $reply)
                                                <tr>
                                                    <td>{{ $reply->user->id .' - '.$reply->user->full_name }}</td>
                                                    <td>
                                                        <button type="button" class="js-show-description btn btn-outline-primary">{{ trans('admin/main.show') }}</button>
                                                        <input type="hidden" value="{{ nl2br($reply->comment) }}">
                                                    </td>
                                                    <td>{{ dateTimeFormat($reply->created_at, 'Y M j | H:i') }}</td>
                                                    <td>
                                                        <span class="text-{{ ($reply->status == 'pending') ? 'warning' : 'success' }}">
                                                            {{ ($reply->status == 'pending') ? trans('admin/main.pending') : trans('admin/main.published') }}
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <span class="text-{{ (empty($reply->reply_id)) ? 'info' : 'warning' }}">
                                                            {{ (empty($reply->reply_id)) ? trans('admin/main.main_comment') : trans('admin/main.replied') }}
                                                        </span>
                                                    </td>

                                                    <td>

                                                        @can('admin_comments_status')
                                                            <a href="{{ getAdminPanelUrl() }}/comments/{{ $page }}/{{ $reply->id }}/toggle" class="btn-transparent text-primary">
                                                                @if($reply->status == 'pending')
                                                                    <i class="fa fa-arrow-up"></i>
                                                                @else
                                                                    <i class="fa fa-arrow-down"></i>
                                                                @endif
                                                            </a>
                                                        @endcan

                                                        @can('admin_comments_edit')
                                                            <a href="{{ getAdminPanelUrl() }}/comments/{{ $page }}/{{ $reply->id }}/edit" class="btn-transparent text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.edit') }}">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @endcan

                                                        @can('admin_comments_delete')
                                                            @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/comments/'. $page .'/'.$reply->id.'/delete','btnClass' => 'btn-sm mt-2'])
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="card-body ">
                            <form action="{{ getAdminPanelUrl() }}/comments/{{ $page }}/{{ $comment->id }}/reply" method="post">
                                {{ csrf_field() }}

                                <div class="form-group mt-15">
                                    <label class="input-label">{{ trans('admin/main.reply_comment') }}</label>
                                    <textarea id="summernote" name="comment" class="summernote form-control @error('comment')  is-invalid @enderror">{!! old('comment')  !!}</textarea>

                                    @error('comment')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <button type="submit" class="mt-3 btn btn-primary">{{ trans('admin/main.save_change') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="contactMessage" tabindex="-1" aria-labelledby="contactMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactMessageLabel">{{ trans('admin/main.comment') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts_bottom')
    <script src="/assets/vendors/summernote/summernote-bs4.min.js"></script>
    <script src="/assets/default/js/admin/comments.min.js"></script>
@endpush
