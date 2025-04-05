@extends(getTemplate() .'.panel.layouts.panel_layout')

@push('styles_top')

@endpush

@section('content')

    <section>
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="section-title">{{ trans('update.course_notes') }}</h2>
        </div>

        @if(!empty($personalNotes) and !$personalNotes->isEmpty())

            <div class="panel-section-card py-20 px-25 mt-20">
                <div class="row">
                    <div class="col-12 ">
                        <div class="table-responsive">
                            <table class="table custom-table text-center ">
                                <thead>
                                <tr>
                                    <th class="text-left">{{ trans('product.course') }}</th>
                                    <th class="text-left">{{ trans('public.file') }}</th>
                                    <th class="text-center">{{ trans('update.note') }}</th>

                                    @if(!empty(getFeaturesSettings('course_notes_attachment')))
                                        <th class="text-center">{{ trans('update.attachment') }}</th>
                                    @endif

                                    <th class="text-center">{{ trans('public.date') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($personalNotes as $personalNote)
                                    @php
                                        $item = $personalNote->getItem();
                                    @endphp

                                    <tr>
                                        <th class="text-left">
                                            <span class="d-block">{{ $personalNote->course->title }}</span>
                                            <span class="d-block font-12 text-gray mt-5">{{ trans('public.by') }} {{ $personalNote->course->teacher->full_name }}</span>
                                        </th>

                                        <th class="text-left">
                                            @if(!empty($item))
                                                <span class="d-block">{{ $item->title }}</span>

                                                @if(!empty($item->chapter))
                                                    <span class="d-block font-12 text-gray mt-5">{{ trans('public.chapter') }}: {{ $item->chapter->title }}</span>
                                                @else
                                                    -
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </th>

                                        <td class=" text-center">
                                            <input type="hidden" value="{{ $personalNote->note }}">
                                            <button type="button" class="js-show-note btn btn-sm btn-gray200">{{ trans('public.view') }}</button>
                                        </td>

                                        @if(!empty(getFeaturesSettings('course_notes_attachment')))
                                            <td class="align-middle">
                                                @if(!empty($personalNote->attachment))
                                                    <a href="/course/personal-notes/{{ $personalNote->id }}/download-attachment" class="btn btn-sm btn-gray200">{{ trans('home.download') }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endif

                                        <td class="align-middle">{{ dateTimeFormat($personalNote->created_at,'j M Y | H:i') }}</td>

                                        <td class="align-middle text-right">

                                            <div class="btn-group dropdown table-actions">
                                                <button type="button" class="btn-transparent dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i data-feather="more-vertical" height="20"></i>
                                                </button>
                                                <div class="dropdown-menu">

                                                    <a href="{{ "{$personalNote->course->getLearningPageUrl()}?type={$personalNote->getItemType()}&item={$personalNote->targetable_id}" }}" target="_blank" class="d-block text-left btn btn-sm btn-transparent">{{ trans('public.view') }}</a>

                                                    <a href="/panel/webinars/personal-notes/{{ $personalNote->id }}/delete" class="delete-action d-block text-left btn btn-sm btn-transparent">{{ trans('public.delete') }}</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="no-result my-50 d-flex align-items-center justify-content-center flex-column">
                <div class="no-result-logo">
                    <img src="/assets/default/img/no-results/personal_note.png" alt="{{ trans('update.no_notes') }}">
                </div>
                <div class="d-flex align-items-center flex-column mt-0 text-center">
                    <h2>{{ trans('update.no_notes') }}</h2>
                    <p class="mt-5 text-center">{{ trans("update.you_haven't_submitted_notes_for_your_courses") }}</p>
                </div>
            </div>
        @endif

    </section>

    <div class="my-30">
        {{ $personalNotes->appends(request()->input())->links('vendor.pagination.panel') }}
    </div>
@endsection

@push('scripts_bottom')
    <script>
        var noteLang = '{{ trans('update.note') }}';
    </script>

    <script src="/assets/default/js/panel/personal_note.min.js"></script>
@endpush
