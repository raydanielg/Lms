@extends('admin.layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.comments_reports') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{trans('admin/main.dashboard')}}</a>
                </div>
                <div class="breadcrumb-item">{{ trans('admin/main.comments_reports') }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <tr>
                                        <th>{{ trans('admin/main.user') }}</th>
                                        <th class="text-left">{{ trans('admin/main.post_or_webinar') }}</th>
                                        <th>{{ trans('site.message') }}</th>
                                        <th>{{ trans('public.date') }}</th>
                                        <th>{{ trans('admin/main.action') }}</th>
                                    </tr>
                                    @foreach($reports as $report)
                                        <tr>
                                            <td>{{ $report->user->id .' - '.$report->user->full_name }}</td>
                                            <td class="text-left" width="20%">
                                                <a href="{{ $report->$itemRelation->getUrl() }}" target="_blank">
                                                    {{ $report->$itemRelation->title }}
                                                </a>
                                            </td>
                                            <td width="25%">{{ nl2br($report->message) }}</td>
                                            <td>{{ dateTimeFormat($report->created_at, 'Y M j | H:i') }}</td>

                                            <td width="150px">


                                                <a href="{{ getAdminPanelUrl() }}/comments/{{ $page }}/{{ $report->comment_id }}/edit" class="btn-transparent  text-primary" data-toggle="tooltip" data-placement="top" title="{{ trans('admin/main.show') }}">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                @include('admin.includes.delete_button',['url' => getAdminPanelUrl().'/comments/'. $page .'/reports/'.$report->id.'/delete','btnClass' => ''])

                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{ $reports->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

