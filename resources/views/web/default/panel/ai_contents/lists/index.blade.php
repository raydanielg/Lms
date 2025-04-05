@extends('web.default.panel.layouts.panel_layout')

@section('content')

    <section class="mt-20">
        <h2 class="section-title">{{ trans('update.generated_contents') }}</h2>

        @if(!empty($contents) and !$contents->isEmpty())

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table custom-table text-center ">
                                <thead>
                                <tr>
                                    <th>{{ trans('update.service_type') }}</th>
                                    <th class="text-center">{{ trans('update.service') }}</th>
                                    <th class="text-center">{{ trans('update.keyword') }}</th>
                                    <th class="text-center">{{ trans('auth.language') }}</th>
                                    <th class="text-center">{{ trans('update.generated_date') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($contents as $content)
                                    <tr>

                                        <td class="text-left">
                                            {{ trans($content->service_type) }}
                                        </td>

                                        <td>
                                            @if(!empty($content->template))
                                                {{ $content->template->title }}
                                            @else
                                                {{ trans('update.custom') }}
                                            @endif
                                        </td>

                                        <td>
                                            <span class="">{{ truncate($content->keyword, 100) }}</span>
                                        </td>

                                        <td>
                                            <span class="">{{ truncate($content->language, 100) }}</span>
                                        </td>

                                        <td>{{ dateTimeFormat($content->created_at, 'j F Y H:i') }}</td>

                                        <td>
                                            <input type="hidden" class="js-prompt" value="{{ $content->prompt }}">
                                            <input type="hidden" class="js-result" value="{{ $content->result }}">


                                            <a href="#" class="js-view-content btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('public.view') }}">
                                                <i data-feather="eye" width="18" height="18" class=""></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-30">
                {{ $contents->appends(request()->input())->links('vendor.pagination.panel') }}
            </div>

        @else
            @include(getTemplate() . '.includes.no-result',[
                'file_name' => 'comment.png',
                'title' => trans('update.ai_contents_no_result'),
                'hint' =>  nl2br(trans('update.ai_contents_no_result_hint')) ,
            ])
        @endif
    </section>

    <!-- Modal -->
    <div class="modal fade" id="contentModal" tabindex="-1" aria-labelledby="contactMessageLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactMessageLabel">{{ trans('update.generated_content') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="js-prompt-card">
                        <h6 class="font-weight-bold font-14">{{ trans('update.prompt') }}:</h6>
                        <p class="text-gray font-12"></p>
                    </div>

                    {{-- Text Generated --}}
                    <div class="js-text-generated-modal mt-20 p-15 bg-info-light border-gray300 rounded-sm d-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="font-weight-bold font-14 text-gray">{{ trans('update.generated_content') }}</h4>

                            <div class="form-group mb-0">
                                <button type="button" class="btn-transparent d-flex align-items-center js-copy-content-modal" data-toggle="tooltip" data-placement="top" title="{{ trans('public.copy') }}" data-copy-text="{{ trans('public.copy') }}" data-done-text="{{ trans('public.done') }}">
                                    <i data-feather="copy" width="18" height="18" class="text-gray"></i>
                                    <span class="text-gray font-12 ml-5">{{ trans('public.copy') }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-10 font-14 text-gray js-content-modal"></div>
                    </div>


                    {{-- Text Generated --}}
                    <div class="js-image-generated-modal mt-20 p-15 bg-info-light border-gray300 rounded-sm d-none">
                        <div class="">
                            <h4 class="font-weight-bold font-14 text-gray">{{ trans('update.generated_content') }}</h4>
                            <p class="mt-5 text-gray font-12">{{ trans('update.click_on_images_to_download_them') }}</p>
                        </div>

                        <div class="js-content-modal mt-10 d-flex-center flex-wrap">

                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ trans('admin/main.close') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/admin/ai_contents_lists.min.js"></script>
@endpush
