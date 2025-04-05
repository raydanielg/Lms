<div class="tab-pane mt-3 fade {{ (request()->get('tab') == "loginHistory") ? 'active show' : '' }}" id="loginHistory" role="tabpanel" aria-labelledby="loginHistory-tab">
    <div class="row">

        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="section-title after-line m-0 flex-1 mr-10">{{ trans('update.login_history') }}</h5>

                @can('admin_user_login_history_end_session')
                    @include('admin.includes.delete_button',[
                        'url' => getAdminPanelUrl("/users/{$user->id}/end-all-login-sessions"),
                        'noBtnTransparent' => true,
                        'btnText' => trans('update.end_all_sessions'),
                        'btnClass' => "btn btn-primary text-white"
                       ])
                @endcan
            </div>

            <div class="table-responsive mt-5">
                <table class="table table-striped font-14">
                    <tr>
                        <th>{{ trans('update.os') }}</th>
                        <th>{{ trans('update.browser') }}</th>
                        <th>{{ trans('update.device') }}</th>
                        <th>{{ trans('update.ip_address') }}</th>
                        <th>{{ trans('update.country') }}</th>
                        <th>{{ trans('update.city') }}</th>
                        <th>{{ trans('update.lat,long') }}</th>
                        <th>{{ trans('update.session_start') }}</th>
                        <th>{{ trans('update.session_end') }}</th>
                        <th>{{ trans('public.duration') }}</th>
                        <th width="120">{{ trans('admin/main.actions') }}</th>
                    </tr>

                    @if(!empty($userLoginHistories))
                        @foreach($userLoginHistories as $session)

                            <tr>
                                <td>{{ $session->os ?? '-' }}</td>

                                <td>{{ $session->browser ?? '-' }}</td>

                                <td>{{ $session->device ?? '-' }}</td>

                                <td>{{ $session->ip ?? '-' }}</td>

                                <td>{{ $session->country ?? '-' }}</td>

                                <td>{{ $session->city ?? '-' }}</td>

                                <td>{{ $session->location ?? '-' }}</td>

                                <td>{{ dateTimeFormat($session->session_start_at, 'j M Y H:i') }}</td>

                                <td>{{ !empty($session->session_end_at) ? dateTimeFormat($session->session_end_at, 'j M Y H:i') : '-' }}</td>

                                <td>{{ $session->getDuration() }}</td>

                                <td class="text-center mb-2" width="120">

                                    @can('admin_user_login_history_end_session')
                                        @if(empty($session->session_end_at))
                                            @include('admin.includes.delete_button',[
                                                'url' => getAdminPanelUrl().'/users/login-history/'.$session->id.'/end-session' ,
                                                'btnIcon' => 'fa-arrow-down',
                                                'tooltip' => trans('update.end_session')
                                               ])
                                        @endif
                                    @endcan

                                    @can('admin_user_login_history_delete')
                                        @include('admin.includes.delete_button',[
                                                'url' => getAdminPanelUrl().'/users/login-history/'.$session->id.'/delete' ,
                                               ])
                                    @endcan

                                </td>

                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>

            @if(!empty($userLoginHistories))
                <div class="card-footer text-center">
                    {{ $userLoginHistories->appends(request()->input())->links() }}
                </div>
            @endif


        </div>
    </div>
</div>
