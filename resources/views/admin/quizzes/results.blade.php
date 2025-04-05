@extends('admin.layouts.app')

@push('libraries_top')

@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.quiz_results') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.quiz_results') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            @can('admin_quiz_result_export_excel')
                                <div class="text-right">
                                    <a href="{{ getAdminPanelUrl() }}/quizzes/{{ $quiz_id}}/results/excel" class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                                </div>
                            @endcan
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th class="text-left">{{ trans('admin/main.title') }}</th>
                                        <th class="text-left">{{ trans('quiz.student') }}</th>
                                        <th class="text-left">{{ trans('admin/main.instructor') }}</th>
                                        <th class="text-center">{{ trans('admin/main.grade') }}</th>
                                        <th class="text-center">{{ trans('admin/main.quiz_date') }}</th>
                                        <th class="text-center">{{ trans('admin/main.status') }}</th>
                                        <th>{{ trans('admin/main.actions') }}</th>
                                    </tr>

                                    @foreach($quizzesResults as $result)
                                        <tr>
                                            <td>
                                                <span>{{ $result->quiz->title }}</span>
                                                <small class="d-block text-left text-primary">({{ $result->quiz->webinar->title }})</small>
                                            </td>
                                            <td class="text-left">{{ $result->user->full_name }}</td>
                                            <td class="text-left">
                                                {{ $result->quiz->teacher->full_name }}
                                            </td>
                                            <td class="text-center">
                                                <span>{{ $result->user_grade }}</span>
                                            </td>
                                            <td class="text-center">{{ dateTimeformat($result->created_at, 'j F Y') }}</td>
                                            <td class="text-center">
                                                @switch($result->status)
                                                    @case(\App\Models\QuizzesResult::$passed)
                                                    <span class="text-success">{{ trans('quiz.passed') }}</span>
                                                    @break

                                                    @case(\App\Models\QuizzesResult::$failed)

                                                    <span class="text-danger">{{ trans('quiz.failed') }}</span>
                                                    @break

                                                    @case(\App\Models\QuizzesResult::$waiting)
                                                    <span class="text-warning">{{ trans('quiz.waiting') }}</span>
                                                    @break

                                                @endswitch
                                            </td>

                                            <td>
                                                @can('admin_quizzes_results_delete')
                                                    @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/quizzes/result/'. $result->id.'/delete'])
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach

                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $quizzesResults->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')

@endpush
